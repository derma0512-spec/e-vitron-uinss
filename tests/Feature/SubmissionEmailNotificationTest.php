<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use App\Notifications\AccountApprovalNotification;
use App\Notifications\SubmissionStatusNotification;
use App\Services\SubmissionDecisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class SubmissionEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@mitra-uin.com',
            'role' => 'mitra',
            'unit_or_prodi' => 'PT Sponsor Kreatif',
            'phone' => '081234567890',
        ]);

        $this->submission = Submission::create([
            'ticket_code' => 'TKT-202610-TEST1',
            'user_id' => $this->user->id,
            'title' => 'Sosialisasi Beasiswa Mitra',
            'content_type' => 'poster',
            'description' => 'Penayangan poster beasiswa mahasiswa.',
            'pic_name' => 'Ahmad Fauzi',
            'organization' => 'PT Sponsor Kreatif',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/test12345',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
            'admin_notes' => null,
        ]);
    }

    /**
     * Pastikan channel notification menyertakan 'mail' dan 'database' jika email valid,
     * dan hanya 'database' jika alamat email kosong.
     */
    public function test_submission_status_notification_channels(): void
    {
        $notif = new SubmissionStatusNotification($this->submission, 'disetujui');

        $channelsWithEmail = $notif->via($this->user);
        $this->assertContains('database', $channelsWithEmail);
        $this->assertContains('mail', $channelsWithEmail);

        $userWithoutEmail = new User(['email' => null]);
        $channelsWithoutEmail = $notif->via($userWithoutEmail);
        $this->assertContains('database', $channelsWithoutEmail);
        $this->assertNotContains('mail', $channelsWithoutEmail);
    }

    /**
     * Pastikan toMail menghasilkan subjek, view template, dan data yang tepat.
     */
    public function test_to_mail_builds_mail_message_properly(): void
    {
        $notif = new SubmissionStatusNotification(
            $this->submission,
            'revisi',
            'Harap perbaiki resolusi gambar menjadi 1920x1080 piksel.'
        );

        $mailMessage = $notif->toMail($this->user);

        $this->assertStringContainsString('[E-VITRON]', $mailMessage->subject);
        $this->assertStringContainsString('Perlu Revisi', $mailMessage->subject);
        $this->assertStringContainsString($this->submission->ticket_code, $mailMessage->subject);
        $this->assertEquals('emails.submission-status', $mailMessage->view);

        // Pastikan HTML template email ter-render tanpa error
        $renderedHtml = (string) $mailMessage->render();
        $this->assertStringContainsString('E-VITRON', $renderedHtml);
        $this->assertStringContainsString('UIN Siber Syekh Nurjati Cirebon', $renderedHtml);
        $this->assertStringContainsString('Ahmad Fauzi', $renderedHtml);
        $this->assertStringContainsString('PT Sponsor Kreatif', $renderedHtml);
        $this->assertStringContainsString('TKT-202610-TEST1', $renderedHtml);
        $this->assertStringContainsString('Harap perbaiki resolusi gambar', $renderedHtml);
        $this->assertStringContainsString('Buka Rincian Pengajuan di E-VITRON', $renderedHtml);
    }

    /**
     * Pastikan saat status diubah menjadi 'disetujui', observer otomatis mengirimkan notifikasi.
     */
    public function test_submission_observer_sends_notification_on_status_change(): void
    {
        Notification::fake();

        $this->submission->status = 'disetujui';
        $this->submission->admin_notes = 'Materi telah disetujui untuk tayang.';
        $this->submission->save();

        Notification::assertSentTo(
            $this->user,
            SubmissionStatusNotification::class,
            function (SubmissionStatusNotification $notification) {
                return $notification->event === 'disetujui' &&
                       $notification->submission->id === $this->submission->id;
            }
        );
    }

    /**
     * Pastikan seluruh variasi status ('disetujui', 'revisi', 'ditolak', 'selesai')
     * dapat ter-render dengan sempurna ke HTML surat email.
     */
    public function test_all_status_events_render_cleanly_in_email_template(): void
    {
        $events = ['disetujui', 'revisi', 'ditolak', 'selesai'];

        foreach ($events as $event) {
            $notif = new SubmissionStatusNotification(
                $this->submission,
                $event,
                "Catatan pengetesan untuk status $event"
            );

            $mail = $notif->toMail($this->user);
            $html = (string) $mail->render();

            $this->assertNotEmpty($html);
            $this->assertStringContainsString($this->submission->ticket_code, $html);
            $this->assertStringContainsString("Catatan pengetesan untuk status $event", $html);
        }
    }

    /**
     * Pastikan kegagalan SMTP saat admin mengambil keputusan tidak menggagalkan transaksi DB
     * dan tetap menjaga notifikasi portal (database) tersimpan tanpa duplikasi.
     */
    public function test_admin_approval_succeeds_and_preserves_database_notification_when_smtp_fails(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'identifier' => 'ADM-TEST-SMTP',
        ]);

        $videotron = Videotron::create([
            'name' => 'Videotron Uji SMTP',
            'location' => 'Kampus Utama',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'operating_hours' => '06.00 – 22.00 WIB',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $this->submission->videotrons()->attach($videotron->id);

        Mail::shouldReceive('mailer')
            ->once()
            ->andThrow(new TransportException('SMTP server unreachable'));
        Log::spy();

        app(SubmissionDecisionService::class)->approve(
            $this->submission,
            'Materi siap ditayangkan sesuai jadwal.',
            $admin->id
        );

        $this->assertSame('disetujui', $this->submission->fresh()->status);
        $this->assertDatabaseHas('submission_status_histories', [
            'submission_id' => $this->submission->id,
            'to_status' => 'disetujui',
            'user_id' => $admin->id,
        ]);

        $this->assertSame(1, $this->user->notifications()->count());

        $storedNotification = $this->user->notifications()->first();
        $this->assertNotNull($storedNotification);
        $this->assertSame('disetujui', $storedNotification->data['event']);
        $this->assertSame($this->submission->ticket_code, $storedNotification->data['ticket_code']);

        Log::shouldHaveReceived('warning')->once();
    }

    /**
     * Pastikan driver Brevo HTTPS API (port 443) mengirim payload email persetujuan akun
     * yang valid dan menggunakan header api-key tanpa bergantung pada port SMTP.
     */
    public function test_brevo_https_transport_sends_account_approval_email_over_port_443(): void
    {
        config([
            'mail.default' => 'brevo',
            'mail.from.address' => 'evitron.uinssc@gmail.com',
            'mail.from.name' => 'E-VITRON UIN SSC',
            'services.brevo.api_key' => 'xkeysib-test-secret-key-12345',
            'services.brevo.endpoint' => 'https://api.brevo.com/v3/smtp/email',
            'services.brevo.timeout' => 8,
        ]);
        Mail::purge('brevo');

        Http::fake([
            'https://api.brevo.com/v3/smtp/email' => Http::response(['messageId' => '<msg-123@brevo>'], 201),
        ]);

        $this->user->notifyNow(new AccountApprovalNotification(
            $this->user,
            'approved',
            'Akun mitra Anda sudah aktif.'
        ));

        Http::assertSent(function (HttpClientRequest $request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->hasHeader('api-key', 'xkeysib-test-secret-key-12345')
                && ($payload['sender']['email'] ?? null) === 'evitron.uinssc@gmail.com'
                && ($payload['sender']['name'] ?? null) === 'E-VITRON UIN SSC'
                && ($payload['to'][0]['email'] ?? null) === 'ahmad.fauzi@mitra-uin.com'
                && str_contains((string) ($payload['subject'] ?? ''), '[E-VITRON]')
                && str_contains((string) ($payload['htmlContent'] ?? ''), 'Akun mitra Anda sudah aktif.');
        });
    }

    /**
     * Pastikan error dari Brevo API melempar TransportException yang aman
     * tanpa membocorkan API Key ke dalam pesan error.
     */
    public function test_brevo_https_transport_redacts_api_key_when_api_fails(): void
    {
        $secretKey = 'xkeysib-super-secret-99999';

        config([
            'mail.default' => 'brevo',
            'mail.from.address' => 'evitron.uinssc@gmail.com',
            'mail.from.name' => 'E-VITRON UIN SSC',
            'services.brevo.api_key' => $secretKey,
            'services.brevo.endpoint' => 'https://api.brevo.com/v3/smtp/email',
        ]);
        Mail::purge('brevo');

        Http::fake([
            'https://api.brevo.com/v3/smtp/email' => Http::response([
                'code' => 'unauthorized',
                'message' => 'Key not found: '.$secretKey,
            ], 401),
        ]);

        try {
            $this->user->notifyNow(new AccountApprovalNotification($this->user, 'approved'));
            $this->fail('TransportException seharusnya dilempar saat Brevo API mengembalikan 401.');
        } catch (TransportException $e) {
            $this->assertStringContainsString('HTTP 401', $e->getMessage());
            $this->assertStringNotContainsString($secretKey, $e->getMessage());
            $this->assertStringContainsString('[REDACTED]', $e->getMessage());
        }
    }
}
