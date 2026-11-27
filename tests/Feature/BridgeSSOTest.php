<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BridgeSSOTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.bridge_sso.enabled', true);
        Config::set('services.bridge_sso.base_url', 'https://bridge.uinssc.ac.id');
        Config::set('services.bridge_sso.simulate', false);
    }

    public function test_applicant_can_login_via_bridge_sso_and_user_is_auto_provisioned(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => Http::response([
                'status' => true,
                'message' => 'Login sukses',
                'data' => [
                    'token' => 'sample_bearer_token_12345',
                ],
            ], 200),
            'https://bridge.uinssc.ac.id/api/v2/profil' => Http::response([
                'status' => true,
                'data' => [
                    'nama' => 'Ahmad Dahlan',
                    'nim' => '2108101055',
                    'email' => 'ahmad.dahlan@uinssc.ac.id',
                    'prodi' => 'Teknologi Informasi',
                    'fakultas' => 'Fakultas Sains dan Teknologi Siber',
                    'no_hp' => '081234567890',
                ],
            ], 200),
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'kampusSecret123',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('bridge_sso_token', 'sample_bearer_token_12345');

        $this->assertDatabaseHas('users', [
            'identifier' => '2108101055',
            'name' => 'Ahmad Dahlan',
            'email' => 'ahmad.dahlan@uinssc.ac.id',
            'role' => 'mahasiswa',
            'unit_or_prodi' => 'Teknologi Informasi',
            'phone' => '081234567890',
            'auth_type' => 'campus_sso',
        ]);

        $user = User::where('identifier', '2108101055')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_applicant_login_via_sso_updates_profile(): void
    {
        $user = User::factory()->create([
            'identifier' => '2108101055',
            'name' => 'Nama Lama',
            'role' => 'mahasiswa',
            'unit_or_prodi' => 'Prodi Lama',
            'auth_type' => 'local',
        ]);

        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => Http::response([
                'status' => true,
                'data' => ['token' => 'token_updated_abc'],
            ], 200),
            'https://bridge.uinssc.ac.id/api/v2/profil' => Http::response([
                'status' => true,
                'data' => [
                    'nama' => 'Nama Baru Dari SSO',
                    'nim' => '2108101055',
                    'prodi' => 'Informatika Medis',
                ],
            ], 200),
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertSame('Nama Baru Dari SSO', $user->name);
        $this->assertSame('Informatika Medis', $user->unit_or_prodi);
        $this->assertSame('campus_sso', $user->auth_type);
        $this->assertAuthenticatedAs($user);
    }

    public function test_bridge_sso_invalid_credentials_returns_error_and_does_not_authenticate(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => Http::response([
                'status' => false,
                'message' => 'NIM atau kata sandi portal tidak sesuai.',
            ], 401),
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'NIM atau kata sandi portal tidak sesuai.');
        $this->assertGuest();
    }

    public function test_bridge_sso_invalid_credentials_falls_back_to_local_if_exists(): void
    {
        $user = User::factory()->create([
            'identifier' => '2108101055',
            'role' => 'mahasiswa',
            'password' => bcrypt('localpassword123'),
        ]);

        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => Http::response([
                'status' => false,
                'message' => 'Portal reject',
            ], 401),
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'localpassword123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_bridge_sso_connection_error_falls_back_to_local_if_exists(): void
    {
        $user = User::factory()->create([
            'identifier' => '2108101055',
            'role' => 'mahasiswa',
            'password' => bcrypt('localpassword123'),
        ]);

        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'localpassword123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_bridge_sso_connection_error_returns_friendly_error_if_no_local_user(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $response = $this->post('/login', [
            'identifier' => '2108101055',
            'password' => 'somepassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_bridge_sso_simulated_mode_authenticates_student(): void
    {
        Config::set('services.bridge_sso.simulate', true);

        $response = $this->post('/login', [
            'identifier' => '2108201099',
            'password' => 'bebas123',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'identifier' => '2108201099',
            'role' => 'mahasiswa',
            'auth_type' => 'campus_sso',
        ]);

        $user = User::where('identifier', '2108201099')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_bridge_sso_simulated_mode_authenticates_lecturer_with_long_nip(): void
    {
        Config::set('services.bridge_sso.simulate', true);

        // NIP standard length >= 12
        $response = $this->post('/login', [
            'identifier' => '198701012015011001',
            'password' => 'bebas123',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'identifier' => '198701012015011001',
            'role' => 'dosen',
            'auth_type' => 'campus_sso',
        ]);

        $user = User::where('identifier', '198701012015011001')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_calls_bridge_sso_logout_when_token_exists(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/logout' => Http::response([
                'status' => true,
                'message' => 'Logout berhasil',
            ], 200),
        ]);

        $user = User::factory()->create([
            'identifier' => '2108101055',
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['bridge_sso_token' => 'active_session_token_xyz'])
            ->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://bridge.uinssc.ac.id/api/v2/logout'
                && $request->hasHeader('Authorization', 'Bearer active_session_token_xyz');
        });
    }

    public function test_admin_login_with_email_bypasses_bridge_sso_completely(): void
    {
        Http::fake();

        $admin = User::factory()->create([
            'email' => 'admin.utama@uinssc.ac.id',
            'role' => 'admin',
            'password' => bcrypt('adminsecret123'),
        ]);

        $response = $this->post('/login', [
            'identifier' => 'admin.utama@uinssc.ac.id',
            'password' => 'adminsecret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);

        // Pastikan tidak ada request HTTP yang terkirim ke API portal kampus
        Http::assertNothingSent();
    }

    public function test_campus_sso_account_does_not_cache_campus_password_or_fallback_when_sso_rejects(): void
    {
        Http::fake([
            'https://bridge.uinssc.ac.id/api/v2/portal/login' => Http::sequence()
                ->push([
                    'status' => true,
                    'data' => ['token' => 'first_valid_sso_token'],
                ], 200)
                ->push([
                    'status' => false,
                    'message' => 'NIM atau kata sandi portal tidak sesuai.',
                ], 401),
            'https://bridge.uinssc.ac.id/api/v2/profil' => Http::response([
                'status' => true,
                'data' => [
                    'nama' => 'Siti Aisyah',
                    'nim' => '2208109999',
                    'email' => 'siti.aisyah@uinssc.ac.id',
                    'prodi' => 'Sistem Informasi',
                ],
            ], 200),
            'https://bridge.uinssc.ac.id/api/v2/logout' => Http::response(['status' => true], 200),
        ]);

        // 1. Login pertama via SSO berhasil
        $this->post('/login', [
            'identifier' => '2208109999',
            'password' => 'CampusSecret2026!',
        ])->assertRedirect(route('home'));

        $user = User::where('identifier', '2208109999')->firstOrFail();
        $this->assertSame('campus_sso', $user->auth_type);
        $this->assertFalse(Hash::check('CampusSecret2026!', $user->password));

        // 2. Logout lalu coba masuk lagi memakai password lama saat SSO menolak (password kampus diganti / akun dibekukan)
        $this->post('/logout')->assertRedirect(route('login'));

        $staleResponse = $this->post('/login', [
            'identifier' => '2208109999',
            'password' => 'CampusSecret2026!',
        ]);

        $staleResponse->assertRedirect();
        $staleResponse->assertSessionHas('error', 'NIM atau kata sandi portal tidak sesuai.');
        $this->assertGuest();
    }
}
