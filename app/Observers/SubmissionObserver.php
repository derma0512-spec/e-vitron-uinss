<?php

namespace App\Observers;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionStatusNotification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmissionObserver
{
    /**
     * Handle the Submission "created" event.
     */
    public function created(Submission $submission): void
    {
        // Kirim notifikasi nyata ke seluruh akun Admin aktif (Format Filament Database Notification)
        $admins = User::activeAdmins()->get();
        foreach ($admins as $admin) {
            $notif = Notification::make()
                ->title('Pengajuan Baru Masuk')
                ->icon('heroicon-o-document-plus')
                ->iconColor('success')
                ->body("Tiket {$submission->ticket_code} ({$submission->title}) siap ditinjau.")
                ->actions([
                    Action::make('view')
                        ->label('Tinjau Sekarang')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.resources.submissions.index', ['selected' => $submission->id, 'from' => 'notification']))
                        ->markAsRead(),
                ]);

            $admin->notifyNow($notif->toDatabase());
        }
    }

    /**
     * Handle the Submission "updated" event.
     */
    public function updated(Submission $submission): void
    {
        if (! $submission->wasChanged('status')) {
            return;
        }

        $newStatus = $submission->status;
        if (! in_array($newStatus, ['disetujui', 'revisi', 'ditolak', 'selesai'], true)) {
            return;
        }

        $user = $submission->user;
        if (! $user || $this->hasRecentStatusNotification($user, $submission, $newStatus)) {
            return;
        }

        $notification = new SubmissionStatusNotification(
            $submission,
            $newStatus,
            $submission->admin_notes
        );

        try {
            $user->notify($notification);
        } catch (Throwable $e) {
            // Jangan gagalkan transaksi keputusan admin jika SMTP bermasalah;
            // catat error dan pastikan notifikasi portal (database) tetap tercatat.
            Log::warning('Pengiriman email notifikasi status pengajuan gagal; status pengajuan tetap disimpan.', [
                'submission_id' => $submission->id,
                'ticket_code' => $submission->ticket_code,
                'status' => $newStatus,
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);

            if (! $this->hasRecentStatusNotification($user, $submission, $newStatus)) {
                try {
                    $user->notifyNow($notification, ['database']);
                } catch (Throwable $fallbackException) {
                    Log::error('Fallback notifikasi database status pengajuan gagal.', [
                        'submission_id' => $submission->id,
                        'ticket_code' => $submission->ticket_code,
                        'status' => $newStatus,
                        'user_id' => $user->id,
                        'exception' => $fallbackException->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Memeriksa apakah notifikasi portal untuk status yang sama baru saja tercatat.
     */
    protected function hasRecentStatusNotification(User $user, Submission $submission, string $status): bool
    {
        return $user->notifications()
            ->where('data->submission_id', $submission->id)
            ->where('data->event', $status)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();
    }
}
