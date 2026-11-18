<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $status  'approved' | 'rejected'
     */
    public function __construct(
        public User $user,
        public string $status = 'approved',
        public ?string $notes = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (! empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        [$title, $messageText, $subject] = match ($this->status) {
            'rejected' => [
                'Pendaftaran Akun Belum Disetujui',
                'Mohon maaf, pendaftaran akun E-VITRON Anda belum dapat disetujui oleh Admin PUSTIKOM UIN Siber Syekh Nurjati Cirebon.',
                '[E-VITRON] Pendaftaran Akun Belum Dapat Disetujui',
            ],
            default => [
                'Akun Anda Telah Disetujui & Aktif',
                'Selamat! Pendaftaran akun Anda pada sistem E-VITRON UIN Siber Syekh Nurjati Cirebon telah diverifikasi dan DISETUJUI oleh Admin PUSTIKOM. Sekarang Anda sudah dapat masuk (login) dan mengajukan permohonan penayangan videotron.',
                '[E-VITRON] Akun Anda Telah Disetujui — Silakan Login',
            ],
        };

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.account-approval', [
                'user' => $this->user,
                'status' => $this->status,
                'title' => $title,
                'messageText' => $messageText,
                'notes' => $this->notes,
                'loginUrl' => route('login'),
                'subjectTitle' => $subject,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        [$title, $message] = match ($this->status) {
            'rejected' => [
                'Pendaftaran Akun Ditolak',
                'Pendaftaran akun Anda belum dapat disetujui oleh Admin PUSTIKOM.',
            ],
            default => [
                'Akun Disetujui & Aktif',
                'Selamat datang di E-VITRON! Akun Anda telah diverifikasi dan disetujui oleh Admin PUSTIKOM.',
            ],
        };

        return [
            'event' => 'account_'.$this->status,
            'title' => $title,
            'message' => $message,
            'status_label' => $this->status === 'approved' ? 'Disetujui' : 'Ditolak',
            'badge_color' => $this->status === 'approved' ? 'emerald' : 'rose',
            'url' => route('home'),
            'created_at' => now()->toIso8601String(),
        ];
    }
}

