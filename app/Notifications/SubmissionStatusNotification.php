<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SubmissionStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $event  'submitted', 'disetujui', 'revisi', 'ditolak', 'selesai'
     */
    public function __construct(
        public Submission $submission,
        public string $event,
        public ?string $customNotes = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Kirim email jika user memiliki alamat email yang valid
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
        $notes = $this->customNotes ?? $this->submission->admin_notes;

        [$title, $messageText, $subjectPrefix] = match ($this->event) {
            'submitted' => [
                'Pengajuan Berhasil Dikirim',
                'Pengajuan penayangan videotron Anda telah berhasil dicatat ke sistem E-VITRON dan sedang dalam antrean verifikasi oleh tim admin.',
                'Pengajuan Berhasil Dikirim',
            ],
            'disetujui' => [
                'Pengajuan Disetujui',
                'Selamat! Pengajuan penayangan materi videotron Anda telah disetujui oleh tim verifikator dan dijadwalkan tayang sesuai periode yang diajukan.',
                'Pengajuan Disetujui',
            ],
            'revisi' => [
                'Pengajuan Memerlukan Perbaikan',
                'Pengajuan penayangan videotron Anda memerlukan perbaikan materi atau berkas sebelum dapat disetujui. Silakan periksa catatan dari admin di bawah ini.',
                'Perlu Revisi',
            ],
            'ditolak' => [
                'Pengajuan Belum Dapat Disetujui',
                'Mohon maaf, pengajuan penayangan materi videotron Anda belum dapat disetujui oleh pihak kampus dengan alasan pada catatan di bawah ini.',
                'Pengajuan Ditolak',
            ],
            'selesai' => [
                'Penayangan Selesai',
                'Materi kegiatan Anda telah selesai ditayangkan pada titik videotron UIN Siber Syekh Nurjati Cirebon sesuai periode jadwal yang disetujui.',
                'Penayangan Selesai',
            ],
            default => [
                'Pembaruan Status Pengajuan',
                'Status pengajuan penayangan videotron Anda telah diperbarui.',
                'Pembaruan Status',
            ],
        };

        $subject = sprintf(
            '[E-VITRON] %s - %s (#%s)',
            $subjectPrefix,
            Str::limit($this->submission->title, 40),
            $this->submission->ticket_code
        );

        $url = route('pengajuan.show', $this->submission->ticket_code);

        $recipientName = $this->submission->pic_name ?: ($notifiable->name ?? 'Pemohon');
        $recipientOrg = $this->submission->organization ?: ($notifiable->unit_or_prodi ?? null);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.submission-status', [
                'event' => $this->event,
                'title' => $title,
                'messageText' => $messageText,
                'notes' => $notes,
                'submission' => $this->submission,
                'recipientName' => $recipientName,
                'recipientOrg' => $recipientOrg,
                'url' => $url,
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
        $notes = $this->customNotes ?? $this->submission->admin_notes;

        [$title, $message, $statusLabel, $badgeColor] = match ($this->event) {
            'submitted' => [
                'Pengajuan Berhasil Dikirim',
                'Pengajuan penayangan videotron Anda telah berhasil dicatat dan sedang menunggu antrean peninjauan oleh tim admin.',
                'Menunggu',
                'menunggu',
            ],
            'disetujui' => [
                'Pengajuan Disetujui',
                'Selamat! Pengajuan penayangan videotron Anda telah disetujui oleh admin dan siap dijadwalkan.',
                'Disetujui',
                'disetujui',
            ],
            'revisi' => [
                'Pengajuan Perlu Revisi',
                $notes
                    ? 'Pengajuan Anda memerlukan perbaikan dari admin: "'.Str::limit(strip_tags($notes), 120).'"'
                    : 'Pengajuan Anda memerlukan perbaikan. Silakan periksa catatan revisi dari admin.',
                'Perlu Revisi',
                'revisi',
            ],
            'ditolak' => [
                'Pengajuan Ditolak',
                $notes
                    ? 'Pengajuan penayangan belum dapat disetujui: "'.Str::limit(strip_tags($notes), 120).'"'
                    : 'Mohon maaf, pengajuan penayangan videotron Anda tidak disetujui oleh admin.',
                'Ditolak',
                'ditolak',
            ],
            'selesai' => [
                'Penayangan Selesai',
                'Periode penayangan materi videotron untuk kegiatan ini telah selesai sesuai jadwal.',
                'Selesai',
                'selesai',
            ],
            default => [
                'Pembaruan Pengajuan',
                'Status pengajuan Anda telah diperbarui.',
                ucfirst($this->event),
                'default',
            ],
        };

        return [
            'event' => $this->event,
            'submission_id' => $this->submission->id,
            'ticket_code' => $this->submission->ticket_code,
            'submission_title' => $this->submission->title,
            'title' => $title,
            'message' => $message,
            'notes' => $notes,
            'status_label' => $statusLabel,
            'badge_color' => $badgeColor,
            'url' => route('pengajuan.show', $this->submission->ticket_code),
        ];
    }
}
