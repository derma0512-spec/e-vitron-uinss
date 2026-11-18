<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Livewire\DatabaseNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelLayoutAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('login'));
    }

    public function test_pengaju_cannot_access_admin_panel(): void
    {
        $mahasiswa = User::where('role', 'mahasiswa')->first();
        $response = $this->actingAs($mahasiswa)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_panel_and_see_four_main_menus_and_overview_stats(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();

        // Menu Utama Sidebar
        $response->assertSee('Dashboard');
        $response->assertSee('Pengajuan');
        $response->assertSee('Kelola Videotron');
        $response->assertSee('Riwayat &amp; Rekap', false);
        $response->assertSee('Pengaturan');

        // Menu lama yang sudah digabung tidak boleh muncul lagi sebagai item terpisah
        $response->assertDontSee('Daftar Pengajuan');
        $response->assertDontSee('Persiapan &amp; Penayangan', false);

        // Menu tambahan yang tidak diizinkan tidak boleh muncul
        $response->assertDontSee('Pengguna');

        $response->assertSee('E-VITRON');
        $response->assertSee('Perlu Direview');
        $response->assertSee('Menunggu Perbaikan');
        $response->assertSee('Terjadwal');
        $response->assertSee('Sedang Tayang');
        $response->assertSee('Selesai Tayang');
        $response->assertSee('Jadwal Tayang Terdekat');
        $response->assertSee('Kondisi Perangkat Videotron');
    }

    public function test_admin_can_interact_with_filament_database_notifications(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertGreaterThan(0, $admin->unreadNotifications()->count());

        Livewire::actingAs($admin)
            ->test(DatabaseNotifications::class)
            ->assertSuccessful();
    }
}
