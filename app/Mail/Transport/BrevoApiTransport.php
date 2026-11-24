<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $endpoint = 'https://api.brevo.com/v3/smtp/email',
        private readonly int $timeout = 8,
    ) {
        parent::__construct();
    }

    /**
     * Mengirim pesan email melalui Brevo Transactional Email API v3 (HTTPS port 443).
     *
     * @throws TransportException
     */
    protected function doSend(SentMessage $message): void
    {
        if (trim($this->apiKey) === '') {
            throw new TransportException('Konfigurasi BREVO_API_KEY belum diisi.');
        }

        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $fromAddress = $email->getFrom()[0] ?? $message->getEnvelope()->getSender();
        $senderEmail = $fromAddress->getAddress();
        $senderName = trim($fromAddress->getName());

        $sender = ['email' => $senderEmail];
        if ($senderName !== '') {
            $sender['name'] = $senderName;
        }

        $to = $this->formatAddresses($email->getTo());
        if ($to === []) {
            $to = $this->formatAddresses($message->getEnvelope()->getRecipients());
        }

        if ($to === []) {
            throw new TransportException('Alamat email penerima tidak tersedia.');
        }

        $payload = [
            'sender' => $sender,
            'to' => $to,
            'subject' => (string) ($email->getSubject() ?? ''),
        ];

        $cc = $this->formatAddresses($email->getCc());
        if ($cc !== []) {
            $payload['cc'] = $cc;
        }

        $bcc = $this->formatAddresses($email->getBcc());
        if ($bcc !== []) {
            $payload['bcc'] = $bcc;
        }

        $replyTo = $email->getReplyTo()[0] ?? null;
        if ($replyTo instanceof Address) {
            $replyToPayload = ['email' => $replyTo->getAddress()];
            if (trim($replyTo->getName()) !== '') {
                $replyToPayload['name'] = trim($replyTo->getName());
            }
            $payload['replyTo'] = $replyToPayload;
        }

        $htmlBody = $email->getHtmlBody();
        if (is_resource($htmlBody)) {
            $htmlBody = stream_get_contents($htmlBody);
        }
        if (is_string($htmlBody) && $htmlBody !== '') {
            $payload['htmlContent'] = $htmlBody;
        }

        $textBody = $email->getTextBody();
        if (is_resource($textBody)) {
            $textBody = stream_get_contents($textBody);
        }
        if (is_string($textBody) && $textBody !== '') {
            $payload['textContent'] = $textBody;
        }

        if (! isset($payload['htmlContent']) && ! isset($payload['textContent'])) {
            $payload['textContent'] = '';
        }

        $response = Http::timeout(max(1, $this->timeout))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'api-key' => $this->apiKey,
            ])
            ->post($this->endpoint, $payload);

        if (! $response->successful()) {
            $apiMessage = (string) ($response->json('message') ?? $response->body());
            $safeMessage = str_replace($this->apiKey, '[REDACTED]', trim($apiMessage));

            throw new TransportException(
                sprintf('Brevo API mengembalikan HTTP %d: %s', $response->status(), $safeMessage !== '' ? $safeMessage : 'Permintaan ditolak.'),
                $response->status()
            );
        }
    }

    /**
     * Memetakan daftar objek alamat Symfony Mime ke format array API Brevo.
     *
     * @param  array<int, Address>  $addresses
     * @return list<array{email: string, name?: string}>
     */
    private function formatAddresses(array $addresses): array
    {
        $formatted = [];

        foreach ($addresses as $address) {
            $entry = ['email' => $address->getAddress()];
            $name = trim($address->getName());
            if ($name !== '') {
                $entry['name'] = $name;
            }
            $formatted[] = $entry;
        }

        return $formatted;
    }

    public function __toString(): string
    {
        return 'brevo+api';
    }
}
