<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\AccountApprovalNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_successfully_with_professional_branding(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('E-VITRON');
        $response->assertSee('Masuk ke E-VITRON');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('Masuk dengan Akun Portal Akademik (NIM / NIP)');
        $response->assertSee('Masuk');
        $response->assertSee('UIN Siber Syekh Nurjati Cirebon');
        $response->assertSee('info@uinssc.ac.id');
        $response->assertSee('to=info@uinssc.ac.id', false);
        $response->assertDontSee('https://pustikom.uinssc.ac.id');
        $response->assertDontSee('Dosen / Tendik'); // Role toggle removed from UI
        $response->assertSee(route('login.post'));
    }

    public function test_user_can_login_with_registered_nim(): void
    {
        $user = User::factory()->create([
            'identifier' => '2388010023',
            'role' => 'mahasiswa',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'identifier' => '2388010023',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_with_registered_email_and_redirects_to_admin(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@uin.ac.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->post('/login', [
            'identifier' => 'admin@uin.ac.id',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_unregistered_email_cannot_login(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'bukanadmin@gmail.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_unregistered_nim_cannot_login(): void
    {
        $response = $this->post('/login', [
            'identifier' => '9999999999',
            'password' => 'password',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_invalid_username_format_cannot_login(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'random_text_user',
            'password' => 'password',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_redirects(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('home'));
    }

    public function test_authenticated_admin_visiting_login_redirects_to_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('/admin');
    }

    public function test_unauthenticated_user_accessing_admin_redirects_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_login_url_redirects_to_unified_login(): void
    {
        $response = $this->get('/admin/login');

        $response->assertRedirect(route('login'));
    }

    public function test_user_logout_redirects_to_login(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_web_routes_include_no_cache_headers_to_prevent_back_history(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_admin_accessing_applicant_submissions_list_redirects_to_admin_submissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/pengajuan');

        $response->assertRedirect('/admin/submissions');
    }

    public function test_admin_accessing_applicant_submissions_list_with_selesai_status_redirects_to_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/pengajuan?status=selesai');

        $response->assertRedirect('/admin/history-and-recap?status=selesai');
    }

    public function test_admin_accessing_applicant_submissions_list_with_menunggu_status_redirects_to_admin_status_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/pengajuan?status=menunggu');

        $response->assertRedirect('/admin/submissions?statusFilter=menunggu');
    }

    public function test_admin_accessing_applicant_submission_detail_redirects_to_admin_drawer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $applicant = User::factory()->create(['role' => 'mahasiswa']);

        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-TEST1',
            'user_id' => $applicant->id,
            'title' => 'Test Pengajuan',
            'pic_name' => 'Pemohon Test',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'description' => 'Deskripsi test',
            'content_type' => 'poster',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->get('/pengajuan/VTR-202610-TEST1');

        $response->assertRedirect('/admin/submissions?selected='.$submission->id);
    }

    public function test_admin_accessing_applicant_new_submission_form_redirects_to_admin_submissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/pengajuan/baru');

        $response->assertRedirect('/admin/submissions');
    }

    public function test_applicant_user_can_access_applicant_submissions_normally(): void
    {
        $applicant = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($applicant)->get('/pengajuan');

        $response->assertStatus(200);
    }

    public function test_register_page_renders_successfully(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Pemohon');
        $response->assertSee('Nama Lengkap Penanggung Jawab (PIC)');
        $response->assertSee('Nama Instansi / Unit Kerja');
        $response->assertSee(route('register.post'));
    }

    public function test_tendik_registration_creates_pending_account_and_requires_admin_approval(): void
    {
        $response = $this->post('/register', [
            'name' => 'Fauzi Tendik',
            'role_type' => 'tendik',
            'unit_or_prodi' => 'Biro Administrasi Umum',
            'email' => 'fauzi.tendik@uinssc.ac.id',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'fauzi.tendik@uinssc.ac.id',
            'role' => 'tendik',
            'unit_or_prodi' => 'Biro Administrasi Umum',
            'account_status' => 'pending',
        ]);
    }

    public function test_mitra_cannot_login_while_pending_but_can_login_after_admin_approval_and_email_sent(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Mitra BSI',
            'role_type' => 'mitra',
            'unit_or_prodi' => 'Bank Syariah Indonesia Cirebon',
            'email' => 'cs@bsi-cirebon.co.id',
            'phone' => '085577889900',
            'password' => 'mitra12345',
            'password_confirmation' => 'mitra12345',
        ]);

        $this->assertGuest();

        // Percobaan login saat status masih pending harus ditolak
        $pendingLoginResponse = $this->post('/login', [
            'identifier' => 'cs@bsi-cirebon.co.id',
            'password' => 'mitra12345',
        ]);

        $pendingLoginResponse->assertSessionHas('error');
        $this->assertGuest();

        // Simulasi Admin menyetujui akun & mengirim notifikasi email
        $user = User::where('email', 'cs@bsi-cirebon.co.id')->firstOrFail();
        $user->update(['account_status' => 'approved']);
        $user->notify(new AccountApprovalNotification($user, 'approved'));

        Notification::assertSentTo($user, AccountApprovalNotification::class);

        // Setelah di-approve, user berhasil login dan masuk ke Beranda
        $approvedLoginResponse = $this->post('/login', [
            'identifier' => 'cs@bsi-cirebon.co.id',
            'password' => 'mitra12345',
        ]);

        $approvedLoginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_demo_quick_login_is_hidden_by_default_and_works_when_enabled_with_exhibition_seeder(): void
    {
        config(['services.demo.quick_login' => false]);

        $defaultLoginView = $this->get('/login');
        $defaultLoginView->assertStatus(200);
        $defaultLoginView->assertDontSee('Akses Cepat Demo');

        config([
            'services.demo.quick_login' => true,
            'services.demo.admin_email' => 'admin.evitron@uinssc.ac.id',
            'services.demo.admin_password' => 'UinsscDemo26',
            'services.demo.applicant_identifier' => '2208101001',
            'services.demo.applicant_email' => 'pemohon.demo@uinssc.ac.id',
            'services.demo.applicant_password' => 'UinsscDemo26',
        ]);

        $demoLoginView = $this->get('/login');
        $demoLoginView->assertStatus(200);
        $demoLoginView->assertSee('Akses Cepat Demo');
        $demoLoginView->assertSee('Pemohon Demo');
        $demoLoginView->assertSee('Admin E-VITRON');
        $demoLoginView->assertSee('2208101001');

        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ExhibitionDemoSeeder', '--force' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin.evitron@uinssc.ac.id',
            'name' => 'Admin E-VITRON',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'identifier' => '2208101001',
            'role' => 'mahasiswa',
        ]);
        $this->assertSame(3, Submission::count());

        $applicantLogin = $this->post('/login', [
            'identifier' => '2208101001',
            'password' => 'UinsscDemo26',
        ]);
        $applicantLogin->assertRedirect(route('home'));
    }
}
