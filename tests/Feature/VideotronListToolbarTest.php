<?php

namespace Tests\Feature;

use App\Filament\Resources\VideotronResource\Pages\ListVideotrons;
use App\Models\User;
use App\Models\Videotron;
use Database\Seeders\VideotronSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class VideotronListToolbarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::getCurrentPanel()->boot();
    }

    public function test_videotron_list_toolbar_and_search_behavior(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        // Ensure we have videotron records
        if (Videotron::count() === 0) {
            Videotron::create([
                'name' => 'Videotron Masjid Kampus',
                'location' => 'Depan Masjid',
                'resolution' => '1920x1080',
                'orientation' => 'landscape',
                'condition' => 'normal',
                'is_active' => true,
            ]);
        }

        $totalCount = Videotron::count();

        $test = Livewire::test(ListVideotrons::class);

        // 1. Initial State: Title and total badge visible, no filter indicators
        $test->assertSee('Daftar Videotron');
        $test->assertSee("{$totalCount} Perangkat");
        $test->assertDontSee('fi-ta-filter-indicators');

        // 2. Search matches: Title still visible, count updated
        $first = Videotron::first();
        $test->set('tableSearch', $first->name);
        $test->assertSee('Daftar Videotron');
        $test->assertDontSee('fi-ta-filter-indicators');

        // 3. Search 1 character "h" (not in any name, only in location "Character"): returns 0 results
        $test->set('tableSearch', 'h');
        $test->assertSee('Daftar Videotron');
        $test->assertSee('0 Perangkat');
        $test->assertSee('Tidak ada videotron ditemukan');

        // 4. Search 0 results with longer query
        $test->set('tableSearch', 'zzz_no_match_query_123');
        $test->assertSee('Daftar Videotron');
        $test->assertSee('0 Perangkat');
        $test->assertSee('Tidak ada videotron ditemukan');
        $test->assertDontSee('No Daftar Videotron');
        $test->assertDontSee('fi-ta-filter-indicators');

        // 5. Search cleared: Title still visible, original count restored
        $test->set('tableSearch', '');
        $test->assertSee('Daftar Videotron');
        $test->assertSee("{$totalCount} Perangkat");
        $test->assertDontSee('fi-ta-filter-indicators');
    }

    public function test_campus_videotrons_resolve_permanent_webp_photos_and_fallback_when_custom_upload_is_missing(): void
    {
        Storage::fake('public');

        $seeded = VideotronSeeder::seedMasterVideotrons();

        $expectedPaths = [
            'masjid' => 'images/videotrons/masjid.webp',
            'rektorat' => 'images/videotrons/rektorat.webp',
            'syariah' => 'images/videotrons/syariah.webp',
            'fitk' => 'images/videotrons/fitk.webp',
            'siber' => 'images/videotrons/siber.webp',
            'febi' => 'images/videotrons/febi.webp',
        ];

        foreach ($expectedPaths as $key => $relativePath) {
            $this->assertFileExists(public_path($relativePath));
            $this->assertFalse($seeded[$key]->hasCustomImage());
            $this->assertSame($relativePath, $seeded[$key]->getDefaultImagePath());
            $this->assertSame(asset($relativePath), $seeded[$key]->image_url);
        }

        // Simulasi container ephemeral redeploy: path tersimpan di DB tetapi berkas fisik hilang
        $rektorat = $seeded['rektorat'];
        $rektorat->update(['image' => 'videotrons/wiped-on-redeploy.jpg']);
        $rektorat->refresh();

        $this->assertFalse($rektorat->hasCustomImage());
        $this->assertSame(asset('images/videotrons/rektorat.webp'), $rektorat->image_url);

        // Saat admin membuka modal Ubah Data, nilai stale image dibersihkan menjadi null agar FilePond tidak 404
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test(ListVideotrons::class)
            ->mountTableAction('edit', $rektorat)
            ->assertTableActionDataSet([
                'image' => [],
            ])
            ->assertSee('Foto Resmi Kampus');

        // Jika berkas unggahan khusus benar-benar ada di disk public, unggahan khusus tetap diprioritaskan
        Storage::disk('public')->put('videotrons/custom-rektorat.webp', 'fake-webp-bytes');
        $rektorat->update(['image' => 'videotrons/custom-rektorat.webp']);
        $rektorat->refresh();

        $this->assertTrue($rektorat->hasCustomImage());
        $this->assertSame(asset('storage/videotrons/custom-rektorat.webp'), $rektorat->image_url);
    }
}
