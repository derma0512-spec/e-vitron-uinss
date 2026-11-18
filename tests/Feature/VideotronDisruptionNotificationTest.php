<?php

namespace Tests\Feature;

use App\Filament\Resources\VideotronResource\Pages\ListVideotrons;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use App\Notifications\VideotronConditionNotification;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class VideotronDisruptionNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::getCurrentPanel()->boot();
    }

    private function createTestSubmission(User $user, array $overrides = []): Submission
    {
        return Submission::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Kajian Akbar Ramadhan',
            'ticket_code' => 'VTR-20261027-001',
            'content_type' => 'poster',
            'description' => 'Deskripsi kegiatan publikasi',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'disetujui',
        ], $overrides));
    }

    public function test_single_videotron_submission_receives_single_disruption_message(): void
    {
        Notification::fake();

        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Masjid Kampus',
            'location' => 'Depan Masjid',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-001',
        ]);
        $submission->videotrons()->attach($videotron->id);

        $videotron->condition = 'gangguan';
        $videotron->issue_notes = 'Modul LED sisi barat padam';
        $videotron->estimated_recovery_date = now()->addDays(3)->toDateString();
        $videotron->save();

        $count = $videotron->notifyAffectedSubmissions('disruption');

        $this->assertEquals(1, $count);

        Notification::assertSentTo(
            $applicant,
            VideotronConditionNotification::class,
            function (VideotronConditionNotification $notification) use ($applicant) {
                $data = $notification->toArray($applicant);

                return $data['event'] === 'videotron_disruption'
                    && str_contains($data['message'], 'Videotron Masjid Kampus')
                    && str_contains($data['message'], 'segera kami tayangkan begitu perangkat selesai diperbaiki')
                    && ! str_contains($data['message'], 'tetap dapat tayang di videotron lain');
            }
        );
    }

    public function test_multi_videotron_submission_receives_multi_disruption_message_mentioning_other_screens(): void
    {
        Notification::fake();

        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotronMasjid = Videotron::create([
            'name' => 'Videotron Masjid Kampus',
            'location' => 'Depan Masjid',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);
        $videotronSiber = Videotron::create([
            'name' => 'Videotron Gedung Siber',
            'location' => 'Lobby Siber',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-002',
            'title' => 'Seminar Teknologi Nasional',
        ]);
        $submission->videotrons()->attach([$videotronMasjid->id, $videotronSiber->id]);

        $videotronMasjid->condition = 'dalam_perbaikan';
        $videotronMasjid->issue_notes = 'Perbaikan kabel power utama';
        $videotronMasjid->estimated_recovery_date = now()->addDays(2)->toDateString();
        $videotronMasjid->save();

        $count = $videotronMasjid->notifyAffectedSubmissions('disruption');

        $this->assertEquals(1, $count);

        Notification::assertSentTo(
            $applicant,
            VideotronConditionNotification::class,
            function (VideotronConditionNotification $notification) use ($applicant) {
                $data = $notification->toArray($applicant);

                return $data['event'] === 'videotron_disruption'
                    && str_contains($data['message'], 'tetap dapat tayang di videotron lain yang Anda pilih')
                    && str_contains($data['message'], 'Videotron Gedung Siber');
            }
        );
    }

    public function test_recovery_notification_is_sent_when_videotron_restores_to_normal(): void
    {
        Notification::fake();

        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Masjid Kampus',
            'location' => 'Depan Masjid',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'gangguan',
            'is_active' => false,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-003',
        ]);
        $submission->videotrons()->attach($videotron->id);

        $videotron->condition = 'normal';
        $videotron->is_active = true;
        $videotron->save();

        $count = $videotron->notifyAffectedSubmissions('recovered');

        $this->assertEquals(1, $count);

        Notification::assertSentTo(
            $applicant,
            VideotronConditionNotification::class,
            function (VideotronConditionNotification $notification) use ($applicant) {
                $data = $notification->toArray($applicant);

                return $data['event'] === 'videotron_recovered'
                    && str_contains($data['title'], 'Kabar Baik')
                    && str_contains($data['message'], 'telah selesai diperbaiki');
            }
        );
    }

    public function test_pengajuan_show_displays_troubled_badge_and_issue_alert(): void
    {
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Masjid Kampus',
            'location' => 'Depan Masjid',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'gangguan',
            'issue_notes' => 'Tersambar petir pada gardu panel',
            'estimated_recovery_date' => now()->addDays(4)->toDateString(),
            'is_active' => false,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-004',
            'title' => 'Milad Kampus UIN',
        ]);
        $submission->videotrons()->attach($videotron->id);

        $response = $this->actingAs($applicant)->get(route('pengajuan.show', $submission->ticket_code).'?drawer=1');

        $response->assertStatus(200);
        $response->assertSee('Kendala Teknis Videotron Masjid Kampus');
        $response->assertSee('Tersambar petir pada gardu panel');
        $response->assertSee('Gangguan');
    }

    public function test_filament_admin_quick_condition_triggers_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);

        $videotron = Videotron::create([
            'name' => 'Videotron Masjid Kampus',
            'location' => 'Depan Masjid',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-005',
        ]);
        $submission->videotrons()->attach($videotron->id);

        $this->actingAs($admin);

        Livewire::test(ListVideotrons::class)
            ->callTableAction('quick_condition', $videotron, [
                'condition' => 'gangguan',
                'issue_notes' => 'Modul samping rusak',
                'estimated_recovery_date' => now()->addDays(2)->toDateString(),
            ])
            ->assertHasNoTableActionErrors();

        $this->assertEquals('gangguan', $videotron->fresh()->condition);

        Notification::assertSentTo(
            $applicant,
            VideotronConditionNotification::class
        );
    }

    public function test_repeated_condition_changes_always_notify_but_identical_edits_are_skipped(): void
    {
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Gedung Siber',
            'location' => 'Lobby Siber',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $submission = $this->createTestSubmission($applicant, [
            'ticket_code' => 'VTR-20261027-006',
        ]);
        $submission->videotrons()->attach($videotron->id);

        $videotron->update(['condition' => 'gangguan', 'is_active' => false]);
        $this->assertEquals(1, $videotron->notifyAffectedSubmissions('disruption'));

        // Admin hanya mengoreksi catatan dengan status yang sama: dilewati
        $videotron->update(['issue_notes' => 'Koreksi typo catatan']);
        $this->assertEquals(0, $videotron->notifyAffectedSubmissions('disruption'));

        $this->travel(1)->seconds();
        $videotron->update(['condition' => 'normal', 'is_active' => true, 'issue_notes' => null]);
        $this->assertEquals(1, $videotron->notifyAffectedSubmissions('recovered'));

        // Rusak lagi beberapa menit kemudian: tetap dikirim
        $this->travel(1)->seconds();
        $videotron->update(['condition' => 'gangguan', 'is_active' => false]);
        $this->assertEquals(1, $videotron->notifyAffectedSubmissions('disruption'));

        // Berubah dari gangguan ke dalam perbaikan: dikirim karena kondisinya berubah
        $this->travel(1)->seconds();
        $videotron->update(['condition' => 'dalam_perbaikan']);
        $this->assertEquals(1, $videotron->notifyAffectedSubmissions('disruption'));

        // Total 4 notifikasi kondisi (tidak ada submitted notification)
        $this->assertEquals(4, $applicant->notifications()->count());
    }

    public function test_notifications_from_one_event_share_batch_id_in_polling_endpoint(): void
    {
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Gedung Siber',
            'location' => 'Lobby Siber',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        foreach (['VTR-20261027-007', 'VTR-20261027-008'] as $ticketCode) {
            $this->createTestSubmission($applicant, ['ticket_code' => $ticketCode])
                ->videotrons()
                ->attach($videotron->id);
        }

        // Tandai notifikasi "submitted" awal agar fokus hanya pada notifikasi kendala
        $applicant->unreadNotifications()->update(['read_at' => now()]);

        $this->travel(1)->seconds();
        $videotron->update(['condition' => 'gangguan', 'is_active' => false]);
        $this->assertEquals(2, $videotron->notifyAffectedSubmissions('disruption'));

        $response = $this->actingAs($applicant)->getJson(route('notifikasi.checkUnread'));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'notifications');
        $response->assertJsonPath('notifications.0.videotron_name', 'Videotron Gedung Siber');

        $batchIds = collect($response->json('notifications'))->pluck('batch_id')->unique();
        $this->assertCount(1, $batchIds);
        $this->assertNotNull($batchIds->first());
        $this->assertIsInt($response->json('notifications.0.age_seconds'));
    }
}
