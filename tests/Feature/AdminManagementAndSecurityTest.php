<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Models\Submission;
use App\Models\User;
use App\Services\AdminAccountService;
use DomainException;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminManagementAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::getCurrentPanel()->boot();
    }

    private function createSuperAdmin(string $email = 'superadmin@uinssc.ac.id', string $name = 'Super Admin PUSTIKOM'): User
    {
        $admin = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('supersecret123'),
            'role' => 'admin',
            'auth_type' => 'local',
            'account_status' => 'approved',
        ]);
        $admin->is_super_admin = true;
        $admin->save();

        return $admin;
    }

    private function createOperatorAdmin(string $email = 'operator@uinssc.ac.id', string $name = 'Admin Operator'): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('operator123'),
            'role' => 'admin',
            'auth_type' => 'local',
            'account_status' => 'approved',
        ]);
    }

    public function test_super_admin_can_create_reset_password_promote_demote_and_deactivate_admin_in_settings(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $this->actingAs($superAdmin);

        $component = Livewire::withQueryParams(['tab' => 'admins'])
            ->test(ManageSettings::class)
            ->assertSet('activeTab', 'admins')
            ->assertSee('Tim Admin')
            ->assertSee('Tim Pengelola Admin E-VITRON')
            ->call('openCreateAdminModal')
            ->assertSet('showCreateAdminModal', true)
            ->set('new_admin_name', 'Staf Humas Baru')
            ->set('new_admin_email', 'humas.baru@uinssc.ac.id')
            ->set('new_admin_unit', 'Humas UIN SSC')
            ->set('new_admin_phone', '081234567899')
            ->set('new_admin_password', 'Rahasia123!')
            ->set('new_admin_password_confirmation', 'Rahasia123!')
            ->set('new_admin_is_super_admin', false)
            ->call('createAdminAccount')
            ->assertHasNoErrors()
            ->assertSet('showCreateAdminModal', false)
            ->assertNotified()
            ->assertSee('Staf Humas Baru');

        $created = User::where('email', 'humas.baru@uinssc.ac.id')->firstOrFail();
        $this->assertSame('admin', $created->role);
        $this->assertFalse($created->is_super_admin);
        $this->assertSame('approved', $created->account_status);
        $this->assertTrue(Hash::check('Rahasia123!', $created->password));

        // Reset password
        $component
            ->call('openResetPasswordModal', $created->id)
            ->assertSet('showResetPasswordModal', true)
            ->set('reset_admin_password', 'PasswordBaru99!')
            ->set('reset_admin_password_confirmation', 'PasswordBaru99!')
            ->call('confirmResetAdminPassword')
            ->assertHasNoErrors()
            ->assertSet('showResetPasswordModal', false)
            ->assertNotified();

        $this->assertTrue(Hash::check('PasswordBaru99!', $created->fresh()->password));

        // Promosikan -> Super Admin, lalu turunkan -> Operator
        $component
            ->call('toggleSuperAdminRole', $created->id)
            ->assertNotified();
        $this->assertTrue($created->fresh()->is_super_admin);

        $component
            ->call('toggleSuperAdminRole', $created->id)
            ->assertNotified();
        $this->assertFalse($created->fresh()->is_super_admin);

        // Nonaktifkan -> Aktifkan kembali
        $component
            ->call('toggleAdminStatus', $created->id)
            ->assertNotified();
        $this->assertSame('inactive', $created->fresh()->account_status);

        $component
            ->call('toggleAdminStatus', $created->id)
            ->assertNotified();
        $this->assertSame('approved', $created->fresh()->account_status);
    }

    public function test_regular_admin_operator_cannot_access_tim_admin_tab_or_call_management_actions(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $operator = $this->createOperatorAdmin();

        $this->actingAs($operator);

        Livewire::withQueryParams(['tab' => 'admins'])
            ->test(ManageSettings::class)
            ->assertSet('activeTab', 'signatory')
            ->assertDontSee('Tim Pengelola Admin E-VITRON')
            ->assertDontSee('Tambah Akun Admin Baru');

        Livewire::test(ManageSettings::class)
            ->call('setTab', 'admins')
            ->assertForbidden();

        Livewire::test(ManageSettings::class)
            ->call('openCreateAdminModal')
            ->assertForbidden();

        Livewire::test(ManageSettings::class)
            ->call('toggleAdminStatus', $superAdmin->id)
            ->assertForbidden();

        Livewire::test(ManageSettings::class)
            ->call('toggleSuperAdminRole', $superAdmin->id)
            ->assertForbidden();

        Livewire::test(ManageSettings::class)
            ->call('openResetPasswordModal', $superAdmin->id)
            ->assertForbidden();

        $this->assertSame('approved', $superAdmin->fresh()->account_status);
        $this->assertTrue($superAdmin->fresh()->is_super_admin);
    }

    public function test_is_super_admin_cannot_be_mass_assigned_or_escalated_via_public_register(): void
    {
        $this->assertNotContains('is_super_admin', (new User)->getFillable());

        try {
            User::create([
                'name' => 'Coba Mass Assign',
                'email' => 'massassign@uinssc.ac.id',
                'password' => Hash::make('secret1234'),
                'role' => 'admin',
                'is_super_admin' => true,
            ]);
            $this->fail('Seharusnya menolak mass assignment atribut is_super_admin.');
        } catch (MassAssignmentException $e) {
            $this->assertStringContainsString('is_super_admin', $e->getMessage());
        }

        $this->assertDatabaseMissing('users', ['email' => 'massassign@uinssc.ac.id']);

        $response = $this->post(route('register.post'), [
            'name' => 'Pendaftar Iseng',
            'unit_or_prodi' => 'Unit Luar',
            'email' => 'iseng@example.com',
            'phone' => '081234567890',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'role' => 'admin',
            'is_super_admin' => 1,
        ]);

        $response->assertRedirect(route('login'));

        $registered = User::where('email', 'iseng@example.com')->firstOrFail();
        $this->assertNotSame('admin', $registered->role);
        $this->assertFalse((bool) $registered->is_super_admin);
        $this->assertSame('pending', $registered->account_status);
    }

    public function test_deactivated_admin_is_blocked_from_login_panel_admin_routes_and_notifications(): void
    {
        $activeAdmin = $this->createSuperAdmin();
        $inactiveAdmin = $this->createOperatorAdmin('nonaktif@uinssc.ac.id', 'Mantan Admin');
        $inactiveAdmin->update(['account_status' => 'inactive']);

        // 1. Login ditolak
        $loginResponse = $this->post(route('login.post'), [
            'identifier' => 'nonaktif@uinssc.ac.id',
            'password' => 'operator123',
        ]);
        $loginResponse->assertRedirect();
        $loginResponse->assertSessionHas('error');
        $this->assertGuest();

        // 2. Akses panel /admin dan rute middleware EnsureAdminRole ditolak (403)
        $this->actingAs($inactiveAdmin)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($inactiveAdmin)
            ->get(route('admin.history-and-recap.print'))
            ->assertForbidden();

        // 3. Notifikasi pengajuan baru hanya dikirim ke admin aktif
        $pengaju = User::factory()->create(['role' => 'mahasiswa']);
        Submission::create([
            'ticket_code' => 'VTR-202611-9001',
            'user_id' => $pengaju->id,
            'title' => 'Seminar Nasional',
            'content_type' => 'poster',
            'description' => 'Deskripsi uji notifikasi.',
            'pic_name' => $pengaju->name,
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/file/d/test',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'status' => 'menunggu',
        ]);

        $this->assertSame(1, $activeAdmin->notifications()->count());
        $this->assertSame(0, $inactiveAdmin->notifications()->count());
    }

    public function test_anti_lockout_prevents_self_deactivation_self_demotion_and_removing_last_super_admin(): void
    {
        $soleSuperAdmin = $this->createSuperAdmin();

        $this->actingAs($soleSuperAdmin);

        Livewire::test(ManageSettings::class)
            ->call('toggleAdminStatus', $soleSuperAdmin->id)
            ->assertNotified();

        $this->assertSame('approved', $soleSuperAdmin->fresh()->account_status);

        Livewire::test(ManageSettings::class)
            ->call('toggleSuperAdminRole', $soleSuperAdmin->id)
            ->assertNotified();

        $this->assertTrue($soleSuperAdmin->fresh()->is_super_admin);

        $service = app(AdminAccountService::class);

        try {
            $service->setAdminActiveStatus($soleSuperAdmin, false);
            $this->fail('Seharusnya menolak penonaktifan Super Admin aktif terakhir.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('Super Admin aktif terakhir', $e->getMessage());
        }

        try {
            $service->updateAdminRole($soleSuperAdmin, false);
            $this->fail('Seharusnya menolak penurunan peran Super Admin aktif terakhir.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('1 Super Admin aktif', $e->getMessage());
        }
    }

    public function test_artisan_evitron_admin_command_manages_admins_safely(): void
    {
        $this->artisan('evitron:admin', [
            '--action' => 'create',
            '--name' => 'Kepala PUSTIKOM',
            '--email' => 'pustikom@uinssc.ac.id',
            '--unit' => 'PUSTIKOM UIN SSC',
            '--password' => 'Pustikom2026!',
            '--super' => true,
        ])->assertSuccessful();

        $first = User::where('email', 'pustikom@uinssc.ac.id')->firstOrFail();
        $this->assertTrue($first->isSuperAdmin());

        // Tidak boleh menonaktifkan Super Admin tunggal via CLI
        $this->artisan('evitron:admin', [
            '--action' => 'deactivate',
            '--email' => 'pustikom@uinssc.ac.id',
        ])->assertFailed();

        // Buat operator kedua, lalu reset password, promosikan, dan nonaktifkan
        $this->artisan('evitron:admin', [
            '--action' => 'create',
            '--name' => 'Operator Shift 1',
            '--email' => 'operator1@uinssc.ac.id',
            '--password' => 'Operator2026!',
        ])->assertSuccessful();

        $second = User::where('email', 'operator1@uinssc.ac.id')->firstOrFail();
        $this->assertTrue($second->isActiveAdmin());
        $this->assertFalse($second->is_super_admin);

        $this->artisan('evitron:admin', [
            '--action' => 'reset-password',
            '--email' => 'operator1@uinssc.ac.id',
            '--password' => 'ResetBaru2026!',
        ])->assertSuccessful();
        $this->assertTrue(Hash::check('ResetBaru2026!', $second->fresh()->password));

        $this->artisan('evitron:admin', [
            '--action' => 'promote',
            '--email' => 'operator1@uinssc.ac.id',
        ])->assertSuccessful();
        $this->assertTrue($second->fresh()->is_super_admin);

        $this->artisan('evitron:admin', [
            '--action' => 'deactivate',
            '--email' => 'operator1@uinssc.ac.id',
        ])->assertSuccessful();
        $this->assertSame('inactive', $second->fresh()->account_status);

        $this->artisan('evitron:admin', [
            '--action' => 'list',
        ])->assertSuccessful();
    }

    public function test_demo_admin_account_is_locked_only_while_demo_quick_login_is_enabled(): void
    {
        $demoAdmin = $this->createSuperAdmin('demo.admin@uinssc.ac.id', 'Admin Demo');
        $demoAdmin->update(['password' => Hash::make('DemoPass2026!')]);

        $otherSuperAdmin = $this->createSuperAdmin('other.super@uinssc.ac.id', 'Super Admin Lain');

        config([
            'services.demo.quick_login' => true,
            'services.demo.admin_email' => 'demo.admin@uinssc.ac.id',
        ]);

        // 1. Saat DEMO_QUICK_LOGIN=true, ubah password mandiri ditolak 403
        $this->actingAs($demoAdmin)
            ->postJson(route('admin.change-password'), [
                'current_password' => 'DemoPass2026!',
                'new_password' => 'HackedPass2026!',
                'new_password_confirmation' => 'HackedPass2026!',
            ])
            ->assertStatus(403);

        $this->assertTrue(Hash::check('DemoPass2026!', $demoAdmin->fresh()->password));

        // 2. Super Admin lain di UI tidak dapat menonaktifkan, menurunkan peran, atau mereset password akun demo
        $service = app(AdminAccountService::class);
        foreach ([
            fn () => $service->setAdminActiveStatus($demoAdmin, false, $otherSuperAdmin),
            fn () => $service->updateAdminRole($demoAdmin, false, $otherSuperAdmin),
            fn () => $service->resetAdminPassword($demoAdmin, 'ResetLain2026!', $otherSuperAdmin),
        ] as $operation) {
            try {
                $operation();
                $this->fail('Operasi mutasi akun admin demo seharusnya ditolak saat mode demo aktif.');
            } catch (DomainException $e) {
                $this->assertStringContainsString('mode demo', $e->getMessage());
            }
        }

        // 3. Pemulihan lewat CLI ($actor = null) tetap diizinkan
        $service->resetAdminPassword($demoAdmin, 'CliReset2026!', null);
        $this->assertTrue(Hash::check('CliReset2026!', $demoAdmin->fresh()->password));

        // 4. Saat DEMO_QUICK_LOGIN=false (mode produksi), ubah password berjalan normal 200 OK
        config(['services.demo.quick_login' => false]);

        $this->actingAs($demoAdmin->fresh())
            ->postJson(route('admin.change-password'), [
                'current_password' => 'CliReset2026!',
                'new_password' => 'ProdSecure2026!',
                'new_password_confirmation' => 'ProdSecure2026!',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('ProdSecure2026!', $demoAdmin->fresh()->password));
    }
}
