<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_home(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_panduan_page(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($user)->get('/informasi');

        $response->assertStatus(200);
        $response->assertSee('Hari Kerja');
        $response->assertSee('Tanpa Keluaran Audio');
        $response->assertSee('info@uinssc.ac.id');
        $response->assertSee('Butuh Bantuan atau Konsultasi Penayangan?');
        $response->assertDontSee('Daftar Titik Perangkat Videotron Kampus');
        $response->assertDontSee('Perangkat Siap Pakai');
    }
}
