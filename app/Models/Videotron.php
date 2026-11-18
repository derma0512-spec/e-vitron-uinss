<?php

namespace App\Models;

use App\Notifications\VideotronConditionNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            return asset('storage/'.$this->image);
        }

        return $this->orientation === 'portrait'
            ? asset('images/hero_banner.jpg')
            : asset('images/campus-videotron.png');
    }

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_videotron')
            ->withPivot('playback_status')
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

        $notifiedCount = 0;
        $event = $type === 'recovered' ? 'videotron_recovered' : 'videotron_disruption';
        $batchId = (string) Str::uuid();

        foreach ($submissions as $submission) {
            $user = $submission->user;
            if (! $user) {
                continue;
            }

            $lastConditionNotification = $user->notifications()
                ->whereIn('data->event', ['videotron_disruption', 'videotron_recovered'])
                ->where('data->submission_id', $submission->id)
                ->where('data->videotron_id', $this->id)
                ->latest()
                ->first();

            if ($lastConditionNotification) {
                $lastData = $lastConditionNotification->data ?? [];
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
