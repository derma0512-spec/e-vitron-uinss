<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Models\Setting;
use App\Models\User;
use App\Models\Videotron;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageSettingsPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::getCurrentPanel()->boot();
        Setting::clearCache();
    }

    public function test_admin_can_toggle_min_days_before_off_for_same_day_and_restore_previous_value(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::set('min_days_before', 5, 'integer', 'policy');

        $this->actingAs($admin);

        Livewire::test(ManageSettings::class)
            ->assertSet('min_days_before_enabled', true)
            ->assertSet('min_days_before', 5)
            ->set('min_days_before_enabled', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified();

        $this->assertSame(0, setting('min_days_before'));
        $this->assertFalse(setting('min_days_before_enabled'));
        $this->assertSame(5, setting('min_days_before_last'));
        $this->assertDatabaseHas('settings', ['key' => 'min_days_before', 'value' => '0']);
        $this->assertDatabaseHas('settings', ['key' => 'min_days_before_enabled', 'value' => '0']);
        $this->assertDatabaseHas('settings', ['key' => 'min_days_before_last', 'value' => '5']);

        Livewire::test(ManageSettings::class)
            ->assertSet('min_days_before_enabled', false)
            ->assertSet('min_days_before', 5)
            ->set('min_days_before_enabled', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified();

        $this->assertSame(5, setting('min_days_before'));
        $this->assertTrue(setting('min_days_before_enabled'));
        $this->assertDatabaseHas('settings', ['key' => 'min_days_before', 'value' => '5']);
        $this->assertDatabaseHas('settings', ['key' => 'min_days_before_enabled', 'value' => '1']);
    }

    public function test_legacy_zero_min_days_before_mounts_as_same_day_mode_with_safe_default_value(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::set('min_days_before', 0, 'integer', 'policy');

        $this->actingAs($admin);

        Livewire::test(ManageSettings::class)
            ->assertSet('min_days_before_enabled', false)
            ->assertSet('min_days_before', 3);
    }

    public function test_enabled_min_days_before_rejects_zero_value(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::set('min_days_before', 3, 'integer', 'policy');

        $this->actingAs($admin);

        Livewire::test(ManageSettings::class)
            ->set('min_days_before_enabled', true)
            ->set('min_days_before', 0)
            ->call('save')
            ->assertHasErrors(['min_days_before' => 'min']);

        $this->assertSame(3, setting('min_days_before'));
    }

    public function test_public_guide_and_submission_wizard_display_same_day_copy_when_min_days_before_is_zero(): void
    {
        Setting::set('min_days_before', 0, 'integer', 'policy');
        Setting::set('min_days_before_enabled', false, 'boolean', 'policy');
        Setting::set('max_display_days', 0, 'integer', 'policy');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        Videotron::create([
            'name' => 'Videotron Gerbang Utama',
            'location' => 'Kampus 1',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $guideResponse = $this->actingAs($user)->get(route('panduan'));
        $guideResponse->assertOk();
        $guideResponse->assertSee('hingga hari penayangan (Hari-H)');
        $guideResponse->assertDontSee('H-0');
        $guideResponse->assertDontSee('0 hari kerja');

        $wizardResponse = $this->actingAs($user)->get(route('pengajuan.create'));
        $wizardResponse->assertOk();
        $wizardResponse->assertSee('Bisa Hari-H');
        $wizardResponse->assertSee('hingga hari penayangan (Hari-H)');
        $wizardResponse->assertSee('mulai hari ini (Hari-H)');
        $wizardResponse->assertDontSee('Minimal H-0');
        $wizardResponse->assertDontSee('H-0');
        $wizardResponse->assertDontSee('0 hari kerja');

        Setting::set('max_display_days', 5, 'integer', 'policy');

        $wizardWithMaxDaysResponse = $this->actingAs($user)->get(route('pengajuan.create'));
        $wizardWithMaxDaysResponse->assertOk();
        $wizardWithMaxDaysResponse->assertSee('mulai hari ini (Hari-H)');
        $wizardWithMaxDaysResponse->assertSee('5 hari');
        $wizardWithMaxDaysResponse->assertDontSee('H-0');
    }

    public function test_public_guide_and_submission_wizard_display_h_minus_copy_when_min_days_before_is_positive(): void
    {
        Setting::set('min_days_before', 3, 'integer', 'policy');
        Setting::set('min_days_before_enabled', true, 'boolean', 'policy');
        Setting::set('max_display_days', 7, 'integer', 'policy');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        Videotron::create([
            'name' => 'Videotron Gerbang Utama',
            'location' => 'Kampus 1',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        $guideResponse = $this->actingAs($user)->get(route('panduan'));
        $guideResponse->assertOk();
        $guideResponse->assertSee('H-3 (3 hari kerja)');
        $guideResponse->assertDontSee('Hari-H');

        $wizardResponse = $this->actingAs($user)->get(route('pengajuan.create'));
        $wizardResponse->assertOk();
        $wizardResponse->assertSee('Minimal H-3');
        $wizardResponse->assertSee('3 hari kerja');
        $wizardResponse->assertSee('H-3');
        $wizardResponse->assertSee('7 hari');
        $wizardResponse->assertDontSee('Bisa Hari-H');
        $wizardResponse->assertDontSee('mulai hari ini (Hari-H)');
    }
}
