<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Videotron;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubmissionDecisionService
{
    /**
     * Memeriksa ketersediaan perangkat videotron dan potensi benturan jadwal
     * dengan pengajuan lain yang telah disetujui.
     *
     * @return array{
     *     can_approve: bool,
     *     unavailable_videotrons: Collection<int, Videotron>,
     *     conflicts: Collection<int, Submission>,
     *     error_message: ?string
     * }
     */
    public function checkAvailabilityAndConflicts(Submission $submission): array
    {
        // Pastikan relasi videotrons ter-load
        if (! $submission->relationLoaded('videotrons')) {
            $submission->load('videotrons');
        }

        $videotrons = $submission->videotrons;

        if ($videotrons->isEmpty()) {
            return [
                'can_approve' => false,
                'unavailable_videotrons' => collect(),
                'conflicts' => collect(),
                'error_message' => 'Persetujuan gagal: Belum ada perangkat videotron yang dipilih untuk pengajuan ini.',
            ];
        }

        // 1. Cek Ketersediaan / Kondisi Operasional Perangkat
        $unavailableVideotrons = $videotrons->filter(function (Videotron $videotron) {
            return ! $videotron->is_active || in_array($videotron->condition, ['gangguan', 'dalam_perbaikan', 'rusak'], true);
        });

        if ($unavailableVideotrons->isNotEmpty()) {
            $names = $unavailableVideotrons->map(fn ($v) => "{$v->name} (Kondisi: ".str_replace('_', ' ', (string) $v->condition).')')->join(', ');

            return [
                'can_approve' => false,
                'unavailable_videotrons' => $unavailableVideotrons,
                'conflicts' => collect(),
                'error_message' => "Perangkat berikut sedang tidak aktif atau mengalami gangguan: {$names}.",
            ];
        }

        // 2. Cek Pengajuan Lain yang Juga Tayang pada Layar & Rentang Tanggal yang Sama (Rotasi Playlist Bersamaan)
        // Videotron beroperasi nonstop dan memutar banyak materi secara bergiliran (looping playlist).
        // Sehingga pengajuan pada tanggal yang sama bukan benturan/penolakan, melainkan informasi antrean rotasi penayangan.
        $coexistingBroadcasts = Submission::query()
            ->where('id', '!=', $submission->id)
            ->where('status', 'disetujui')
            ->whereHas('videotrons', function ($query) use ($videotrons) {
                $query->whereIn('videotrons.id', $videotrons->pluck('id'));
            })
            ->where(function ($query) use ($submission) {
                $query->where('start_date', '<=', $submission->end_date)
                    ->where('end_date', '>=', $submission->start_date);
            })
            ->with(['videotrons', 'user'])
            ->get();

        return [
            'can_approve' => true,
            'unavailable_videotrons' => collect(),
            'conflicts' => $coexistingBroadcasts,
            'coexisting_broadcasts' => $coexistingBroadcasts,
            'error_message' => null,
        ];
    }

    /**
     * Menyetujui pengajuan setelah validasi ketersediaan dan benturan lolos.
     *
     * @throws DomainException
     */
    public function approve(Submission $submission, ?string $notes = null, ?int $adminId = null): void
    {
        if (! in_array($submission->status, ['menunggu', 'revisi', 'ditolak'], true)) {
            throw new DomainException("Pengajuan berstatus '{$submission->status}' tidak dapat disetujui.");
        }

        // Pastikan pengajuan memiliki perangkat videotron yang dipilih
        if (! $submission->relationLoaded('videotrons')) {
            $submission->load('videotrons');
        }
        if ($submission->videotrons->isEmpty()) {
            throw new DomainException('Persetujuan gagal: Belum ada perangkat videotron yang dipilih untuk pengajuan ini.');
        }

        $check = $this->checkAvailabilityAndConflicts($submission);
        if (! $check['can_approve']) {
            throw new DomainException($check['error_message'] ?? 'Persetujuan gagal: Perangkat videotron sasaran sedang tidak aktif atau mengalami gangguan.');
        }

        $adminId = $adminId ?? Auth::id();
        $approvalNotes = ! empty(trim((string) $notes)) ? trim((string) $notes) : 'Pengajuan telah diperiksa dan disetujui untuk penayangan sesuai jadwal.';

        DB::transaction(function () use ($submission, $approvalNotes, $adminId) {
            $oldStatus = $submission->status;

            // Catat ke riwayat status
            $submission->statusHistories()->create([
                'user_id' => $adminId,
                'from_status' => $oldStatus,
                'to_status' => 'disetujui',
                'notes' => $approvalNotes,
            ]);

            // Update status dan catatan admin
            $submission->update([
                'status' => 'disetujui',
                'admin_notes' => $approvalNotes,
            ]);
        });
    }

    /**
     * Meminta revisi kepada pengaju dengan catatan wajib.
     *
     * @throws DomainException
     */
    public function requestRevision(Submission $submission, string $feedback, ?int $adminId = null, ?string $revisionType = null): void
    {
        // Validasi status awal (mendukung koreksi keputusan dari disetujui/ditolak)
        if (! in_array($submission->status, ['menunggu', 'disetujui', 'ditolak'], true)) {
            throw new DomainException("Pengajuan dengan status '{$submission->status}' tidak dapat diminta revisi.");
        }

        $feedback = trim($feedback);
        if (mb_strlen($feedback) < 5) {
            throw new DomainException('Catatan revisi wajib diisi dengan jelas (minimal 5 karakter) agar pengaju memahami bagian yang harus diperbaiki.');
        }

        $adminId = $adminId ?? Auth::id();

        // Jika revisionType belum ditentukan atau unknown, smart detect dari feedback
        $text = strtolower($feedback);
        $hasSurat = str_contains($text, 'surat') || str_contains($text, 'peminjaman') || str_contains($text, 'permohonan') || str_contains($text, 'stempel') || str_contains($text, 'tanda tangan') || str_contains($text, 'ttd') || str_contains($text, 'dokumen') || str_contains($text, 'pdf') || str_contains($text, 'berkas');
        $hasMateri = str_contains($text, 'drive') || str_contains($text, 'link') || str_contains($text, 'tautan') || str_contains($text, 'video') || str_contains($text, 'poster') || str_contains($text, 'durasi') || str_contains($text, 'resolusi') || str_contains($text, 'akses') || str_contains($text, 'materi') || str_contains($text, 'gdrive') || str_contains($text, 'konten');

        if (empty($revisionType) || $revisionType === 'unknown') {
            if (! $hasSurat && ! $hasMateri) {
                throw new DomainException('Revisi tidak dapat dikirim: Mohon sertakan kata "surat" atau "drive / video" agar sistem mengetahui form yang harus dibuka untuk pengaju.');
            }

            if ($hasSurat && ! $hasMateri) {
                $revisionType = 'surat';
            } elseif ($hasSurat && $hasMateri) {
                $revisionType = 'keduanya';
            } else {
                $revisionType = 'materi';
            }
        }

        DB::transaction(function () use ($submission, $feedback, $adminId, $revisionType) {
            $oldStatus = $submission->status;

            // Catat di tabel submission_revisions
            $submission->revisions()->create([
                'revision_type' => $revisionType,
                'admin_feedback' => $feedback,
                'previous_gdrive_link' => $submission->gdrive_link,
                'previous_surat_peminjaman' => $submission->surat_peminjaman,
                'requested_at' => now(),
            ]);

            // Catat di tabel status_histories
            $submission->statusHistories()->create([
                'user_id' => $adminId,
                'from_status' => $oldStatus,
                'to_status' => 'revisi',
                'notes' => $feedback,
            ]);

            // Update status pengajuan & reset penanda unduh materi jika sebelumnya disetujui
            $submission->update([
                'status' => 'revisi',
                'admin_notes' => $feedback,
                'material_downloaded_at' => null,
                'material_downloaded_by' => null,
            ]);
        });
    }

    /**
     * Menolak pengajuan dengan alasan penolakan wajib.
     *
     * @throws DomainException
     */
    public function reject(Submission $submission, string $reason, ?int $adminId = null): void
    {
        // Validasi status awal (mendukung koreksi keputusan dari disetujui/revisi)
        if (! in_array($submission->status, ['menunggu', 'disetujui', 'revisi'], true)) {
            throw new DomainException("Pengajuan berstatus '{$submission->status}' tidak dapat ditolak.");
        }

        $reason = trim($reason);
        if (mb_strlen($reason) < 5) {
            throw new DomainException('Alasan penolakan wajib diisi dengan jelas (minimal 5 karakter) untuk transparansi keputusan.');
        }

        $adminId = $adminId ?? Auth::id();

        DB::transaction(function () use ($submission, $reason, $adminId) {
            $oldStatus = $submission->status;

            // Catat di riwayat status beserta identitas admin penolak dan waktu
            $submission->statusHistories()->create([
                'user_id' => $adminId,
                'from_status' => $oldStatus,
                'to_status' => 'ditolak',
                'notes' => $reason,
            ]);

            // Update status pengajuan & reset penanda unduh materi jika sebelumnya disetujui
            $submission->update([
                'status' => 'ditolak',
                'admin_notes' => $reason,
                'material_downloaded_at' => null,
                'material_downloaded_by' => null,
            ]);
        });
    }

    /**
     * Menandai materi pengajuan yang disetujui sebagai "Sudah Diunduh".
     * Tidak mengubah status pengajuan (tidak otomatis menjadi Selesai).
     *
     * @throws DomainException
     */
    public function markMaterialDownloaded(Submission $submission, ?int $adminId = null): void
    {
        $adminId = $adminId ?? Auth::id();

        DB::transaction(function () use ($submission, $adminId) {
            $locked = Submission::query()->lockForUpdate()->findOrFail($submission->id);

            if ($locked->status !== 'disetujui') {
                throw new DomainException('Status materi hanya dapat diperbarui untuk pengajuan yang sudah disetujui.');
            }

            if ($locked->material_downloaded_at !== null) {
                throw new DomainException('Materi pengajuan ini sudah ditandai Sudah Diunduh sebelumnya.');
            }

            $locked->update([
                'material_downloaded_at' => now(),
                'material_downloaded_by' => $adminId,
            ]);
        });

        $submission->refresh();
    }
}
