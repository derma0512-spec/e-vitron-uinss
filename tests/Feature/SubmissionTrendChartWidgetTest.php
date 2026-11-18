<?php

namespace Tests\Feature;

use App\Filament\Widgets\SubmissionTrendChartWidget;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionTrendChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    private function createSubmission(User $user, string $ticket, string $createdAt): Submission
    {
        $submission = Submission::create([
            'ticket_code' => $ticket,
            'user_id' => $user->id,
            'title' => 'Test '.$ticket,
            'content_type' => 'video',
            'description' => 'Deskripsi konten pengajuan',
            'pic_name' => 'Budi',
            'whatsapp' => '081234567890',
            'gdrive_link' => 'https://drive.google.com/test',
            'start_date' => substr($createdAt, 0, 10),
            'end_date' => substr($createdAt, 0, 10),
            'status' => 'menunggu',
        ]);

        $submission->timestamps = false;
        $submission->created_at = Carbon::parse($createdAt);
        $submission->updated_at = Carbon::parse($createdAt);
        $submission->save();

        return $submission;
    }

    public function test_only_authenticated_admin_can_view_widget(): void
    {
        // 1. Guest (auth user null) harus false
        Auth::logout();
        $this->assertFalse(SubmissionTrendChartWidget::canView());

        // 2. Non-admin (pengaju) harus false
        $applicant = User::factory()->create(['role' => 'pengaju']);
        $this->actingAs($applicant);
        $this->assertFalse(SubmissionTrendChartWidget::canView());

        // 3. Admin harus true
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $this->assertTrue(SubmissionTrendChartWidget::canView());
    }

    public function test_six_months_filter_avoids_carbon_month_overflow_with_differentiated_seeded_data(): void
    {
        // Kunci waktu pengujian di tanggal rawan overflow: 31 Maret 2026 jam 10:00
        Carbon::setTestNow('2026-03-31 10:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Seed data dengan kuantitas bertingkat:
        // Okt 2025: 1, Nov 2025: 2, Des 2025: 3, Jan 2026: 4, Feb 2026: 5, Mar 2026: 6
        $seedPlan = [
            '2025-10' => 1,
            '2025-11' => 2,
            '2025-12' => 3,
            '2026-01' => 4,
            '2026-02' => 5,
            '2026-03' => 6,
        ];

        $counter = 1;
        foreach ($seedPlan as $ym => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $this->createSubmission($admin, sprintf('TIK-M-%04d', $counter++), "{$ym}-15 14:00:00");
            }
        }

        $widget = new SubmissionTrendChartWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getSixMonthsSeries');
        $method->setAccessible(true);

        /** @var array{labels: list<string>, data: list<int>, fullDates: list<string>} $series */
        $series = $method->invoke($widget, Carbon::now());

        // 1. Verifikasi 6 label bulan berurutan unik
        $expectedLabels = ['Oktober 2025', 'November 2025', 'Desember 2025', 'Januari 2026', 'Februari 2026', 'Maret 2026'];
        $this->assertSame($expectedLabels, $series['labels']);

        // 2. Verifikasi data tidak tertukar / tidak hilang akibat overflow (tepat [1, 2, 3, 4, 5, 6])
        $this->assertSame([1, 2, 3, 4, 5, 6], $series['data']);

        Carbon::setTestNow();
    }

    public function test_boundary_timestamps_across_midnight_and_month_cutoff(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat 1 pengajuan di menit-menit akhir Maret dan 1 pengajuan di menit awal April
        $this->createSubmission($admin, 'TIK-BOUND-MAR', '2026-03-31 23:30:00');
        $this->createSubmission($admin, 'TIK-BOUND-APR', '2026-04-01 00:30:00');

        // Uji query agregat bulanan dari 1 Maret s.d. 30 April
        $widget = new SubmissionTrendChartWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getMonthlyCounts');
        $method->setAccessible(true);

        /** @var array<string, int> $monthlyCounts */
        $monthlyCounts = $method->invoke(
            $widget,
            Carbon::parse('2026-03-01 00:00:00'),
            Carbon::parse('2026-04-30 23:59:59')
        );

        $this->assertSame(1, $monthlyCounts['2026-03'] ?? 0, '23:30 WIB harus terhitung di bulan Maret');
        $this->assertSame(1, $monthlyCounts['2026-04'] ?? 0, '00:30 WIB harus terhitung di bulan April');
    }

    public function test_thirty_days_filter_has_exactly_thirty_data_points(): void
    {
        Carbon::setTestNow('2026-04-15 12:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Buat pengajuan di hari ini jam 00:00 persis
        $this->createSubmission($admin, 'TIK-30D-MIDNIGHT', '2026-04-15 00:00:00');

        $widget = new SubmissionTrendChartWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getDailySeries');
        $method->setAccessible(true);

        /** @var array{labels: list<string>, data: list<int>, fullDates: list<string>} $series */
        $series = $method->invoke($widget, Carbon::now());

        $this->assertCount(30, $series['labels']);
        $this->assertCount(30, $series['data']);
        $this->assertCount(30, $series['fullDates']);

        // Data point terakhir (hari ini, 15 Apr) harus mencatat 1 pengajuan jam 00:00
        $this->assertSame(1, end($series['data']));

        Carbon::setTestNow();
    }

    public function test_future_months_are_null_in_this_year_filter(): void
    {
        // Simulasikan bulan April 2026
        Carbon::setTestNow('2026-04-15 12:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->createSubmission($admin, 'TIK-YEAR-APR', '2026-04-10 09:00:00');

        $widget = new SubmissionTrendChartWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getYearlySeries');
        $method->setAccessible(true);

        /** @var array{labels: list<string>, data: list<int|null>, fullDates: list<string>} $series */
        $series = $method->invoke($widget, Carbon::now());

        // Jan - Apr (indeks 0..3) bernilai integer (bukan null)
        $this->assertIsInt($series['data'][0]);
        $this->assertIsInt($series['data'][1]);
        $this->assertIsInt($series['data'][2]);
        $this->assertSame(1, $series['data'][3]);

        // Mei - Des (indeks 4..11) bernilai null (garis grafik berhenti di bulan berjalan)
        for ($m = 4; $m < 12; $m++) {
            $this->assertNull($series['data'][$m], "Bulan ke-{$m} seharusnya null");
        }

        Carbon::setTestNow();
    }

    public function test_widget_renders_successfully_via_livewire(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test(SubmissionTrendChartWidget::class)
            ->assertSuccessful()
            ->set('filter', '30_days')
            ->assertSuccessful()
            ->set('filter', 'this_year')
            ->assertSuccessful();
    }
}
