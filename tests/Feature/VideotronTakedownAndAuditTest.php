<?php

namespace Tests\Feature;

use App\Filament\Pages\HistoryAndRecap;
use App\Filament\Resources\SubmissionResource\Pages\ListSubmissions;
use App\Filament\Resources\VideotronResource\Pages\ListVideotrons;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use App\Services\VideotronPlaybackService;
use DomainException;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class VideotronTakedownAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::getCurrentPanel()->boot();
    }

    private function createVideotron(string $name, string $location): Videotron
    {
        return Videotron::create([
            'name' => $name,
            'location' => $location,
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'operating_hours' => '06.00 – 22.00 WIB',
            'condition' => 'normal',
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createSubmission(User $owner, array $overrides = []): Submission
    {
        static $seq = 1;
        $num = $seq++;

        return Submission::create(array_merge([
            'ticket_code' => sprintf('VTR-TEST-%04d', $num),
            'user_id' => $owner->id,
            'title' => "Video Uji {$num}",
            'pic_name' => 'PIC Uji',
            'whatsapp' => '081234567890',
            'organization' => 'Unit Uji',
            'gdrive_link' => 'https://drive.google.com/file/d/sample',
            'description' => 'Deskripsi materi uji',
            'content_type' => 'video',
            'video_duration_seconds' => 30,
            'start_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'end_date' => now()->timezone('Asia/Jakarta')->toDateString(),
            'status' => 'disetujui',
        ], $overrides));
    }

    public function test_active_broadcasts_for_videotron_only_returns_approved_videos_playing_today(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Audit']);
        $pengaju = User::factory()->create(['role' => 'pengaju']);
        $today = now()->timezone('Asia/Jakarta');

        $masjid = $this->createVideotron('Videotron Masjid', 'Depan Masjid');
        $febi = $this->createVideotron('Videotron FEBI', 'Atas Gedung FEBI');

        // 1. Disetujui & sedang tayang hari ini di Masjid -> HARUS MUNCUL di daftar tayang
        $liveMasjid = $this->createSubmission($pengaju, [
            'title' => 'Wisuda Aktif Hari Ini',
            'status' => 'disetujui',
            'start_date' => $today->copy()->subDay()->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
        ]);
        $liveMasjid->videotrons()->attach($masjid->id);

        // 2. Disetujui & baru mulai besok di Masjid -> TIDAK BOLEH muncul
        $futureMasjid = $this->createSubmission($pengaju, [
            'title' => 'Seminar Besok Terjadwal',
            'status' => 'disetujui',
            'start_date' => $today->copy()->addDay()->toDateString(),
            'end_date' => $today->copy()->addDays(3)->toDateString(),
        ]);
        $futureMasjid->videotrons()->attach($masjid->id);

        // 3. Disetujui & sudah berakhir kemarin di Masjid -> masuk daftar perlu diturunkan, BUKAN sedang tayang
        $expiredMasjid = $this->createSubmission($pengaju, [
            'title' => 'Dies Natalis Sudah Habis',
            'status' => 'disetujui',
            'start_date' => $today->copy()->subDays(4)->toDateString(),
            'end_date' => $today->copy()->subDay()->toDateString(),
        ]);
        $expiredMasjid->videotrons()->attach($masjid->id);

        // 4. Masih menunggu persetujuan di tanggal hari ini -> TIDAK BOLEH muncul
        $pendingMasjid = $this->createSubmission($pengaju, [
            'title' => 'Pengajuan Masih Menunggu',
            'status' => 'menunggu',
            'start_date' => $today->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
        ]);
        $pendingMasjid->videotrons()->attach($masjid->id);

        // 5. Ditolak di tanggal hari ini -> TIDAK BOLEH muncul
        $rejectedMasjid = $this->createSubmission($pengaju, [
            'title' => 'Konten Ditolak Admin',
            'status' => 'ditolak',
            'start_date' => $today->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
        ]);
        $rejectedMasjid->videotrons()->attach($masjid->id);

        // 6. Sedang tayang hari ini tapi di FEBI -> TIDAK BOLEH muncul di audit Masjid
        $liveFebi = $this->createSubmission($pengaju, [
            'title' => 'Kuliah Umum Khusus FEBI',
            'status' => 'disetujui',
            'start_date' => $today->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
        ]);
        $liveFebi->videotrons()->attach($febi->id);

        $service = app(VideotronPlaybackService::class);
        $activeForMasjid = $service->getActiveBroadcastsForVideotron($masjid);
        $pendingForMasjid = $service->getPendingTakedownsForVideotron($masjid);

        $this->assertSame([$liveMasjid->id], $activeForMasjid->pluck('id')->all());
        $this->assertSame([$expiredMasjid->id], $pendingForMasjid->pluck('id')->all());

        // Uji tabel Kelola Videotron: kembali lega tanpa kolom tambahan & tanpa tombol ikon ekstra
        Livewire::actingAs($admin)
            ->test(ListVideotrons::class)
            ->assertSee('Videotron Masjid')
            ->assertSee('1 tayang')
            ->assertSee('Videotron FEBI')
            ->assertDontSee('perlu diturunkan')
            ->assertTableActionExists('quick_condition')
            ->assertTableActionExists('edit');

        // Uji klik dari Dashboard: filter 1 baris (tableSearch) + buka otomatis laci Tayang Hari Ini via native Filament defaultTableAction (wire:init)
        Livewire::withQueryParams([
            'tableSearch' => $masjid->name,
            'tableAction' => 'inspect_broadcasts',
            'tableActionRecord' => (string) $masjid->id,
        ])
            ->actingAs($admin)
            ->test(ListVideotrons::class)
            ->assertCanSeeTableRecords([$masjid])
            ->assertCanNotSeeTableRecords([$febi])
            ->assertSet('defaultTableAction', 'inspect_broadcasts')
            ->assertSet('defaultTableActionRecord', (string) $masjid->id)
            ->assertSee('wire:init="mountTableAction(', false)
            ->call('mountTableAction', 'inspect_broadcasts', (string) $masjid->id)
            ->assertTableActionMounted('inspect_broadcasts')
            ->assertSee('Tayang Hari Ini — Videotron Masjid')
            ->assertSee('Wisuda Aktif Hari Ini')
            ->assertSee('1 konten habis masa tayang belum diturunkan')
            ->assertSee('Kelola semua di Riwayat')
            ->assertSee('Dies Natalis Sudah Habis')
            ->assertSee('sipetron-audit-chevron', false)
            ->assertDontSee('Sudah Diturunkan')
            ->assertDontSee('Batal')
            ->assertDontSee('Seminar Besok Terjadwal')
            ->assertDontSee('Pengajuan Masih Menunggu')
            ->assertDontSee('Konten Ditolak Admin')
            ->assertDontSee('Kuliah Umum Khusus FEBI');

        // Uji alias query ?inspect=ID juga memetakan ke native defaultTableAction (FEBI: tanpa tunggakan penurunan)
        Livewire::withQueryParams(['inspect' => $febi->id])
            ->actingAs($admin)
            ->test(ListVideotrons::class)
            ->assertSet('defaultTableAction', 'inspect_broadcasts')
            ->assertSet('defaultTableActionRecord', (string) $febi->id)
            ->call('mountTableAction', 'inspect_broadcasts', (string) $febi->id)
            ->assertTableActionMounted('inspect_broadcasts')
            ->assertSee('Tayang Hari Ini — Videotron FEBI')
            ->assertSee('Kuliah Umum Khusus FEBI')
            ->assertDontSee('konten habis masa tayang belum diturunkan')
            ->assertDontSee('Wisuda Aktif Hari Ini');

        // Pastikan metode takedown/undo di ListVideotrons & undo di service sudah dihapus total
        $this->assertFalse(method_exists(ListVideotrons::class, 'markBroadcastTakenDown'));
        $this->assertFalse(method_exists(ListVideotrons::class, 'undoBroadcastTakenDown'));
        $this->assertFalse(method_exists(VideotronPlaybackService::class, 'undoTakenDown'));

        // Pastikan chip Daftar Titik Layar di Dashboard membawa tableSearch + tableAction + tableActionRecord & bebas teks merah "• X turun"
        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('tableSearch=Videotron%20Masjid', false)
            ->assertSee('tableAction=inspect_broadcasts', false)
            ->assertSee('tableActionRecord='.$masjid->id, false)
            ->assertSee('1 tayang')
            ->assertDontSee('1 turun');
    }

    public function test_history_and_recap_takedown_checklist_records_admin_and_is_final(): void
    {
        $adminSatu = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pertama']);
        $adminDua = User::factory()->create(['role' => 'admin', 'name' => 'Admin Kedua']);
        $pengaju = User::factory()->create(['role' => 'pengaju']);
        $today = now()->timezone('Asia/Jakarta');

        $masjid = $this->createVideotron('Videotron Masjid', 'Depan Masjid');
        $febi = $this->createVideotron('Videotron FEBI', 'Atas Gedung FEBI');

        $expired = $this->createSubmission($pengaju, [
            'title' => 'Sosialisasi PMB Habis Tayang',
            'status' => 'disetujui',
            'start_date' => $today->copy()->subDays(5)->toDateString(),
            'end_date' => $today->copy()->subDays(2)->toDateString(),
        ]);
        $expired->videotrons()->attach([$masjid->id, $febi->id]);

        $service = app(VideotronPlaybackService::class);
        $this->assertSame(1, $service->countPendingTakedownSubmissions());
        $this->assertSame('1', HistoryAndRecap::getNavigationBadge());

        // Halaman Pengajuan tetap bersih dari checklist penurunan
        Livewire::actingAs($adminSatu)
            ->test(ListSubmissions::class)
            ->assertDontSee('Perlu Diturunkan')
            ->assertDontSee('Sosialisasi PMB Habis Tayang');

        // Dashboard menampilkan banner pengingat yang mengarah ke Riwayat & Rekap (filter perlu_diturunkan)
        $this->actingAs($adminSatu)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Pengingat Penurunan Konten')
            ->assertSee('1 Video')
            ->assertSee('filterStatus=perlu_diturunkan');

        // Di Riwayat & Rekap, video tampil dengan status Perlu Diturunkan & tombol Tandai Turun
        Livewire::actingAs($adminSatu)
            ->test(HistoryAndRecap::class)
            ->assertSee('Sosialisasi PMB Habis Tayang')
            ->assertSee('Perlu Diturunkan')
            ->assertSee('Tandai Turun')
            ->call('markSubmissionTakenDown', $expired->id)
            ->assertDispatched('sipetron:takedown-count-updated', count: 0)
            ->assertNotified()
            ->assertSee('Admin Pertama')
            ->assertDontSee('Tandai Turun');

        $this->assertSame(0, $service->countPendingTakedownSubmissions());
        $this->assertNull(HistoryAndRecap::getNavigationBadge());

        $checkedRows = DB::table('submission_videotron')
            ->where('submission_id', $expired->id)
            ->where('taken_down_by', $adminSatu->id)
            ->where('playback_status', 'selesai')
            ->whereNotNull('taken_down_at')
            ->count();

        $this->assertSame(2, $checkedRows);

        // Begitu sudah diturunkan, banner pengingat di Dashboard otomatis hilang
        $this->actingAs($adminDua)
            ->get('/admin')
            ->assertSuccessful()
            ->assertDontSee('Pengingat Penurunan Konten');

        // Di detail drawer Riwayat & Rekap, nama Admin Pertama bersifat final (tidak ada tombol Batalkan & tidak bisa ditimpa Admin Kedua)
        Livewire::actingAs($adminDua)
            ->test(HistoryAndRecap::class)
            ->call('selectSubmission', $expired->id)
            ->assertSee('Admin Pertama')
            ->assertDontSee('Batalkan')
            ->call('markSubmissionTakenDown', $expired->id);

        $stillOwnedByAdminSatu = DB::table('submission_videotron')
            ->where('submission_id', $expired->id)
            ->where('taken_down_by', $adminSatu->id)
            ->count();

        $this->assertSame(2, $stillOwnedByAdminSatu);
        $this->assertFalse(method_exists(HistoryAndRecap::class, 'undoSubmissionTakenDown'));
    }

    public function test_takedown_filter_exports_and_non_admin_authorization_guard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);
        $pengaju = User::factory()->create(['role' => 'pengaju', 'name' => 'Mahasiswa Uji']);
        $today = now()->timezone('Asia/Jakarta');

        $masjid = $this->createVideotron('Videotron Masjid', 'Depan Masjid');
        $rektorat = $this->createVideotron('Videotron Rektorat', 'Gedung Rektorat');

        // Status langsung 'selesai' di database juga wajib tertangkap oleh checklist penurunan
        $finished = $this->createSubmission($pengaju, [
            'title' => 'Festival Kampus Status Selesai',
            'status' => 'selesai',
            'start_date' => $today->copy()->subDays(7)->toDateString(),
            'end_date' => $today->copy()->subDays(3)->toDateString(),
        ]);
        $finished->videotrons()->attach([$masjid->id, $rektorat->id]);

        $rejected = $this->createSubmission($pengaju, [
            'title' => 'Pengajuan Ditolak Tidak Perlu Turun',
            'status' => 'ditolak',
            'start_date' => $today->copy()->subDays(5)->toDateString(),
            'end_date' => $today->copy()->subDays(2)->toDateString(),
        ]);
        $rejected->videotrons()->attach($masjid->id);

        $service = app(VideotronPlaybackService::class);
        $this->assertSame(1, $service->countPendingTakedownSubmissions());

        // Guard: non-admin tidak boleh mengubah status penurunan
        try {
            $service->markAllVideotronsTakenDown($finished, $pengaju);
            $this->fail('Seharusnya melempar DomainException untuk pengguna non-admin.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('Hanya admin', $e->getMessage());
        }

        // Guard: konten yang masih aktif tayang hari ini tidak boleh ditandai turun lebih awal
        $currentlyPlaying = $this->createSubmission($pengaju, [
            'title' => 'Konten Masih Tayang Hari Ini',
            'status' => 'disetujui',
            'start_date' => $today->copy()->subDay()->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
        ]);
        $currentlyPlaying->videotrons()->attach($masjid->id);

        try {
            $service->markAllVideotronsTakenDown($currentlyPlaying, $admin);
            $this->fail('Seharusnya menolak penurunan untuk konten yang belum habis masa tayangnya.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('setelah masa tayang berakhir', $e->getMessage());
        }

        // Guard: parameter filterStatus tidak valid di URL diabaikan dengan aman
        Livewire::withQueryParams(['filterStatus' => 'ngawur'])
            ->actingAs($admin)
            ->test(HistoryAndRecap::class)
            ->assertSet('filterStatus', '')
            ->assertDontSee('Status: Ngawur');

        // Filter perlu_diturunkan hanya menampilkan konten selesai tayang yang belum turun (bukan yang ditolak)
        Livewire::actingAs($admin)
            ->test(HistoryAndRecap::class)
            ->call('setFilterStatus', 'perlu_diturunkan')
            ->assertSee('Festival Kampus Status Selesai')
            ->assertDontSee('Pengajuan Ditolak Tidak Perlu Turun')
            ->set('tableSearch', 'kata-kunci-tidak-ada')
            ->assertSee('Tidak ada arsip pengajuan ditemukan')
            ->assertDontSee('Semua video habis masa tayang sudah diturunkan')
            ->set('tableSearch', '')
            ->call('markSubmissionTakenDown', $finished->id)
            ->assertNotified()
            ->assertSee('Semua video habis masa tayang sudah diturunkan');

        // Ceklis item terakhir pada halaman 2 filter perlu_diturunkan otomatis kembali ke halaman 1 tanpa empty-state palsu
        $extraPending = [];
        for ($i = 1; $i <= 6; $i++) {
            $item = $this->createSubmission($pengaju, [
                'title' => "Video Paging Turun {$i}",
                'status' => 'disetujui',
                'start_date' => $today->copy()->subDays(10 + $i)->toDateString(),
                'end_date' => $today->copy()->subDays($i)->toDateString(),
            ]);
            $item->videotrons()->attach($masjid->id);
            $extraPending[] = $item;
        }

        // Item ke-6 (end_date paling lama) berada di halaman 2 saat perPage=5
        Livewire::actingAs($admin)
            ->test(HistoryAndRecap::class)
            ->set('perPage', 5)
            ->call('setFilterStatus', 'perlu_diturunkan')
            ->call('setPage', 2)
            ->assertSee('Video Paging Turun 6')
            ->call('markSubmissionTakenDown', $extraPending[5]->id)
            ->assertSee('Video Paging Turun 1')
            ->assertDontSee('Semua video habis masa tayang sudah diturunkan');

        // Ekspor PDF dan Cetak HTML dengan filter perlu_diturunkan berjalan normal & label rapi (tanpa underscore)
        $this->actingAs($admin)
            ->get(route('admin.history-and-recap.export-pdf', ['status' => 'perlu_diturunkan']))
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('admin.history-and-recap.print', ['status' => 'perlu_diturunkan']))
            ->assertSuccessful()
            ->assertSee('Perlu Diturunkan')
            ->assertDontSee('Perlu_diturunkan');
    }

    public function test_videotron_table_and_audit_drawer_display_computed_aspect_ratio_from_resolution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $ultrawide = Videotron::create([
            'name' => 'Videotron Gerbang Ultrawide',
            'location' => 'Gerbang Utama',
            'resolution' => '2352x672',
            'orientation' => 'landscape',
            'operating_hours' => '06.00 – 22.00 WIB',
            'condition' => 'normal',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListVideotrons::class)
            ->assertSee('2352 × 672')
            ->assertSee('Landscape (7:2)')
            ->assertDontSee('Landscape (16:9)')
            ->mountTableAction('inspect_broadcasts', $ultrawide)
            ->assertSee('2352 × 672')
            ->assertSee('Landscape (7:2)')
            ->assertDontSee('Landscape (16:9)');
    }
}
