<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat detail atau mencetak bukti pengajuan.
     */
    public function view(User $user, Submission $submission): bool
    {
        return $user->isActiveAdmin() || (int) $submission->user_id === (int) $user->id;
    }

    /**
     * Tentukan apakah pengguna dapat mengunduh/melihat berkas PDF surat permohonan.
     */
    public function downloadSurat(User $user, Submission $submission): bool
    {
        return $user->isActiveAdmin() || (int) $submission->user_id === (int) $user->id;
    }

    /**
     * Tentukan apakah pengaju dapat membatalkan pengajuan (hanya pemilik saat status menunggu).
     */
    public function cancel(User $user, Submission $submission): bool
    {
        return (int) $submission->user_id === (int) $user->id && $submission->status === 'menunggu';
    }

    /**
     * Tentukan apakah pengaju dapat mengirim perbaikan revisi (hanya pemilik saat status revisi).
     */
    public function submitRevision(User $user, Submission $submission): bool
    {
        return (int) $submission->user_id === (int) $user->id && $submission->status === 'revisi';
    }
}
