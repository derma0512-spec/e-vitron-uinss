<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Models\Videotron;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VideotronConditionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $type  'disruption' | 'recovered'
     * @param  string|null  $batchId  ID kejadian bersama agar pop-up di sisi pengaju bisa dikelompokkan
     */
    public function __construct(
        public Videotron $videotron,
        public Submission $submission,
        public string $type = 'disruption',
        public ?string $batchId = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $vName = $this->videotron->name;
        $issueNotes = $this->videotron->issue_notes;
        $estDate = $this->videotron->estimated_recovery_date
            ? Carbon::parse($this->videotron->estimated_recovery_date)->locale('id')->translatedFormat('d M Y')
            : null;

        if ($this->type === 'recovered') {
            $event = 'videotron_recovered';
            $title = "Kabar Baik: {$vName} Pulih";
            $message = "Perangkat {$vName} telah selesai diperbaiki dan siap menayangkan materi kegiatan Anda kembali.";
            $statusLabel = 'Normal Kembali';
            $badgeColor = 'success';
        } else {
            $event = 'videotron_disruption';
            $title = "Kendala: {$vName}";

            $reasonPhrase = filled($issueNotes) ? "karena {$issueNotes}" : 'karena kendala operasional';
            $estPhrase = $estDate
                ? "dan diestimasikan aktif kembali pada tanggal {$estDate}"
                : 'dan sedang dalam penanganan intensif tim teknisi';

            // Cek apakah ada videotron lain yang dipilih dalam pengajuan ini dan kondisinya masih normal
            $otherVideotrons = $this->submission->videotrons
                ->filter(fn ($v) => (int) $v->id !== (int) $this->videotron->id && $v->condition === 'normal' && (bool) $v->is_active);

            if ($otherVideotrons->count() > 0) {
                // Skenario Multi-Videotron
                $otherNames = $otherVideotrons->pluck('name')->join(', ');
                $message = "Mohon maaf, penayangan di {$vName} yang Anda ajukan sementara terganggu {$reasonPhrase} {$estPhrase}. Namun, materi Anda tetap dapat tayang di videotron lain yang Anda pilih ({$otherNames}).";
            } else {
                // Skenario Single-Videotron
                $message = "Mohon maaf, pengajuan penayangan di {$vName} yang Anda ajukan sementara terganggu {$reasonPhrase} {$estPhrase}, dan akan segera kami tayangkan begitu perangkat selesai diperbaiki.";
            }

            $statusLabel = 'Kendala Perangkat';
            $badgeColor = 'warning';
        }

        return [
            'event' => $event,
            'submission_id' => $this->submission->id,
            'ticket_code' => $this->submission->ticket_code,
            'submission_title' => $this->submission->title,
            'videotron_id' => $this->videotron->id,
            'videotron_name' => $vName,
            'videotron_condition' => $this->videotron->condition,
            'batch_id' => $this->batchId,
            'title' => $title,
            'message' => $message,
            'notes' => $issueNotes,
            'estimated_recovery_date' => $this->videotron->estimated_recovery_date,
            'status_label' => $statusLabel,
            'badge_color' => $badgeColor,
            'url' => route('pengajuan.show', $this->submission->ticket_code),
        ];
    }
}
