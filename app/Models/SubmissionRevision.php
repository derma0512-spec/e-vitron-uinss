<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'revision_type',
        'admin_feedback',
        'user_reply',
        'previous_gdrive_link',
        'updated_gdrive_link',
        'previous_surat_peminjaman',
        'updated_surat_peminjaman',
        'requested_at',
        'resolved_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Mengembalikan tipe revisi efektif ('surat', 'materi', 'keduanya')
     */
    public function getEffectiveTypeAttribute(): string
    {
        $rawType = (string) ($this->revision_type ?? '');
        if ($rawType === 'keduanya') {
            return 'keduanya';
        }

        $text = strtolower((string) ($this->admin_feedback ?? ''));
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

        if (in_array($rawType, ['surat', 'materi', 'keduanya'], true)) {
            return $rawType;
        }

        return 'keduanya';
    }
}
