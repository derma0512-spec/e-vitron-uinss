<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionWizardFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Videotron $videotron1;

    protected Videotron $videotronBroken;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create([
            'name' => 'Ahmad Pengaju',
            'email' => 'ahmad@student.uin.ac.id',
            'phone' => '081234567890',
            'unit_or_prodi' => 'Teknik Informatika',
            'role' => 'mahasiswa',
        ]);

        $this->videotron1 = Videotron::create([
            'name' => 'Videotron Gerbang Utama',
            'location' => 'Kampus 1',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $this->videotronBroken = Videotron::create([
            'name' => 'Videotron Rusak',
            'location' => 'Gedung Lama',
            'resolution' => '1080x1920',
            'orientation' => 'portrait',
            'condition' => 'gangguan',
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_access_submission_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('Ajukan Penayangan');
        $response->assertSee('Pilih Videotron');
        $response->assertSee($this->videotron1->name);
    }

    public function test_redirects_to_pengajuan_index_with_banner_when_service_is_inactive(): void
    {
        Setting::updateOrCreate(
            ['key' => 'service_active'],
            ['value' => '0', 'type' => 'boolean', 'group' => 'service']
        );
        Setting::updateOrCreate(
            ['key' => 'service_closed_message'],
            ['value' => 'Sedang pemeliharaan server tahunan.', 'type' => 'string', 'group' => 'service']
        );

        $response = $this->actingAs($this->user)->get(route('pengajuan.create'));

        $response->assertRedirect(route('pengajuan.index'));

        $indexResponse = $this->actingAs($this->user)->get(route('pengajuan.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Perhatian:');
        $indexResponse->assertSee('Layanan Ditutup');
        $indexResponse->assertSee('Sedang pemeliharaan server tahunan.');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('pengajuan.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_submission_fails_when_required_fields_missing(): void
    {
        $response = $this->actingAs($this->user)->post(route('pengajuan.store'), []);

        $response->assertSessionHasErrors([
            'videotrons',
            'start_date',
            'end_date',
            'title',
            'content_type',
            'description',
            'pic_name',
            'whatsapp',
            'gdrive_link',
            'surat_peminjaman',
        ]);
    }

    public function test_submission_fails_with_broken_or_inactive_videotron(): void
    {
        $response = $this->actingAs($this->user)->post(route('pengajuan.store'), [
            'videotrons' => [$this->videotronBroken->id],
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'title' => 'Pengumuman Seminar',
            'content_type' => 'poster',
            'description' => 'Deskripsi kegiatan publikasi',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-folder',
            'surat_peminjaman' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors(['videotrons']);
    }

    public function test_submission_fails_when_video_duration_is_missing_for_video(): void
    {
        $response = $this->actingAs($this->user)->post(route('pengajuan.store'), [
            'videotrons' => [$this->videotron1->id],
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'title' => 'Teaser Video Dies Natalis',
            'content_type' => 'video',
            'video_duration_seconds' => null,
            'description' => 'Teaser video dies natalis kampus',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/video-teaser',
            'surat_peminjaman' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors(['video_duration_seconds']);
    }

    public function test_successful_poster_submission_creates_record_and_redirects_with_step5_data(): void
    {
        $response = $this->actingAs($this->user)->post(route('pengajuan.store'), [
            'videotrons' => [$this->videotron1->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'title' => 'Workshop AI & Data Science 2026',
            'content_type' => 'poster',
            'description' => 'Pendaftaran workshop AI terbuka untuk seluruh mahasiswa UIN SSC.',
            'pic_name' => 'Ahmad Pengaju',
            'organization' => 'HIMA TI',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/workshop-ai-2026',
            'surat_peminjaman' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pengajuan.create'));
        $response->assertSessionHas('submission_success');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $this->user->id,
            'title' => 'Workshop AI & Data Science 2026',
            'content_type' => 'poster',
            'status' => 'menunggu',
            'video_duration_seconds' => null,
        ]);

        $submission = Submission::where('title', 'Workshop AI & Data Science 2026')->first();
        $this->assertNotNull($submission);
        $this->assertStringStartsWith('VTR-', $submission->ticket_code);
        $this->assertTrue($submission->videotrons->contains($this->videotron1->id));
        $this->assertDatabaseHas('submission_status_histories', [
            'submission_id' => $submission->id,
            'to_status' => 'menunggu',
        ]);
    }

    public function test_user_can_view_submission_index_page(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0099',
            'title' => 'Seminar Teknologi Masa Depan',
            'content_type' => 'poster',
            'description' => 'Seminar tahunan',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
        ]);
        $submission->videotrons()->attach($this->videotron1->id);

        $response = $this->actingAs($this->user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Saya');
        $response->assertSee('Seminar Teknologi Masa Depan');
        $response->assertSee('VTR-202610-0099');
        $response->assertSee('Buat Pengajuan Baru');
    }

    public function test_user_can_filter_submissions_by_status(): void
    {
        Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0101',
            'title' => 'Konten Disetujui',
            'content_type' => 'poster',
            'description' => 'Test',
            'pic_name' => 'Ahmad',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'disetujui',
        ]);

        Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0102',
            'title' => 'Konten Menunggu',
            'content_type' => 'poster',
            'description' => 'Test',
            'pic_name' => 'Ahmad',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->user)->get(route('pengajuan.index', ['status' => 'disetujui']));

        $response->assertStatus(200);
        $response->assertViewHas('submissions', function ($submissions) {
            return $submissions->contains('ticket_code', 'VTR-202610-0101')
                && ! $submissions->contains('ticket_code', 'VTR-202610-0102');
        });
        $response->assertSee('VTR-202610-0101');
    }

    public function test_user_can_cancel_pending_submission(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0200',
            'title' => 'Pengajuan Akan Dibatalkan',
            'content_type' => 'poster',
            'description' => 'Test',
            'pic_name' => 'Ahmad',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->user)->post(route('pengajuan.cancel', $submission->ticket_code));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals('dibatalkan', $submission->status);
        $this->assertDatabaseHas('submission_status_histories', [
            'submission_id' => $submission->id,
            'to_status' => 'dibatalkan',
        ]);
    }

    public function test_user_can_fetch_submission_detail_drawer_partial(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0300',
            'title' => 'Pengajuan Slide Over Drawer',
            'content_type' => 'poster',
            'description' => 'Materi poster untuk slide over drawer',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-drawer',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'status' => 'menunggu',
        ]);
        $submission->videotrons()->attach($this->videotron1->id);

        $response = $this->actingAs($this->user)->get(route('pengajuan.show', $submission->ticket_code).'?drawer=1');

        $response->assertOk();
        $response->assertViewIs('pengajuan.partials.detail-drawer');
        $response->assertSee($submission->ticket_code);
        $response->assertSee('Pengajuan Slide Over Drawer');
        $response->assertSee('Menunggu Pemeriksaan Admin');
    }

    public function test_direct_submission_url_redirects_to_index_with_open_drawer(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0301',
            'title' => 'Pengajuan Standalone Redirect',
            'content_type' => 'video',
            'video_duration_seconds' => 120,
            'description' => 'Materi video',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-standalone',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);
        $submission->videotrons()->attach($this->videotron1->id);

        $response = $this->actingAs($this->user)->get(route('pengajuan.show', $submission->ticket_code));

        $response->assertRedirect(route('pengajuan.index', [
            'open' => $submission->ticket_code,
            'status' => 'menunggu',
        ]));
    }

    public function test_user_can_print_official_proof_pdf(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0302',
            'title' => 'Pengajuan Cetak Bukti PDF',
            'content_type' => 'poster',
            'description' => 'Materi cetak PDF',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-print-proof',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
        ]);
        $submission->videotrons()->attach($this->videotron1->id);

        $response = $this->actingAs($this->user)->get(route('pengajuan.print', $submission->ticket_code));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename="bukti-pengajuan-'.strtolower($submission->ticket_code).'.pdf"');
    }

    public function test_user_can_cancel_submission_via_json_request(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0401',
            'title' => 'Pengajuan Batal Ajax JSON',
            'content_type' => 'poster',
            'description' => 'Test',
            'pic_name' => 'Ahmad',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-json',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pengajuan.cancel', $submission->ticket_code));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Pengajuan berhasil dibatalkan.',
            'ticket_code' => $submission->ticket_code,
        ]);

        $this->assertEquals('dibatalkan', $submission->fresh()->status);
    }

    public function test_user_cannot_cancel_non_menunggu_submission(): void
    {
        $submission = Submission::create([
            'user_id' => $this->user->id,
            'ticket_code' => 'VTR-202610-0402',
            'title' => 'Pengajuan Disetujui Dilarang Batal',
            'content_type' => 'poster',
            'description' => 'Test',
            'pic_name' => 'Ahmad',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test-disetujui',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pengajuan.cancel', $submission->ticket_code));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Hanya pengajuan dengan status Menunggu yang dapat dibatalkan.',
        ]);

        $this->assertEquals('disetujui', $submission->fresh()->status);
    }

    public function test_compact_seven_slot_pagination_prevents_long_page_lists_in_portal_and_admin(): void
    {
        // 1. Total 6 halaman -> semua nomor tampil tanpa elipsis
        $sixPages = new LengthAwarePaginator(range(1, 9), 54, 9, 1, ['path' => '/pengajuan']);
        $portalSixHtml = view('vendor.pagination.sipetron', ['paginator' => $sixPages])->render();
        $this->assertStringContainsString('>1<', $portalSixHtml);
        $this->assertStringContainsString('>6<', $portalSixHtml);
        $this->assertStringNotContainsString('&hellip;', $portalSixHtml);

        // 2. Total 10 halaman (di halaman 1) -> 1 2 3 4 5 ... 10 (tidak memanjang 6-9)
        $tenPagesStart = new LengthAwarePaginator(range(1, 9), 90, 9, 1, ['path' => '/pengajuan']);
        $portalTenStartHtml = view('vendor.pagination.sipetron', ['paginator' => $tenPagesStart])->render();
        $this->assertStringContainsString('aria-current="page">1<', $portalTenStartHtml);
        $this->assertStringContainsString('>5<', $portalTenStartHtml);
        $this->assertStringContainsString('&hellip;', $portalTenStartHtml);
        $this->assertStringContainsString('>10<', $portalTenStartHtml);
        $this->assertStringNotContainsString('page=6"', $portalTenStartHtml);
        $this->assertStringNotContainsString('page=9"', $portalTenStartHtml);

        // 3. Total 10 halaman (di halaman 5) -> 1 ... 4 5 6 ... 10
        $tenPagesMid = new LengthAwarePaginator(range(1, 9), 90, 9, 5, ['path' => '/pengajuan']);
        $portalTenMidHtml = view('vendor.pagination.sipetron', ['paginator' => $tenPagesMid])->render();
        $this->assertStringContainsString('aria-current="page">5<', $portalTenMidHtml);
        $this->assertStringContainsString('page=4"', $portalTenMidHtml);
        $this->assertStringContainsString('page=6"', $portalTenMidHtml);
        $this->assertStringContainsString('page=10"', $portalTenMidHtml);
        $this->assertStringNotContainsString('page=2"', $portalTenMidHtml);
        $this->assertStringNotContainsString('page=8"', $portalTenMidHtml);
        $this->assertSame(2, substr_count($portalTenMidHtml, '&hellip;'));

        // 4. Livewire Admin pagination di 20 halaman (halaman 20) -> 1 ... 16 17 18 19 20
        $twentyPagesEnd = new LengthAwarePaginator(range(1, 10), 200, 10, 20, ['path' => '/admin/submissions']);
        $adminTwentyEndHtml = view('vendor.pagination.sipetron-livewire', ['paginator' => $twentyPagesEnd])->render();
        $this->assertStringContainsString("gotoPage(1, 'page')", $adminTwentyEndHtml);
        $this->assertStringContainsString('&hellip;', $adminTwentyEndHtml);
        $this->assertStringContainsString("gotoPage(16, 'page')", $adminTwentyEndHtml);
        $this->assertStringContainsString('>20<', $adminTwentyEndHtml);
        $this->assertStringNotContainsString("gotoPage(10, 'page')", $adminTwentyEndHtml);
    }

    public function test_video_duration_limit_and_error_message_sync_with_admin_settings(): void
    {
        $basePayload = [
            'videotrons' => [$this->videotron1->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => 'Uji Sinkronisasi Durasi Video',
            'content_type' => 'video',
            'description' => 'Pengujian sinkronisasi batas durasi video dengan pengaturan admin.',
            'pic_name' => 'Ahmad Pengaju',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/video-sync-test',
        ];

        // 1. Batas default 30 detik: wizard default ke detik (30), 31 detik ditolak pesan dinamis 30 detik
        Setting::set('max_video_duration', 30, 'integer', 'policy');

        $wizard30 = $this->actingAs($this->user)->get(route('pengajuan.create'));
        $wizard30->assertOk();
        $wizard30->assertSee('maxVideoDurationSeconds: 30', false);
        $wizard30->assertSee('maxVideoDurationLabel: "30 detik"', false);

        $this->actingAs($this->user)->post(route('pengajuan.store'), array_merge($basePayload, [
            'video_duration_seconds' => 31,
            'surat_peminjaman' => UploadedFile::fake()->create('surat-31.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors([
            'video_duration_seconds' => 'Durasi video maksimal 30 detik.',
        ]);

        $this->actingAs($this->user)->post(route('pengajuan.store'), array_merge($basePayload, [
            'video_duration_seconds' => 30,
            'surat_peminjaman' => UploadedFile::fake()->create('surat-30.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasNoErrors()->assertRedirect(route('pengajuan.create'));

        // 2. Batas diubah admin ke 300 detik (5 menit): 240 detik diterima, 301 detik ditolak pesan 5 menit
        Setting::set('max_video_duration', 300, 'integer', 'policy');

        $wizard300 = $this->actingAs($this->user)->get(route('pengajuan.create'));
        $wizard300->assertOk();
        $wizard300->assertSee('maxVideoDurationSeconds: 300', false);
        $wizard300->assertSee('maxVideoDurationLabel: "5 menit (300 detik)"', false);

        $this->actingAs($this->user)->post(route('pengajuan.store'), array_merge($basePayload, [
            'video_duration_seconds' => 301,
            'surat_peminjaman' => UploadedFile::fake()->create('surat-301.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors([
            'video_duration_seconds' => 'Durasi video maksimal 5 menit (300 detik).',
        ]);

        $this->actingAs($this->user)->post(route('pengajuan.store'), array_merge($basePayload, [
            'video_duration_seconds' => 240,
            'surat_peminjaman' => UploadedFile::fake()->create('surat-240.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasNoErrors()->assertRedirect(route('pengajuan.create'));
    }
}
