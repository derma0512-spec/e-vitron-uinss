<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminAlertNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info',
        public ?string $url = null,
        public ?string $ticketCode = null,
        public ?int $submissionId = null
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
        return [
            'format' => 'filament',
            'title' => $this->title,
            'body' => $this->message,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'ticket_code' => $this->ticketCode,
            'submission_id' => $this->submissionId,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
