<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $loginResponse->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $loginResponse->assertHeaderMissing('Strict-Transport-Security');
        $loginResponse->assertHeaderMissing('X-Powered-By');
        $loginCsp = (string) $loginResponse->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'self'", $loginCsp);
        $this->assertStringContainsString("object-src 'none'", $loginCsp);
        $this->assertStringContainsString("base-uri 'self'", $loginCsp);
        $this->assertStringContainsString("form-action 'self'", $loginCsp);

        $httpsLoginResponse = $this->get('https://localhost/login');
        $httpsLoginResponse->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $applicant = User::factory()->create(['role' => 'mahasiswa']);
        $portalResponse = $this->actingAs($applicant)->get(route('pengajuan.index'));
        $portalResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $portalResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $portalResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $portalResponse->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->assertStringContainsString("object-src 'none'", (string) $portalResponse->headers->get('Content-Security-Policy'));
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

    public function test_owasp_a04_rejects_polyglot_pdf_containing_php_code_on_store_and_revision(): void
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

        // 1. Polyglot PDF (header %PDF- valid namun disisipi <?php) -> Wajib ditolak saat pengajuan baru
        $polyglotPdf = UploadedFile::fake()->createWithContent(
            'surat-polyglot.pdf',
            "%PDF-1.4\n<?php system(\$_GET['cmd']); ?>\n%%EOF"
        );

        $this->actingAs($user)->post(route('pengajuan.store'), [
            'videotrons' => [$videotron->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => 'Uji Polyglot PDF',
            'content_type' => 'poster',
            'description' => 'Pengujian berkas PDF disisipi tag PHP.',
            'pic_name' => 'Tester Keamanan',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/test-polyglot',
            'surat_peminjaman' => $polyglotPdf,
        ])->assertSessionHasErrors(['surat_peminjaman']);

        $this->assertSame(0, Submission::count());

        // 2. Berkas PDF valid asli (diawali %PDF- tanpa tag PHP) -> Wajib diterima
        $validPdf = UploadedFile::fake()->createWithContent(
            'surat-resmi-valid.pdf',
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"
        );

        $this->actingAs($user)->post(route('pengajuan.store'), [
            'videotrons' => [$videotron->id],
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'title' => 'Uji PDF Resmi Valid',
            'content_type' => 'poster',
            'description' => 'Pengujian berkas PDF valid standar ISO.',
            'pic_name' => 'Tester Keamanan',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/test-valid',
            'surat_peminjaman' => $validPdf,
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengajuan.create'));

        $submission = Submission::where('user_id', $user->id)->firstOrFail();

        // 3. Polyglot PDF saat kirim revisi surat -> Wajib ditolak juga
        $submission->update([
            'status' => 'revisi',
            'admin_notes' => 'Mohon perbaiki stempel pada surat permohonan.',
        ]);

        $polyglotRevisionPdf = UploadedFile::fake()->createWithContent(
            'revisi-polyglot.pdf',
            "%PDF-1.7\n<?PHP echo 'backdoor'; ?>\n%%EOF"
        );

        $this->actingAs($user)->post(route('pengajuan.revision', $submission->ticket_code), [
            'user_reply' => 'Surat revisi terlampir.',
            'surat_peminjaman' => $polyglotRevisionPdf,
        ])->assertSessionHasErrors(['surat_peminjaman']);

        $this->assertSame('revisi', $submission->fresh()->status);
    }

    public function test_owasp_a05_security_headers_are_applied_to_admin_panel_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_phase_2_storage_pruning_command_cleans_old_read_notifications_only(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        DB::table('notifications')->insert([
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'type' => 'App\\Notifications\\SubmissionStatusNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['event' => 'disetujui', 'title' => 'Lama Dibaca']),
                'read_at' => now()->subDays(200),
                'created_at' => now()->subDays(210),
                'updated_at' => now()->subDays(200),
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000002',
                'type' => 'App\\Notifications\\SubmissionStatusNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['event' => 'disetujui', 'title' => 'Baru Dibaca']),
                'read_at' => now()->subDays(10),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(10),
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000003',
                'type' => 'App\\Notifications\\SubmissionStatusNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['event' => 'revisi', 'title' => 'Lama Belum Dibaca']),
                'read_at' => null,
                'created_at' => now()->subDays(210),
                'updated_at' => now()->subDays(210),
            ],
            [
                'id' => '00000000-0000-0000-0000-000000000004',
                'type' => 'App\\Notifications\\SubmissionStatusNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['event' => 'revisi', 'title' => 'Sangat Lama Belum Dibaca (>1 Tahun)']),
                'read_at' => null,
                'created_at' => now()->subDays(400),
                'updated_at' => now()->subDays(400),
            ],
        ]);

        $this->artisan('sipetron:prune-storage')->assertExitCode(0);

        $this->assertDatabaseMissing('notifications', ['id' => '00000000-0000-0000-0000-000000000001']);
        $this->assertDatabaseHas('notifications', ['id' => '00000000-0000-0000-0000-000000000002']);
        $this->assertDatabaseHas('notifications', ['id' => '00000000-0000-0000-0000-000000000003']);
        $this->assertDatabaseMissing('notifications', ['id' => '00000000-0000-0000-0000-000000000004']);
    }

    public function test_smtp_timeout_is_bounded_and_heavy_endpoints_enforce_rate_limits(): void
    {
        $smtpTimeout = config('mail.mailers.smtp.timeout');
        $this->assertIsInt($smtpTimeout);
        $this->assertGreaterThan(0, $smtpTimeout);
        $this->assertLessThanOrEqual(15, $smtpTimeout);

        $user = User::factory()->create(['role' => 'mahasiswa']);
        $submission = Submission::create([
            'ticket_code' => 'VTR-202611-RATE',
            'user_id' => $user->id,
            'title' => 'Uji Rate Limiter Cetak PDF',
            'content_type' => 'poster',
            'description' => 'Deskripsi uji.',
            'pic_name' => 'Mahasiswa Uji',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/drive/folders/rate-test',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'status' => 'menunggu',
        ]);

        $pollResponse = $this->actingAs($user)->getJson(route('notifikasi.checkUnread'));
        $pollResponse->assertOk();
        $pollResponse->assertHeader('X-RateLimit-Limit', '60');

        $pdfResponse = $this->actingAs($user)->get(route('pengajuan.print', $submission->ticket_code));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('X-RateLimit-Limit', '20');
        $pdfResponse->assertHeaderMissing('Content-Security-Policy');
    }

    public function test_portal_toast_renderer_escapes_html_before_dom_insertion(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->get(route('home'));
        $response->assertOk();
        $response->assertSee('function escapeHtml(value)', false);
        $response->assertSee('const safeMessage = escapeHtml(message);', false);
        $response->assertSee('const safeTitle = escapeHtml(title);', false);
    }

    public function test_database_seeder_skips_demo_accounts_and_dummy_submissions_in_production(): void
    {
        $originalEnvironment = $this->app['env'];

        try {
            $this->app['env'] = 'production';
            $this->assertTrue($this->app->isProduction());

            $this->artisan('db:seed', ['--force' => true])
                ->assertExitCode(0);

            $this->assertSame(6, Videotron::count());
            $this->assertSame(0, User::count());
            $this->assertDatabaseMissing('users', ['email' => 'admin@uin.ac.id']);
            $this->assertSame(0, Submission::count());
        } finally {
            $this->app['env'] = $originalEnvironment;
        }
    }

    public function test_login_rate_limiter_blocks_password_spraying_across_different_usernames_from_same_ip(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.77'])
                ->post('/login', [
                    'identifier' => "spray{$i}@target.ac.id",
                    'password' => 'CommonPass123',
                ])
                ->assertSessionHas('error');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.77'])
            ->post('/login', [
                'identifier' => 'spray61@target.ac.id',
                'password' => 'CommonPass123',
            ])
            ->assertStatus(429);
    }

    public function test_login_honeypot_blocks_bot_submission_even_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'bot.target@uinssc.ac.id',
            'role' => 'admin',
            'password' => bcrypt('StrongPass123!'),
        ]);

        $response = $this->post('/login', [
            'identifier' => $admin->email,
            'password' => 'StrongPass123!',
            'website' => 'https://spam-bot.example',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Username atau password salah.');
        $this->assertGuest();
    }

    public function test_trusted_proxies_ignores_spoofed_forwarded_ip_from_untrusted_remote_client(): void
    {
        $this->assertSame('127.0.0.1,::1', config('trustedproxy.proxies'));

        // Klien eksternal mencoba memutar header X-Forwarded-For palsu pada setiap percobaan login
        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.45'])
                ->withHeaders(['X-Forwarded-For' => "10.0.0.{$i}"])
                ->post('/login', [
                    'identifier' => 'spoof.target@uinssc.ac.id',
                    'password' => 'WrongPassword'.$i,
                ])
                ->assertSessionHas('error');
        }

        // Percobaan ke-6 tetap terkena HTTP 429 karena X-Forwarded-For palsu diabaikan
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.45'])
            ->withHeaders(['X-Forwarded-For' => '10.0.0.99'])
            ->post('/login', [
                'identifier' => 'spoof.target@uinssc.ac.id',
                'password' => 'WrongPassword6',
            ])
            ->assertStatus(429);
    }

    public function test_admin_change_password_enforces_min_length_invalidates_other_sessions_and_throttles(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin.security@uinssc.ac.id',
            'role' => 'admin',
            'password' => bcrypt('OldPassword123'),
            'remember_token' => 'old_remember_token_123456',
        ]);

        // 1. Password kurang dari 8 karakter wajib ditolak 422
        $this->actingAs($admin)
            ->postJson(route('admin.change-password'), [
                'current_password' => 'OldPassword123',
                'new_password' => 'short77',
                'new_password_confirmation' => 'short77',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);

        // 2. Ubah password valid merotasi remember_token dan menghapus sesi perangkat lain
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert([
            'id' => 'other_device_session_id',
            'user_id' => $admin->id,
            'ip_address' => '198.51.100.12',
            'user_agent' => 'OtherDevice/1.0',
            'payload' => 'dGVzdA==',
            'last_activity' => now()->getTimestamp(),
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.change-password'), [
                'current_password' => 'OldPassword123',
                'new_password' => 'NewSecurePass123',
                'new_password_confirmation' => 'NewSecurePass123',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $admin->refresh();
        $this->assertTrue(Hash::check('NewSecurePass123', $admin->password));
        $this->assertNotSame('old_remember_token_123456', $admin->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'other_device_session_id']);

        // 3. Total 5 percobaan per menit diperbolehkan, percobaan ke-6 diblokir HTTP 429
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($admin)
                ->postJson(route('admin.change-password'), [
                    'current_password' => 'WrongCurrentPass',
                    'new_password' => 'AnotherPass123',
                    'new_password_confirmation' => 'AnotherPass123',
                ])
                ->assertStatus(422);
        }

        $this->actingAs($admin)
            ->postJson(route('admin.change-password'), [
                'current_password' => 'WrongCurrentPass',
                'new_password' => 'AnotherPass123',
                'new_password_confirmation' => 'AnotherPass123',
            ])
            ->assertStatus(429);
    }

    public function test_registration_enforces_minimum_8_character_password(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Pemohon Password Pendek',
            'role_type' => 'mitra',
            'unit_or_prodi' => 'PT Mitra Kampus',
            'email' => 'shortpass@mitra.co.id',
            'phone' => '081234567890',
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'shortpass@mitra.co.id']);
    }

    public function test_deactivated_admin_visiting_home_is_logged_out_and_redirected_to_login(): void
    {
        $inactiveAdmin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'inactive',
        ]);

        $response = $this->actingAs($inactiveAdmin)->get(route('home'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_recap_print_view_displays_truncation_notice_when_archive_exceeds_export_limit(): void
    {
        $view = $this->view('admin.recap-print', [
            'submissions' => collect(),
            'totalArchived' => 500,
            'totalCompleted' => 420,
            'totalRejected' => 50,
            'totalCanceled' => 30,
            'filterStatus' => null,
            'isTruncated' => true,
            'totalMatched' => 1250,
            'maxExportRows' => 500,
        ]);

        $view->assertSee('Catatan Batas Cetak');
        $view->assertSee('1250');
        $view->assertSee('500');
    }

    public function test_setting_and_videotron_seeders_preserve_admin_customizations_on_redeploy(): void
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SettingSeeder', '--force' => true])
            ->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\VideotronSeeder', '--force' => true])
            ->assertExitCode(0);

        $this->assertTrue((bool) Setting::get('min_days_before_enabled'));
        $this->assertSame(6, Videotron::count());

        // Simulasi Admin PUSTIKOM mengubah pengaturan dan kondisi perangkat videotron
        Setting::set('helpdesk_whatsapp', '089911223344', 'string', 'helpdesk');
        Setting::set('min_days_before_enabled', false, 'boolean', 'policy');

        $masjid = Videotron::where('name', 'Videotron Masjid')->firstOrFail();
        $masjid->update([
            'condition' => 'gangguan',
            'is_active' => false,
            'resolution' => '2560x1440',
        ]);

        // Simulasi redeploy yang menjalankan ulang seeder master
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SettingSeeder', '--force' => true])
            ->assertExitCode(0);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\VideotronSeeder', '--force' => true])
            ->assertExitCode(0);

        Setting::clearCache();
        $this->assertSame('089911223344', Setting::get('helpdesk_whatsapp'));
        $this->assertFalse((bool) Setting::get('min_days_before_enabled'));

        $masjid->refresh();
        $this->assertSame('gangguan', $masjid->condition);
        $this->assertFalse($masjid->is_active);
        $this->assertSame('2560x1440', $masjid->resolution);
    }
}
