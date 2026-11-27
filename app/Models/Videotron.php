<?php

namespace App\Models;

use App\Notifications\VideotronConditionNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Videotron extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'image',
        'resolution',
        'orientation',
        'operating_hours',
        'condition',
        'issue_notes',
        'estimated_recovery_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_recovery_date' => 'date',
    ];

    /**
     * In-memory runtime cache ringkasan statistik operasional videotron selama 1 request.
     *
     * @var array{total: int, troubled: int, active: int, repair: int, issue: int, inactive: int}|null
     */
    protected static ?array $runtimeOperationalStats = null;

    protected static function booted(): void
    {
        static::saving(function (Videotron $videotron) {
            // Jika kondisi diset ke gangguan atau perbaikan, otomatis nonaktifkan
            if (in_array($videotron->condition, ['gangguan', 'dalam_perbaikan'])) {
                $videotron->is_active = false;
            } elseif ($videotron->condition === 'normal') {
                // Jika kondisi normal dan beralih dari gangguan/perbaikan, bersihkan catatan dan aktifkan kembali
                if ($videotron->isDirty('condition') && ! $videotron->isDirty('is_active')) {
                    $videotron->is_active = true;
                    $videotron->issue_notes = null;
                    $videotron->estimated_recovery_date = null;
                }
            }
        });

        static::saved(function (): void {
            self::$runtimeOperationalStats = null;
        });

        static::deleted(function (): void {
            self::$runtimeOperationalStats = null;
        });
    }

    /**
     * Bersihkan in-memory runtime cache statistik operasional perangkat.
     */
    public static function clearRuntimeCache(): void
    {
        self::$runtimeOperationalStats = null;
    }

    /**
     * Hitung seluruh metrik status perangkat Videotron dalam 1 query SQL agregat,
     * disimpan di in-memory runtime cache selama siklus request agar Header dan Dashboard berbagi data.
     *
     * @return array{total: int, troubled: int, active: int, repair: int, issue: int, inactive: int}
     */
    public static function getOperationalStats(): array
    {
        if (self::$runtimeOperationalStats !== null) {
            return self::$runtimeOperationalStats;
        }

        $row = self::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN `condition` IN ('gangguan', 'dalam_perbaikan') OR is_active = 0 THEN 1 ELSE 0 END) as troubled,
                SUM(CASE WHEN is_active = 1 AND `condition` = 'normal' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN `condition` = 'dalam_perbaikan' THEN 1 ELSE 0 END) as repair,
                SUM(CASE WHEN `condition` = 'gangguan' THEN 1 ELSE 0 END) as issue,
                SUM(CASE WHEN is_active = 0 AND `condition` = 'normal' THEN 1 ELSE 0 END) as inactive
            ")
            ->first();

        return self::$runtimeOperationalStats = [
            'total' => (int) ($row->total ?? 0),
            'troubled' => (int) ($row->troubled ?? 0),
            'active' => (int) ($row->active ?? 0),
            'repair' => (int) ($row->repair ?? 0),
            'issue' => (int) ($row->issue ?? 0),
            'inactive' => (int) ($row->inactive ?? 0),
        ];
    }

    protected $appends = [
        'image_url',
        'operating_hours',
    ];

    public function getOperatingHoursAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }

        if (stripos($this->name, 'febi') !== false) {
            return '07.00 – 24.00 WIB';
        }

        return '06.00 – 22.00 WIB';
    }

    /**
     * Memeriksa apakah perangkat memiliki foto unggahan khusus yang berkas fisiknya tersedia di disk publik.
     */
    public function hasCustomImage(): bool
    {
        return ! empty($this->image) && Storage::disk('public')->exists($this->image);
    }

    /**
     * Menentukan path aset foto permanen bawaan untuk masing-masing titik videotron kampus.
     */
    public function getDefaultImagePath(): string
    {
        $name = strtolower((string) $this->name);
        $campusPhotos = [
            'masjid' => 'images/videotrons/masjid.webp',
            'rektorat' => 'images/videotrons/rektorat.webp',
            'syariah' => 'images/videotrons/syariah.webp',
            'fitk' => 'images/videotrons/fitk.webp',
            'siber' => 'images/videotrons/siber.webp',
            'febi' => 'images/videotrons/febi.webp',
        ];

        foreach ($campusPhotos as $keyword => $relativePath) {
            if (str_contains($name, $keyword) && is_file(public_path($relativePath))) {
                return $relativePath;
            }
        }

        return $this->orientation === 'portrait'
            ? 'images/hero_banner.jpg'
            : 'images/campus-videotron.png';
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->hasCustomImage()) {
            return asset('storage/'.$this->image);
        }

        return asset($this->getDefaultImagePath());
    }

    /**
     * Memformat teks resolusi secara konsisten menggunakan simbol perkalian (contoh: 960 × 480).
     */
    public function formattedResolution(): string
    {
        $raw = trim((string) $this->resolution);

        if (preg_match('/(\d+)\s*[xX×]\s*(\d+)/u', $raw, $matches) === 1) {
            return ((int) $matches[1]).' × '.((int) $matches[2]);
        }

        return $raw;
    }

    /**
     * Menghitung rasio aspek aktual berdasarkan resolusi piksel layar (menggunakan FPB/GCD).
     */
    public function aspectRatioLabel(): ?string
    {
        if (preg_match('/(\d+)\s*[xX×]\s*(\d+)/u', (string) $this->resolution, $matches) !== 1) {
            return null;
        }

        $width = (int) $matches[1];
        $height = (int) $matches[2];

        if ($width <= 0 || $height <= 0) {
            return null;
        }

        $gcd = self::greatestCommonDivisor($width, $height);
        $ratioWidth = intdiv($width, $gcd);
        $ratioHeight = intdiv($height, $gcd);

        if ($ratioWidth <= 24 && $ratioHeight <= 24) {
            return "{$ratioWidth}:{$ratioHeight}";
        }

        if ($width >= $height) {
            $decimal = rtrim(rtrim(number_format($width / $height, 2, '.', ''), '0'), '.');

            return "{$decimal}:1";
        }

        $decimal = rtrim(rtrim(number_format($height / $width, 2, '.', ''), '0'), '.');

        return "1:{$decimal}";
    }

    /**
     * Menggabungkan orientasi layar dengan rasio aspek hasil perhitungan resolusi (contoh: Landscape (7:2)).
     */
    public function orientationWithRatioLabel(): string
    {
        $orientation = match (strtolower(trim((string) $this->orientation))) {
            'landscape' => 'Landscape',
            'portrait' => 'Portrait',
            default => ucfirst(trim((string) $this->orientation)),
        };

        $ratio = $this->aspectRatioLabel();

        return $ratio !== null ? "{$orientation} ({$ratio})" : $orientation;
    }

    private static function greatestCommonDivisor(int $a, int $b): int
    {
        while ($b !== 0) {
            $remainder = $a % $b;
            $a = $b;
            $b = $remainder;
        }

        return $a;
    }

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_videotron')
            ->withPivot(['playback_status', 'taken_down_at', 'taken_down_by'])
            ->withTimestamps();
    }

    /**
     * Kirim notifikasi otomatis ke semua pengaju yang memilih videotron ini
     * pada pengajuan aktif (menunggu atau disetujui, dan belum kedaluwarsa).
     *
     * Anti-spam berbasis perubahan status: notifikasi hanya dilewati jika notifikasi
     * kondisi terakhir untuk pasangan pengajuan + videotron yang sama memiliki event
     * dan kondisi yang identik (misal admin hanya mengoreksi catatan kendala).
     */
    public function notifyAffectedSubmissions(string $type = 'disruption'): int
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $submissions = $this->submissions()
            ->with(['user', 'videotrons'])
            ->whereIn('submissions.status', ['menunggu', 'disetujui'])
            ->where(function ($q) use ($today) {
                $q->whereNull('submissions.end_date')
                    ->orWhere('submissions.end_date', '>=', $today);
            })
            ->get();

        if ($submissions->isEmpty()) {
            return 0;
        }

        $userIds = $submissions->pluck('user_id')->filter()->unique()->values()->all();

        /** @var array<string, array<string, mixed>> $latestNotificationDataByPair */
        $latestNotificationDataByPair = $userIds === []
            ? []
            : DatabaseNotification::query()
                ->where('notifiable_type', User::class)
                ->whereIn('notifiable_id', $userIds)
                ->whereIn('data->event', ['videotron_disruption', 'videotron_recovered'])
                ->where('data->videotron_id', $this->id)
                ->latest()
                ->get(['notifiable_id', 'data', 'created_at'])
                ->reduce(function (array $carry, DatabaseNotification $notification): array {
                    $data = is_array($notification->data) ? $notification->data : [];
                    $submissionId = $data['submission_id'] ?? null;

                    if ($submissionId === null) {
                        return $carry;
                    }

                    $pairKey = $notification->notifiable_id.':'.(int) $submissionId;
                    if (! array_key_exists($pairKey, $carry)) {
                        $carry[$pairKey] = $data;
                    }

                    return $carry;
                }, []);

        $notifiedCount = 0;
        $event = $type === 'recovered' ? 'videotron_recovered' : 'videotron_disruption';
        $batchId = (string) Str::uuid();

        foreach ($submissions as $submission) {
            $user = $submission->user;
            if (! $user) {
                continue;
            }

            $pairKey = $user->id.':'.(int) $submission->id;
            $lastData = $latestNotificationDataByPair[$pairKey] ?? null;

            if ($lastData !== null) {
                $isSameEvent = ($lastData['event'] ?? null) === $event;
                $isSameCondition = ($lastData['videotron_condition'] ?? null) === $this->condition;

                if ($isSameEvent && ($event === 'videotron_recovered' || $isSameCondition)) {
                    continue;
                }
            }

            $user->notify(new VideotronConditionNotification($this, $submission, $type, $batchId));
            $notifiedCount++;
        }

        return $notifiedCount;
    }
}
