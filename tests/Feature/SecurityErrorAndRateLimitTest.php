<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityErrorAndRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_existent_url_renders_custom_404_page(): void
    {
        $response = $this->get('/halaman-fuzzing-hacker-ngawur-404');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('404');
        $response->assertSee('E-VITRON');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_login_rate_limiter_blocks_brute_force_attempts(): void
    {
        RateLimiter::clear('login');

        // Lakukan 5 percobaan salah
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'identifier' => 'attacker@target.com',
                'password' => 'wrongpassword'.$i,
            ]);
            $response->assertSessionHas('error');
        }

        // Percobaan ke-6 harus diblokir oleh Rate Limiter (HTTP 429)
        $blockedResponse = $this->post('/login', [
            'identifier' => 'attacker@target.com',
            'password' => 'wrongpassword6',
        ]);

        $blockedResponse->assertStatus(429);
        $blockedResponse->assertSee('Terlalu Banyak Percobaan');
        $blockedResponse->assertSee('429');
    }

    public function test_custom_403_view_renders_correctly(): void
    {
        $response = $this->view('errors.403');

        $response->assertSee('Akses Tidak Diizinkan');
        $response->assertSee('403');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_custom_419_view_renders_correctly(): void
    {
        $response = $this->view('errors.419');

        $response->assertSee('Sesi Telah Berakhir');
        $response->assertSee('419');
        $response->assertSee('Muat Ulang Halaman');
    }

    public function test_custom_500_view_renders_correctly(): void
    {
        $response = $this->view('errors.500');

        $response->assertSee('Terjadi Kendala Teknis');
        $response->assertSee('500');
    }

    public function test_custom_503_view_renders_correctly(): void
    {
        $response = $this->view('errors.503');

        $response->assertSee('Sistem Sedang Dipelihara');
        $response->assertSee('503');
    }

    public function test_owasp_a01_surat_peminjaman_is_stored_privately_and_enforces_strict_authorization(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'pengaju']);
        $otherUser = User::factory()->create(['role' => 'pengaju']);
        $admin = User::factory()->create(['role' => 'admin']);

        $videotron = Videotron::create([
            'name' => 'Videotron Rektorat',
            'location' => 'Gedung Rektorat',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'is_active' => true,
            'condition' => 'normal',
        ]);

        $pdfFile = UploadedFile::fake()->create('surat-resmi.pdf', 120, 'application/pdf');

        $this->actingAs($owner)->post(route('pengajuan.store'), [
            'videotrons' => [$videotron->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => 'Seminar Keamanan Siber Kampus',
            'content_type' => 'poster',
            'description' => 'Edukasi keamanan informasi bagi mahasiswa.',
            'pic_name' => 'Budi Santoso',
            'organization' => 'BEM UIN SSC',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/cyber-sec-2026',
            'surat_peminjaman' => $pdfFile,
        ])->assertRedirect(route('pengajuan.create'));

        $submission = Submission::where('user_id', $owner->id)->firstOrFail();

        // Pastikan berkas tersimpan di disk privat (local), BUKAN di disk public
        Storage::disk('local')->assertExists($submission->surat_peminjaman);
        Storage::disk('public')->assertMissing($submission->surat_peminjaman);

        // 1. Pemilik pengajuan dapat mengakses surat
        $ownerResponse = $this->actingAs($owner)->get(route('pengajuan.surat', $submission->ticket_code));
        $ownerResponse->assertOk();
        $ownerResponse->assertHeader('Content-Type', 'application/pdf');
        $ownerResponse->assertHeader('X-Content-Type-Options', 'nosniff');

        // 2. Admin dapat mengakses surat
        $adminResponse = $this->actingAs($admin)->get(route('pengajuan.surat', $submission->ticket_code));
        $adminResponse->assertOk();

        // 3. Pengaju lain (IDOR attempt) ditolak dengan 403 Forbidden
        $otherResponse = $this->actingAs($otherUser)->get(route('pengajuan.surat', $submission->ticket_code));
        $otherResponse->assertForbidden();

        // 4. Tamu belum login diarahkan ke halaman login
        $this->post('/logout');
        $guestResponse = $this->get(route('pengajuan.surat', $submission->ticket_code));
        $guestResponse->assertRedirect(route('login'));
    }

    public function test_owasp_a03_submission_sanitizes_xss_and_script_injection_inputs(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => 'pengaju']);
        $videotron = Videotron::create([
            'name' => 'Videotron Utama',
            'location' => 'Gerbang Utama',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'is_active' => true,
            'condition' => 'normal',
        ]);

        $this->actingAs($user)->post(route('pengajuan.store'), [
            'videotrons' => [$videotron->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => '<script>alert("hacked")</script>Festival Budaya Islam <img src=x onerror=alert(1)>',
            'content_type' => 'poster',
            'description' => '<iframe src="https://evil.example.com"></iframe>Kegiatan tahunan mahasiswa.',
            'pic_name' => '<b>Ahmad</b> <script>steal()</script>Fadhil',
            'organization' => '<a href="javascript:alert(1)">UKM Seni</a>',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/festival-2026',
            'surat_peminjaman' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('pengajuan.create'));

        $submission = Submission::where('user_id', $user->id)->firstOrFail();

        $this->assertSame('Festival Budaya Islam', $submission->title);
        $this->assertSame('Kegiatan tahunan mahasiswa.', $submission->description);
        $this->assertSame('Ahmad Fadhil', $submission->pic_name);
        $this->assertSame('UKM Seni', $submission->organization);
        $this->assertStringNotContainsString('<script>', $submission->title);
        $this->assertStringNotContainsString('<iframe', $submission->description);
    }

    public function test_owasp_a04_rejects_disguised_double_extension_web_shell_upload(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => 'pengaju']);
        $videotron = Videotron::create([
            'name' => 'Videotron Siber',
            'location' => 'Gedung Siber',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'is_active' => true,
            'condition' => 'normal',
        ]);

        $disguisedShell = UploadedFile::fake()->create('backdoor.pdf.php', 50, 'application/pdf');

        $response = $this->actingAs($user)->post(route('pengajuan.store'), [
            'videotrons' => [$videotron->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => 'Uji Upload Web Shell',
            'content_type' => 'poster',
            'description' => 'Deskripsi uji coba.',
            'pic_name' => 'Tester',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/test',
            'surat_peminjaman' => $disguisedShell,
        ]);

        $response->assertSessionHasErrors(['surat_peminjaman']);
    }

    public function test_owasp_a01_applicant_cannot_access_admin_recap_print_or_admin_routes(): void
    {
        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Pemohon biasa mencoba mengakses rute rekap cetak admin -> Wajib ditolak 403
        $this->actingAs($applicant)
            ->get(route('admin.history-and-recap.print'))
            ->assertStatus(403);

        $this->actingAs($applicant)
            ->get(route('admin.history-and-recap.export-pdf'))
            ->assertStatus(403);

        $this->actingAs($applicant)
            ->post(route('admin.change-password'), [
                'current_password' => 'password',
                'new_password' => 'newpass123',
                'new_password_confirmation' => 'newpass123',
            ])
            ->assertStatus(403);

        // Admin asli diizinkan mengakses rute rekap cetak admin -> 200 OK
        $this->actingAs($admin)
            ->get(route('admin.history-and-recap.print'))
            ->assertStatus(200);
    }

    public function test_owasp_a05_security_headers_protect_against_clickjacking_and_mime_sniffing(): void
    {
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $loginResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $loginResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $portalResponse = $this->actingAs($applicant)->get(route('pengajuan.index'));
        $portalResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $portalResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $portalResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_owasp_a07_admin_account_with_numeric_identifier_cannot_be_overwritten_via_sso_login(): void
    {
        config([
            'services.bridge_sso.enabled' => true,
            'services.bridge_sso.simulate' => true,
        ]);

        $admin = User::factory()->create([
            'name' => 'Super Admin UIN',
            'email' => 'admin.utama@uinssc.ac.id',
            'identifier' => '198501012010011001',
            'role' => 'admin',
            'password' => bcrypt('SecretAdminPass!99'),
        ]);

        $response = $this->post(route('login.post'), [
            'identifier' => '198501012010011001',
            'password' => 'hijack-password-via-sso',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();

        // Pastikan password & role admin tidak berubah sama sekali
        $admin->refresh();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('Super Admin UIN', $admin->name);
    }

    public function test_owasp_a03_and_a04_registration_blocks_role_escalation_and_sanitizes_xss(): void
    {
        // 1. Mencoba menyisipkan role_type=admin saat daftar -> Wajib ditolak validasi
        $escalationAttempt = $this->post(route('register.post'), [
            'name' => 'Hacker Admin',
            'role_type' => 'admin',
            'unit_or_prodi' => 'Unit Ilegal',
            'email' => 'escalation@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $escalationAttempt->assertSessionHasErrors(['role_type']);
        $this->assertDatabaseMissing('users', ['email' => 'escalation@example.com']);

        // 2. Mencoba menyisipkan tag <script> pada nama & instansi saat daftar -> Wajib disanitasi bersih
        $this->post(route('register.post'), [
            'name' => '<script>alert(1)</script>Rizky Pratama',
            'role_type' => 'mitra',
            'unit_or_prodi' => '<iframe src="evil"></iframe>PT Siber Nusantara',
            'email' => 'rizky.safe@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $registeredUser = User::where('email', 'rizky.safe@example.com')->firstOrFail();
        $this->assertSame('Rizky Pratama', $registeredUser->name);
        $this->assertSame('PT Siber Nusantara', $registeredUser->unit_or_prodi);
        $this->assertSame('mitra', $registeredUser->role);
    }

    public function test_phase_2_runtime_caching_prevents_redundant_database_queries(): void
    {
        Setting::set('service_active', true, 'boolean', 'service');
        Setting::set('min_days_before', 3, 'integer', 'policy');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        $videotron = Videotron::create([
            'name' => 'Videotron Gedung Siber',
            'location' => 'Lobby Utama',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'is_active' => true,
            'condition' => 'normal',
        ]);

        Submission::create([
            'ticket_code' => 'VTR-202610-0001',
            'user_id' => $user->id,
            'title' => 'Pengajuan Uji Performa',
            'content_type' => 'poster',
            'description' => 'Deskripsi uji performa.',
            'pic_name' => 'Mahasiswa Uji',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/perf',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'status' => 'menunggu',
        ]);

        // Warm first call
        Setting::allCached();
        Submission::getDisplayStatusCounts();
        Videotron::getOperationalStats();

        // Subsequent calls in the same request cycle must trigger 0 database queries
        DB::enableQueryLog();
        DB::flushQueryLog();

        for ($i = 0; $i < 15; $i++) {
            setting('service_active');
            setting('min_days_before');
            Submission::getDisplayStatusCounts();
            Videotron::getOperationalStats();
        }

        $executedQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertCount(0, $executedQueries);
    }

    public function test_phase_2_storage_pruning_command_cleans_expired_sessions_and_cache(): void
    {
        DB::table('sessions')->insert([
            [
                'id' => 'expired_session_1',
                'user_id' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'BotCrawler/1.0',
                'payload' => 'dGVzdA==',
                'last_activity' => now()->subDays(2)->getTimestamp(),
            ],
            [
                'id' => 'active_session_1',
                'user_id' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'payload' => 'dGVzdA==',
                'last_activity' => now()->getTimestamp(),
            ],
        ]);

        $this->artisan('sipetron:prune-storage')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('sessions', ['id' => 'expired_session_1']);
        $this->assertDatabaseHas('sessions', ['id' => 'active_session_1']);
    }
}
