<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileResponsiveNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_hamburger_menu_and_drawer_are_rendered_for_authenticated_users(): void
    {
        $user = User::factory()->create([
            'name' => 'Dicky Dermawan',
            'identifier' => '2388010023',
            'role' => 'mahasiswa',
            'unit_or_prodi' => 'Informatika',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);

        // Memastikan tombol hamburger mobile hadir di header
        $response->assertSee('id="mobile-menu-btn"', false);

        // Memastikan backdrop dan drawer panel mobile hadir
        $response->assertSee('id="mobile-nav-backdrop"', false);
        $response->assertSee('id="mobile-nav-drawer"', false);

        // Memastikan link navigasi lengkap ada di mobile drawer
        $response->assertSee(route('home'));
        $response->assertSee(route('pengajuan.create'));
        $response->assertSee(route('pengajuan.index'));
        $response->assertSee(route('panduan'));

        // Memastikan data profil ringkas ada di mobile drawer
        $response->assertSee('Dicky Dermawan');
        $response->assertSee('2388010023');
        $response->assertSee('Informatika');
        $response->assertSee('Keluar dari Akun');
    }

    public function test_wizard_submission_renders_mobile_compact_stepper(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.create'));

        $response->assertStatus(200);

        // Memastikan stepper mobile ringkas (< sm) ter-render
        $response->assertSee('Langkah ${currentStep} dari ${steps.length}', false);
    }
}
