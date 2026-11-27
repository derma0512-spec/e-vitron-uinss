<?php

namespace Tests\Feature;

use App\Filament\Resources\SubmissionResource;
use App\Filament\Resources\SubmissionResource\Pages\ListSubmissions;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use App\Services\SubmissionDecisionService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionAdminMasterDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_submission_resource_explicitly_disallows_create_and_edit(): void
    {
        $this->assertFalse(SubmissionResource::canCreate(), 'canCreate must return false');

        $submission = Submission::first();
        $this->assertFalse(SubmissionResource::canEdit($submission), 'canEdit must return false');
    }

    public function test_admin_can_access_submissions_index_and_page_has_no_add_or_edit_buttons(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/submissions');

        $response->assertSuccessful();
        $response->assertSee('Pengajuan');
        $response->assertSee('Daftar Pengajuan');

        // Detail panel tertutup secara default saat reload/buka halaman awal
        $response->assertDontSee('Detail Pengajuan');

        // Confirm absence of Add / Create and Edit features
        $response->assertDontSee('+ Pengajuan Baru');
        $response->assertDontSee('Tambah Pengajuan Baru');
        $response->assertDontSee('Edit Data');

        // Panel detail baru muncul saat dibuka dengan parameter selected
        $first = Submission::first();
        $responseWithSelected = $this->actingAs($admin)->get("/admin/submissions?selected={$first->id}");
        $responseWithSelected->assertSuccessful();
        $responseWithSelected->assertSee('Detail Pengajuan');
    }

    public function test_livewire_can_switch_selected_submission_and_tabs(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submissions = Submission::latest()->take(2)->get();
        $first = $submissions[0];
        $second = $submissions[1];

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->assertSet('selectedSubmissionId', null)
            ->call('selectSubmission', $first->id)
            ->assertSet('selectedSubmissionId', $first->id)
            ->call('selectSubmission', $second->id)
            ->assertSet('selectedSubmissionId', $second->id)
            ->call('setTab', 'riwayat')
            ->assertSet('activeTab', 'riwayat')
            ->call('closeDetail')
            ->assertSet('selectedSubmissionId', null);
    }

    public function test_livewire_can_reset_filters(): void
    {
        $admin = User::where('role', 'admin')->first();

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->set('search', 'Keyword')
            ->set('statusFilter', 'menunggu')
            ->set('dateFilter', 'today')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('statusFilter', 'all')
            ->assertSet('dateFilter', 'all');
    }

    public function test_decision_service_approves_submission_and_logs_history(): void
    {
        $service = app(SubmissionDecisionService::class);
        $submission = Submission::where('status', 'menunggu')->first();

        // Ensure videotron is active and normal
        $videotron = $submission->videotrons()->first();
        if (! $videotron) {
            $videotron = Videotron::first();
            $submission->videotrons()->attach($videotron->id);
        }
        $videotron->update(['is_active' => true, 'condition' => 'normal']);
        $submission->unsetRelation('videotrons');

        $service->approve($submission, 'Disetujui untuk tayang');

        $this->assertEquals('disetujui', $submission->fresh()->status);
        $this->assertDatabaseHas('submission_status_histories', [
            'submission_id' => $submission->id,
            'to_status' => 'disetujui',
            'notes' => 'Disetujui untuk tayang',
        ]);
    }

    public function test_decision_service_allows_concurrent_submissions_on_same_videotron_and_date(): void
    {
        $service = app(SubmissionDecisionService::class);
        $videotron = Videotron::where('is_active', true)->where('condition', 'normal')->first();

        // Create an existing approved submission for 2026-10-20 to 2026-10-25
        $existing = Submission::create([
            'ticket_code' => 'VTR-EXISTING-1',
            'user_id' => User::first()->id,
            'title' => 'Konten Sebelumnya',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '081234567890',
            'organization' => 'Unit Pustikom',
            'gdrive_link' => 'https://drive.google.com/sample-existing',
            'description' => 'Materi sudah jalan',
            'content_type' => 'poster',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-25',
            'status' => 'disetujui',
        ]);
        $existing->videotrons()->attach($videotron->id);

        // New submission on overlapping dates (e.g. 2026-10-21 to 2026-10-24)
        $newSubmission = Submission::create([
            'ticket_code' => 'VTR-NEW-CONCURRENT',
            'user_id' => User::first()->id,
            'title' => 'Konten Baru Bersamaan',
            'pic_name' => 'Rizky Budianto',
            'whatsapp' => '089876543210',
            'organization' => 'HIMA TI',
            'gdrive_link' => 'https://drive.google.com/sample-new',
            'description' => 'Materi baru di tanggal yang sama',
            'content_type' => 'video',
            'video_duration_seconds' => 30,
            'start_date' => '2026-10-21',
            'end_date' => '2026-10-24',
            'status' => 'menunggu',
        ]);
        $newSubmission->videotrons()->attach($videotron->id);

        $check = $service->checkAvailabilityAndConflicts($newSubmission);
        $this->assertTrue($check['can_approve'], 'Concurrent broadcasts must be allowed to be approved');
        $this->assertTrue($check['conflicts']->contains('id', $existing->id));

        // Approval must succeed without throwing an exception
        $service->approve($newSubmission, 'Disetujui untuk rotasi playlist');
        $this->assertEquals('disetujui', $newSubmission->fresh()->status);
    }

    public function test_decision_service_rejects_approval_if_videotron_is_in_trouble(): void
    {
        $service = app(SubmissionDecisionService::class);
        $submission = Submission::where('status', 'menunggu')->first();

        $videotron = $submission->videotrons()->first();
        if (! $videotron) {
            $videotron = Videotron::first();
            $submission->videotrons()->attach($videotron->id);
        }
        $videotron->update(['condition' => 'gangguan']);
        $submission->unsetRelation('videotrons');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessageMatches('/gangguan/i');

        $service->approve($submission, 'Coba setujui');
    }

    public function test_decision_service_requests_revision_with_mandatory_feedback(): void
    {
        $service = app(SubmissionDecisionService::class);
        $submission = Submission::where('status', 'menunggu')->first();

        $feedback = 'Mohon perbaiki rasio poster menjadi 16:9 sesuai resolusi layar.';
        $service->requestRevision($submission, $feedback);

        $this->assertEquals('revisi', $submission->fresh()->status);
        $this->assertDatabaseHas('submission_revisions', [
            'submission_id' => $submission->id,
            'admin_feedback' => $feedback,
        ]);
    }

    public function test_decision_service_rejects_submission_with_mandatory_reason(): void
    {
        $service = app(SubmissionDecisionService::class);
        $submission = Submission::where('status', 'menunggu')->first();

        $reason = 'Materi mengandung unsur promosi komersial pihak luar tanpa izin resmi.';
        $service->reject($submission, $reason);

        $this->assertEquals('ditolak', $submission->fresh()->status);
        $this->assertDatabaseHas('submission_status_histories', [
            'submission_id' => $submission->id,
            'to_status' => 'ditolak',
            'notes' => $reason,
        ]);
    }

    public function test_approved_submission_can_be_marked_as_downloaded_without_changing_status_to_selesai(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'disetujui')->first();

        $this->assertNull($submission->material_downloaded_at);

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->call('markMaterialDownloaded')
            ->assertNotified();

        $fresh = $submission->fresh();
        $this->assertNotNull($fresh->material_downloaded_at);
        $this->assertEquals($admin->id, $fresh->material_downloaded_by);
        $this->assertEquals('disetujui', $fresh->status, 'Status must remain disetujui and not change to selesai automatically');
    }

    public function test_marking_material_downloaded_with_status_histories_updates_drawer_on_first_click(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::where('status', 'disetujui')->first();

        $submission->statusHistories()->create([
            'user_id' => $submission->user_id,
            'from_status' => null,
            'to_status' => 'menunggu',
            'notes' => 'Pengajuan baru berhasil dikirimkan oleh pengaju.',
        ]);
        $submission->statusHistories()->create([
            'user_id' => $admin->id,
            'from_status' => 'menunggu',
            'to_status' => 'disetujui',
            'notes' => 'Pengajuan telah diperiksa dan disetujui.',
        ]);

        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->assertSee('Tandai Materi Sudah Diunduh')
            ->call('markMaterialDownloaded')
            ->assertNotified()
            ->assertSee('Materi Siap / Sudah Diunduh')
            ->assertSee("oleh {$admin->name}")
            ->assertDontSee('Tandai Materi Sudah Diunduh');

        $fresh = $submission->fresh();
        $this->assertNotNull($fresh->material_downloaded_at);
        $this->assertSame($admin->id, $fresh->material_downloaded_by);
    }

    public function test_legacy_admin_routes_redirect_to_unified_submissions_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::first();

        $this->actingAs($admin)
            ->get('/admin/schedule-preparation')
            ->assertRedirect('/admin/submissions?statusFilter=disetujui');

        $this->actingAs($admin)
            ->get("/admin/submissions/{$submission->id}/edit")
            ->assertRedirect("/admin/submissions?selected={$submission->id}");
    }

    public function test_drawer_contains_scroll_lock_and_overscroll_containment_attributes(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::first();

        $response = $this->actingAs($admin)->get("/admin/submissions?selected={$submission->id}");
        $response->assertSuccessful();

        // 1. Verify CSS rules for scroll-lock and overscroll-behavior
        $response->assertSee('html.sipetron-drawer-locked', false);
        $response->assertSee('html.sipetron-drawer-locked body', false);
        $response->assertSee('overflow: hidden !important', false);
        $response->assertSee('--sipetron-scrollbar-width', false);
        $response->assertSee('overscroll-behavior: contain', false);

        // 2. Verify Alpine drawer wrapper lifecycle
        $response->assertSee('wire:key="sipetron-drawer-root"', false);
        $response->assertSee('applyLock()', false);
        $response->assertSee('removeLock()', false);
        $response->assertSee('@keydown.escape.window="handleEscape()"', false);

        // 3. Verify backdrop wheel and touch isolation
        $response->assertSee('@wheel.prevent.stop', false);
        $response->assertSee('@touchmove.prevent.stop', false);

        // 4. Verify drawer is not rendered when no submission is selected
        $responseWithoutSelected = $this->actingAs($admin)->get('/admin/submissions');
        $responseWithoutSelected->assertSuccessful();
        $responseWithoutSelected->assertDontSee('wire:key="sipetron-drawer-root"', false);
        $responseWithoutSelected->assertDontSee('Detail Pengajuan');

        // 5. Verify Livewire can open and close submission state
        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->assertSet('selectedSubmissionId', null)
            ->call('selectSubmission', $submission->id)
            ->assertSet('selectedSubmissionId', $submission->id)
            ->call('closeDetail')
            ->assertSet('selectedSubmissionId', null);
    }

    public function test_normal_reload_with_drawer_keeps_all_submissions_in_table(): void
    {
        $admin = User::where('role', 'admin')->first();
        $submission = Submission::first();

        // 1. From notification / external link: ?selected=1 -> fromNotification is true (1 row)
        $response = $this->actingAs($admin)->get("/admin/submissions?selected={$submission->id}");
        $response->assertSuccessful();
        $response->assertSee('Detail Pengajuan');

        Livewire::withQueryParams(['selected' => $submission->id])
            ->actingAs($admin)
            ->test(ListSubmissions::class)
            ->assertSet('fromNotification', true)
            ->assertSet('selectedSubmissionId', $submission->id)
            // Closing detail drawer restores full table
            ->call('closeDetail')
            ->assertSet('fromNotification', false)
            ->assertSet('selectedSubmissionId', null);

        // 2. Normal reload while browsing table: ?drawer=1 -> fromNotification is false (all rows)
        Livewire::withQueryParams(['drawer' => $submission->id])
            ->actingAs($admin)
            ->test(ListSubmissions::class)
            ->assertSet('fromNotification', false)
            ->assertSet('selectedSubmissionId', $submission->id);

        // 3. Clicking row in table sets drawer state without fromNotification
        Livewire::actingAs($admin)
            ->test(ListSubmissions::class)
            ->call('selectSubmission', $submission->id)
            ->assertSet('fromNotification', false)
            ->assertSet('selectedSubmissionId', $submission->id);
    }

    public function test_approving_submission_keeps_its_row_position_stable(): void
    {
        $admin = User::where('role', 'admin')->first();
        $videotron = Videotron::where('is_active', true)->where('condition', 'normal')->first();

        // Bersihkan data lama untuk isolasi tes urutan baris
        Submission::query()->delete();

        $sub1 = Submission::create([
            'ticket_code' => 'VTR-STABLE-0001',
            'user_id' => $admin->id,
            'title' => 'Pengajuan Baris 1',
            'pic_name' => 'PIC 1',
            'whatsapp' => '081234567891',
            'organization' => 'Unit 1',
            'content_type' => 'poster',
            'description' => 'Deskripsi 1',
            'gdrive_link' => 'https://drive.google.com/sample1',
            'start_date' => today()->addDays(2)->toDateString(),
            'end_date' => today()->addDays(4)->toDateString(),
            'status' => 'menunggu',
            'created_at' => now()->subMinutes(10),
        ]);
        $sub1->videotrons()->attach($videotron->id);

        $sub2 = Submission::create([
            'ticket_code' => 'VTR-STABLE-0002',
            'user_id' => $admin->id,
            'title' => 'Pengajuan Baris 2',
            'pic_name' => 'PIC 2',
            'whatsapp' => '081234567892',
            'organization' => 'Unit 2',
            'content_type' => 'poster',
            'description' => 'Deskripsi 2',
            'gdrive_link' => 'https://drive.google.com/sample2',
            'start_date' => today()->addDays(2)->toDateString(),
            'end_date' => today()->addDays(4)->toDateString(),
            'status' => 'menunggu',
            'created_at' => now()->subMinutes(20),
        ]);
        $sub2->videotrons()->attach($videotron->id);

        $sub3 = Submission::create([
            'ticket_code' => 'VTR-STABLE-0003',
            'user_id' => $admin->id,
            'title' => 'Pengajuan Baris 3',
            'pic_name' => 'PIC 3',
            'whatsapp' => '081234567893',
            'organization' => 'Unit 3',
            'content_type' => 'poster',
            'description' => 'Deskripsi 3',
            'gdrive_link' => 'https://drive.google.com/sample3',
            'start_date' => today()->addDays(2)->toDateString(),
            'end_date' => today()->addDays(4)->toDateString(),
            'status' => 'menunggu',
            'created_at' => now()->subMinutes(30),
        ]);
        $sub3->videotrons()->attach($videotron->id);

        // Sebelum disetujui, ambil urutan baris awal
        $livewire = Livewire::actingAs($admin)
            ->test(ListSubmissions::class);

        $initialIds = collect($livewire->get('submissions')->items())->pluck('id')->toArray();
        $this->assertCount(3, $initialIds);
        $thirdRowId = $initialIds[2];

        // Admin memilih baris ke-3 dan menyetujuinya
        $livewire->call('selectSubmission', $thirdRowId)
            ->call('confirmApprove');

        // Setelah disetujui, status baris ke-3 menjadi disetujui
        $this->assertEquals('disetujui', Submission::find($thirdRowId)->status);

        // Posisi baris TETAP di baris ke-3: urutan array tidak berubah/loncat sama sekali
        $afterIds = collect($livewire->get('submissions')->items())->pluck('id')->toArray();
        $this->assertSame($initialIds, $afterIds);
        $this->assertEquals($thirdRowId, $afterIds[2]);
    }
}
