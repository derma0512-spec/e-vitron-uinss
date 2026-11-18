<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'title',
        'content_type',
        'description',
        'pic_name',
        'organization',
        'whatsapp',
        'gdrive_link',
        'surat_peminjaman',
        'video_duration_seconds',
        'start_date',
        'end_date',
        'status',
        'admin_notes',
        'material_downloaded_at',
        'material_downloaded_by',
        'created_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'material_downloaded_at' => 'datetime',
    ];

    /**
     * In-memory runtime cache untuk jumlah per display_status selama 1 request,
     * otomatis di-reset ketika ada Submission yang disimpan atau dihapus.
     *
     * @var array<string, array{menunggu: int, revisi: int, terjadwal: int, tayang: int, selesai: int, ditolak: int, dibatalkan: int}>
     */
    protected static array $runtimeStatusCounts = [];

    protected static function booted(): void
    {
        static::saved(function (): void {
            self::$runtimeStatusCounts = [];
        });

        static::deleted(function (): void {
            self::$runtimeStatusCounts = [];
        });
    }

    /**
     * Bersihkan in-memory runtime cache perhitungan status.
     */
    public static function clearRuntimeCache(): void
    {
        self::$runtimeStatusCounts = [];
    }

    /**
     * URL terproteksi untuk berkas surat peminjaman (PDF)
     */
    public function getSuratPeminjamanUrlAttribute(): ?string
    {
        if (! $this->surat_peminjaman) {
            return null;
        }

        if (str_starts_with($this->surat_peminjaman, 'http://') || str_starts_with($this->surat_peminjaman, 'https://')) {
            return $this->surat_peminjaman;
        }

        if ($this->ticket_code) {
            return route('pengajuan.surat', ['ticket_code' => $this->ticket_code]);
        }

        return asset('storage/'.ltrim($this->surat_peminjaman, '/'));
    }

    /**
     * Nama berkas resmi / terstandarisasi dari surat peminjaman
     */
    public function getSuratPeminjamanFilenameAttribute(): ?string
    {
        if (! $this->surat_peminjaman) {
            return null;
        }

        $ticket = $this->ticket_code ? preg_replace('/[^A-Za-z0-9\-]/', '', (string) $this->ticket_code) : 'DOKUMEN';

        return 'Surat-Permohonan-'.$ticket.'.pdf';
    }

    /**
     * Status materi hanya berlaku untuk pengajuan yang sudah disetujui/selesai.
     */
    public function isMaterialDownloaded(): bool
    {
        return $this->material_downloaded_at !== null;
    }

    public function materialDownloader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'material_downloaded_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videotrons(): BelongsToMany
    {
        return $this->belongsToMany(Videotron::class, 'submission_videotron')
            ->withPivot('playback_status')
            ->withTimestamps();
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(SubmissionRevision::class)->latest();
    }

    public function latestRevision(): HasOne
    {
        return $this->hasOne(SubmissionRevision::class)->latestOfMany();
    }

    /**
     * Mengembalikan tipe revisi efektif ('surat', 'materi', 'keduanya')
     * dengan fallback cerdas berbasis kata kunci catatan admin jika belum ada record histori revisi.
     */
    public function getEffectiveRevisionTypeAttribute(): string
    {
        if ($this->latestRevision) {
            return $this->latestRevision->effective_type;
        }

        $text = strtolower((string) ($this->admin_notes ?? ''));
        $hasSurat = str_contains($text, 'surat')
            || str_contains($text, 'peminjaman')
            || str_contains($text, 'permohonan')
            || str_contains($text, 'stempel')
            || str_contains($text, 'tanda tangan')
            || str_contains($text, 'ttd')
            || str_contains($text, 'dokumen')
            || str_contains($text, 'pdf')
            || str_contains($text, 'berkas');
        $hasMateri = str_contains($text, 'drive')
            || str_contains($text, 'link')
            || str_contains($text, 'tautan')
            || str_contains($text, 'video')
            || str_contains($text, 'poster')
            || str_contains($text, 'durasi')
            || str_contains($text, 'resolusi')
            || str_contains($text, 'public')
            || str_contains($text, 'akses')
            || str_contains($text, 'materi')
            || str_contains($text, 'gdrive')
            || str_contains($text, 'konten');

        if ($hasSurat && $hasMateri) {
            return 'keduanya';
        }

        if ($hasSurat && ! $hasMateri) {
            return 'surat';
        }

        if ($hasMateri && ! $hasSurat) {
            return 'materi';
        }

        return 'keduanya';
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(SubmissionStatusHistory::class)->latest();
    }

    /**
     * Accessor: display_status
     *
     * Logika:
     * 1. Tayang/selesai ditentukan dari tanggal HANYA untuk status database 'disetujui':
     *    - today < start_date  => 'terjadwal'
     *    - start_date <= today <= end_date => 'tayang'
     *    - today > end_date    => 'selesai'
     *    Bandingkan tanggal saja, end_date inklusif, timezone Asia/Jakarta.
     *    Tanggal null -> fallback 'terjadwal'.
     * 2. Status 'selesai' yang tersimpan di database tetap dihormati.
     * 3. menunggu, revisi, ditolak, dibatalkan tidak berubah karena tanggal.
     */
    public function getDisplayStatusAttribute(): string
    {
        if (in_array($this->status, ['menunggu', 'revisi', 'ditolak', 'dibatalkan', 'selesai'], true)) {
            return $this->status;
        }

        if ($this->status === 'disetujui') {
            if (! $this->start_date || ! $this->end_date) {
                return 'terjadwal';
            }

            $today = now()->timezone('Asia/Jakarta')->startOfDay();
            $startDate = $this->start_date->copy()->timezone('Asia/Jakarta')->startOfDay();
            $endDate = $this->end_date->copy()->timezone('Asia/Jakarta')->startOfDay();

            if ($today->lt($startDate)) {
                return 'terjadwal';
            }

            if ($today->gte($startDate) && $today->lte($endDate)) {
                return 'tayang';
            }

            if ($today->gt($endDate)) {
                return 'selesai';
            }
        }

        return $this->status ?? 'menunggu';
    }

    /**
     * Label terpusat per peran (Admin / Pengaju).
     */
    public static function getStatusLabel(string $status, string $role = 'admin'): string
    {
        $adminLabels = [
            'menunggu' => 'Perlu Direview',
            'revisi' => 'Menunggu Perbaikan',
            'terjadwal' => 'Terjadwal',
            'tayang' => 'Sedang Tayang',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak Admin',
            'dibatalkan' => 'Dibatalkan Pengaju',
        ];

        $pengajuLabels = [
            'menunggu' => 'Menunggu Review Admin',
            'revisi' => 'Perlu Direvisi',
            'terjadwal' => 'Disetujui (Terjadwal)',
            'tayang' => 'Sedang Tayang',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];

        $labels = $role === 'pengaju' ? $pengajuLabels : $adminLabels;

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Keterangan deskripsi singkat status untuk kartu atau tooltip.
     */
    public static function getStatusDescription(string $status, string $role = 'admin'): string
    {
        $adminDescriptions = [
            'menunggu' => 'Menunggu keputusan Anda',
            'revisi' => 'Dikembalikan ke pengaju',
            'terjadwal' => 'Siap tayang sesuai jadwal',
            'tayang' => 'Aktif tayang di layar',
            'selesai' => 'Masa tayang berakhir',
            'ditolak' => 'Tidak dapat diproses',
            'dibatalkan' => 'Ditarik oleh pengaju',
        ];

        return $adminDescriptions[$status] ?? '';
    }

    /**
     * Warna token bawaan per status.
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'menunggu' => 'warning',
            'revisi' => 'info',
            'terjadwal' => 'teal',
            'tayang' => 'success',
            'selesai' => 'indigo',
            'ditolak' => 'danger',
            'dibatalkan' => 'gray',
            default => 'gray',
        };
    }

    /**
     * SQL CASE WHEN untuk menghitung display_status secara agregat (bebas perulangan PHP).
     */
    public static function getDisplayStatusCaseSql(?string $table = 'submissions', ?string $currentDate = null): string
    {
        $prefix = $table ? "{$table}." : '';
        $dateExpr = $currentDate ? "'{$currentDate}'" : 'CURDATE()';

        return "CASE
            WHEN {$prefix}status = 'menunggu' THEN 'menunggu'
            WHEN {$prefix}status = 'revisi' THEN 'revisi'
            WHEN {$prefix}status = 'ditolak' THEN 'ditolak'
            WHEN {$prefix}status = 'dibatalkan' THEN 'dibatalkan'
            WHEN {$prefix}status = 'selesai' THEN 'selesai'
            WHEN {$prefix}status = 'disetujui' AND {$prefix}start_date IS NOT NULL AND {$dateExpr} < {$prefix}start_date THEN 'terjadwal'
            WHEN {$prefix}status = 'disetujui' AND {$prefix}start_date IS NOT NULL AND {$prefix}end_date IS NOT NULL AND {$dateExpr} >= {$prefix}start_date AND {$dateExpr} <= {$prefix}end_date THEN 'tayang'
            WHEN {$prefix}status = 'disetujui' AND {$prefix}end_date IS NOT NULL AND {$dateExpr} > {$prefix}end_date THEN 'selesai'
            WHEN {$prefix}status = 'disetujui' THEN 'terjadwal'
            ELSE {$prefix}status
        END";
    }

    /**
     * Menghitung total per display_status dalam 1 query SQL murni.
     *
     * @return array{menunggu: int, revisi: int, terjadwal: int, tayang: int, selesai: int, ditolak: int, dibatalkan: int}
     */
    public static function getDisplayStatusCounts(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, ?int $userId = null): array
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $cacheKey = implode('|', [
            $today,
            $startDate ? $startDate->format('Y-m-d H:i:s') : 'null',
            $endDate ? $endDate->format('Y-m-d H:i:s') : 'null',
            $userId !== null ? (string) $userId : 'all',
        ]);

        if (isset(self::$runtimeStatusCounts[$cacheKey])) {
            return self::$runtimeStatusCounts[$cacheKey];
        }

        $caseSql = self::getDisplayStatusCaseSql('submissions', $today);

        $query = self::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $counts = $query
            ->selectRaw("({$caseSql}) as display_status, COUNT(*) as total")
            ->groupBy('display_status')
            ->pluck('total', 'display_status')
            ->all();

        return self::$runtimeStatusCounts[$cacheKey] = [
            'menunggu' => (int) ($counts['menunggu'] ?? 0),
            'revisi' => (int) ($counts['revisi'] ?? 0),
            'terjadwal' => (int) ($counts['terjadwal'] ?? 0),
            'tayang' => (int) ($counts['tayang'] ?? 0),
            'selesai' => (int) ($counts['selesai'] ?? 0),
            'ditolak' => (int) ($counts['ditolak'] ?? 0),
            'dibatalkan' => (int) ($counts['dibatalkan'] ?? 0),
        ];
    }
}
