<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationAndProfileDropdownTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Videotron $videotron;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create([
            'name' => 'Ahmad Fadhil',
            'email' => '2208101001@student.uin.ac.id',
            'role' => 'mahasiswa',
            'identifier' => '2208101001',
            'unit_or_prodi' => 'Teknik Informatika',
        ]);

        $this->videotron = Videotron::create([
            'name' => 'Videotron 1',
            'location' => 'Kampus Utama',
            'resolution' => '1920x1080',
            'orientation' => 'landscape',
            'condition' => 'normal',
            'is_active' => true,
        ]);
    }

    /**
     * Test tampilan dropdown profil pengaju:
     * - Menampilkan data akun user yang login (nama, identitas, email, badge peran)
     * - Menu 'Pengajuan Saya' dan 'Ajukan Penayangan' sudah dihapus dari dropdown
     * - Menyediakan tombol Keluar (logout) dengan CSRF
     */
    public function test_profile_dropdown_shows_user_info_and_logout_only(): void
    {
        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertStatus(200);

        // Tombol profil di header menampilkan nama pengguna dan inisial
        $response->assertSee('Ahmad Fadhil');
        $response->assertSee('AF');

        // Pastikan dropdown profile hanya ada form logout satu baris
        $content = $response->getContent();
        $this->assertStringContainsString('id="profile-panel"', $content);

        // Di dalam profile-panel, pastikan tidak ada link Pengajuan Saya atau Ajukan Penayangan
        $profilePanelHtml = substr($content, strpos($content, 'id="profile-panel"'));
        $profilePanelHtml = substr($profilePanelHtml, 0, strpos($profilePanelHtml, '</header>'));

        $this->assertStringNotContainsString('Pengajuan Saya</a>', $profilePanelHtml);
        $this->assertStringNotContainsString('Ajukan Penayangan</a>', $profilePanelHtml);

        // Ada tombol logout di profile panel
        $this->assertStringContainsString('action="'.route('logout').'"', $profilePanelHtml);
        $this->assertStringContainsString('Keluar', $profilePanelHtml);
    }

    /**
     * Test proses logout mengakhiri sesi dan redirect ke login.
     */
    public function test_user_can_logout_safely(): void
    {
        $response = $this->actingAs($this->user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Test ketika pengajuan dibuat, otomatis memicu notifikasi pengajuan berhasil dikirim.
     */
    public function test_submission_creation_does_not_flood_applicant_drawer_with_self_notification(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post(route('pengajuan.store'), [
            'videotrons' => [$this->videotron->id],
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'title' => 'Seminar Nasional Kecerdasan Buatan',
            'content_type' => 'poster',
            'description' => 'Seminar nasional tentang pemanfaatan AI untuk pendidikan.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/seminar-ai',
            'surat_peminjaman' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasNoErrors();

        // Pengaju tidak dikirimi notifikasi "submitted" ke drawer agar laci tidak penuh hal yang sudah diketahui
        $this->assertEquals(0, $this->user->notifications()->count());
    }

    /**
     * Test perubahan status pengajuan menghasilkan notifikasi yang tepat:
     * disetujui, revisi (dengan catatan), ditolak (dengan alasan), selesai.
     */
    public function test_submission_status_changes_trigger_proper_notifications(): void
    {
        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-TEST1',
            'user_id' => $this->user->id,
            'title' => 'Festival Budaya Kampus',
            'content_type' => 'poster',
            'description' => 'Festival tahunan kampus.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/budaya',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);

        // Tidak ada notifikasi saat created bagi pengaju
        $this->assertEquals(0, $this->user->notifications()->count());

        // Status diubah menjadi 'revisi' dengan catatan admin
        $this->travel(1)->seconds();
        $submission->update([
            'status' => 'revisi',
            'admin_notes' => 'Tautan drive terkunci, mohon buka akses publik.',
        ]);

        $this->assertEquals(1, $this->user->fresh()->notifications()->count());
        $revisiNotif = $this->user->fresh()->notifications()->where('data->event', 'revisi')->first();
        $this->assertNotNull($revisiNotif);
        $this->assertEquals('Pengajuan Perlu Revisi', $revisiNotif->data['title']);
        $this->assertStringContainsString('Tautan drive terkunci', $revisiNotif->data['notes']);

        // Status diubah menjadi 'disetujui'
        $this->travel(1)->seconds();
        $submission->update(['status' => 'disetujui']);
        $this->assertEquals(2, $this->user->fresh()->notifications()->count());
        $approveNotif = $this->user->fresh()->notifications()->where('data->event', 'disetujui')->first();
        $this->assertNotNull($approveNotif);
        $this->assertEquals('Pengajuan Disetujui', $approveNotif->data['title']);

        // Status diubah menjadi 'selesai'
        $this->travel(1)->seconds();
        $submission->update(['status' => 'selesai']);
        $this->assertEquals(3, $this->user->fresh()->notifications()->count());
        $selesaiNotif = $this->user->fresh()->notifications()->where('data->event', 'selesai')->first();
        $this->assertNotNull($selesaiNotif);
        $this->assertEquals('Penayangan Selesai', $selesaiNotif->data['title']);

        // Status diubah menjadi 'ditolak' dengan alasan
        $this->travel(1)->seconds();
        $submission->update([
            'status' => 'ditolak',
            'admin_notes' => 'Jadwal videotron pada tanggal tersebut sudah penuh.',
        ]);
        $this->assertEquals(4, $this->user->fresh()->notifications()->count());
        $rejectNotif = $this->user->fresh()->notifications()->where('data->event', 'ditolak')->first();
        $this->assertNotNull($rejectNotif);
        $this->assertEquals('Pengajuan Ditolak', $rejectNotif->data['title']);
        $this->assertStringContainsString('Jadwal videotron pada tanggal tersebut sudah penuh', $rejectNotif->data['notes']);
    }

    /**
     * Test ketika user mengklik notifikasi, status berubah menjadi read di DB
     * dan dialihkan ke detail pengajuan terkait.
     */
    public function test_clicking_notification_marks_it_as_read_and_redirects(): void
    {
        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-TEST2',
            'user_id' => $this->user->id,
            'title' => 'Webinar Nasional',
            'content_type' => 'poster',
            'description' => 'Deskripsi webinar.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/webinar',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);

        $submission->update(['status' => 'disetujui']);
        $notification = $this->user->notifications()->first();
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->user)->get(route('notifikasi.read', $notification->id));

        $response->assertRedirect(route('pengajuan.index', [
            'open' => $submission->ticket_code,
            'status' => 'disetujui',
        ]));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * Test aksi menandai semua notifikasi sebagai sudah dibaca.
     */
    public function test_mark_all_notifications_as_read(): void
    {
        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-TEST3',
            'user_id' => $this->user->id,
            'title' => 'Lomba Esai Mahasiswa',
            'content_type' => 'poster',
            'description' => 'Lomba esai.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/lomba',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);

        $submission->update(['status' => 'revisi', 'admin_notes' => 'Catatan']);
        $this->travel(1)->seconds();
        $submission->update(['status' => 'disetujui']);

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post(route('notifikasi.markAllAsRead'));

        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    /**
     * Test isolasi akses: User B tidak boleh dapat mengakses atau menandai notifikasi User A.
     */
    public function test_user_cannot_access_other_users_notification(): void
    {
        $userB = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@student.uin.ac.id',
            'role' => 'mahasiswa',
        ]);

        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-TEST4',
            'user_id' => $this->user->id,
            'title' => 'Kuliah Umum',
            'content_type' => 'poster',
            'description' => 'Kuliah umum.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/kuliah',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);

        $submission->update(['status' => 'disetujui']);
        $notificationA = $this->user->notifications()->first();

        // User B mencoba membuka notifikasi User A -> 404
        $response = $this->actingAs($userB)->get(route('notifikasi.read', $notificationA->id));
        $response->assertStatus(404);

        // User B mencoba menandai dibaca -> 404
        $response = $this->actingAs($userB)->post(route('notifikasi.markAsRead', $notificationA->id));
        $response->assertStatus(404);

        // User B mencoba menghapus -> 404
        $response = $this->actingAs($userB)->delete(route('notifikasi.destroy', $notificationA->id));
        $response->assertStatus(404);
    }

    /**
     * Test akses /notifikasi dialihkan langsung ke drawer notifikasi di Pengajuan Saya.
     */
    public function test_full_notification_page_redirects_to_pengajuan_drawer(): void
    {
        $response = $this->actingAs($this->user)->get(route('notifikasi.index'));

        $response->assertRedirect(route('pengajuan.index', ['notif_drawer' => 1]));
    }

    /**
     * Test portal pengaju menyediakan slide-over notification drawer yang persis dengan panel admin:
     * - Tombol lonceng memanggil openNotifDrawer() dalam 1 kali klik
     * - Elemen slide-over drawer (#notif-drawer) dan backdrop (#notif-drawer-backdrop) berada di luar header
     * - Menyajikan daftar notifikasi lengkap dengan card compact (.sipetron-notif-card), tiket mono, catatan, dan tombol hapus
     */
    public function test_portal_renders_slide_over_notification_drawer(): void
    {
        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-DRAWER',
            'user_id' => $this->user->id,
            'title' => 'Pameran Fotografi Kampus',
            'content_type' => 'poster',
            'description' => 'Pameran foto tahunan.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/foto',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);

        $submission->update([
            'status' => 'revisi',
            'admin_notes' => 'Resolusi poster terlalu kecil, mohon unggah ukuran FHD.',
        ]);

        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Tombol lonceng sekali klik membuka drawer
        $this->assertStringContainsString('id="notif-btn"', $content);
        $this->assertStringContainsString('onclick="openNotifDrawer()"', $content);
        $this->assertStringContainsString('aria-controls="notif-drawer"', $content);

        // 2. Slide-over drawer dan backdrop ada di DOM di luar header
        $this->assertStringContainsString('id="notif-drawer-backdrop"', $content);
        $this->assertStringContainsString('id="notif-drawer"', $content);
        $this->assertStringContainsString('id="notif-drawer-list"', $content);

        // 3. Header identik dengan panel admin
        $this->assertStringContainsString('Notifikasi Masuk', $content);
        $this->assertStringContainsString('Pantau pembaruan pengajuan videotron Anda.', $content);
        $this->assertStringContainsString('Tandai Semua Dibaca', $content);
        $this->assertStringContainsString('Hapus Semua', $content);

        // 4. Konten notifikasi lengkap dengan class CSS admin (.sipetron-notif-card)
        $this->assertStringContainsString('sipetron-notif-card', $content);
        $this->assertStringContainsString('VTR-202610-DRAWER', $content);
        $this->assertStringContainsString('Pameran Fotografi Kampus', $content);
        $this->assertStringContainsString('Resolusi poster terlalu kecil', $content);
    }

    /**
     * Test aksi membersihkan/menghapus semua notifikasi milik pengaju.
     */
    public function test_clear_all_notifications(): void
    {
        $sub = Submission::create([
            'ticket_code' => 'VTR-202610-DEL1',
            'user_id' => $this->user->id,
            'title' => 'Lomba Esai',
            'content_type' => 'poster',
            'description' => 'Lomba esai.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/lomba',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);
        $sub->update(['status' => 'disetujui']);

        $this->assertGreaterThan(0, $this->user->notifications()->count());

        $response = $this->actingAs($this->user)->delete(route('notifikasi.clearAll'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->user->fresh()->notifications()->count());
    }

    /**
     * Test portal pengaju menyediakan floating toast notification pop-up persis seperti di admin:
     * - Wadah toast mengambang di pojok kanan (#sipetron-toast-container)
     * - Menampilkan kartu mint pastel (.sipetron-toast-card) saat session flash ada
     * - Memformat kode tiket dan tombol close
     */
    public function test_portal_renders_sipetron_floating_toast_notification(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['success' => 'Pengajuan berhasil dikirim untuk tiket VTR-202610-0099'])
            ->get(route('home'));

        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Container toast tersedia
        $this->assertStringContainsString('id="sipetron-toast-container"', $content);

        // 2. Kartu floating toast ter-render
        $this->assertStringContainsString('sipetron-toast-card', $content);
        $this->assertStringContainsString('sipetron-toast-ticket', $content);
        $this->assertStringContainsString('VTR-202610-0099', $content);
        $this->assertStringContainsString('sipetron-toast-close-btn', $content);
    }

    /**
     * Test endpoint polling notifikasi real-time untuk pengaju (/notifikasi/cek-terbaru).
     */
    public function test_check_unread_polling_endpoint_returns_live_data(): void
    {
        $submission = Submission::create([
            'ticket_code' => 'VTR-202610-POLL',
            'user_id' => $this->user->id,
            'title' => 'Webinar AI',
            'content_type' => 'poster',
            'description' => 'Webinar AI.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/ai',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'menunggu',
        ]);
        $submission->update(['status' => 'disetujui']);

        $response = $this->actingAs($this->user)->getJson(route('notifikasi.checkUnread'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'latest' => [
                'id',
                'title',
                'message',
                'ticket_code',
                'event',
                'type',
                'url',
            ],
        ]);

        $this->assertEquals(1, $response->json('unread_count'));
        $this->assertEquals('VTR-202610-POLL', $response->json('latest.ticket_code'));
    }

    /**
     * Test mode master-detail ketika dibuka dari notifikasi (Opsi 3):
     * - Mengarahkan ke tab status yang sesuai (misal status=revisi)
     * - Memuat seluruh kartu pada kategori tersebut di grid (bukan terisolasi 1 kartu saja)
     * - Banner peringatan hijau yang canggung dan memanjang sudah 100% dihapus
     */
    public function test_clicking_notification_activates_matching_status_tab_and_loads_category(): void
    {
        $submission1 = Submission::create([
            'ticket_code' => 'VTR-202610-ONE1',
            'user_id' => $this->user->id,
            'title' => 'Pengajuan Pertama Revisi',
            'content_type' => 'poster',
            'description' => 'Deskripsi 1.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/1',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'status' => 'revisi',
        ]);

        $this->user->notify(new SubmissionStatusNotification($submission1, 'revisi', 'Catatan revisi'));

        $submission2 = Submission::create([
            'ticket_code' => 'VTR-202610-TWO2',
            'user_id' => $this->user->id,
            'title' => 'Pengajuan Kedua Juga Revisi',
            'content_type' => 'video',
            'video_duration_seconds' => 30,
            'description' => 'Deskripsi 2.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/2',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'status' => 'revisi',
        ]);

        $submission3 = Submission::create([
            'ticket_code' => 'VTR-202610-THREE3',
            'user_id' => $this->user->id,
            'title' => 'Pengajuan Ketiga Menunggu',
            'content_type' => 'poster',
            'description' => 'Deskripsi 3.',
            'pic_name' => 'Ahmad Fadhil',
            'whatsapp' => '08123456789',
            'gdrive_link' => 'https://drive.google.com/drive/folders/3',
            'start_date' => now()->addDays(8),
            'end_date' => now()->addDays(10),
            'status' => 'menunggu',
        ]);

        // Simulasikan notifikasi untuk submission1 yang berstatus revisi
        $notif = $this->user->notifications()->where('data->ticket_code', 'VTR-202610-ONE1')->first();
        $redirectResponse = $this->actingAs($this->user)->get(route('notifikasi.read', $notif->id));

        // Redirect ke route pengajuan dengan tab status revisi dan open ticket (Opsi 3)
        $redirectResponse->assertRedirect(route('pengajuan.index', [
            'open' => 'VTR-202610-ONE1',
            'status' => 'revisi',
        ]));

        $response = $this->actingAs($this->user)->get(route('pengajuan.index', [
            'open' => 'VTR-202610-ONE1',
            'status' => 'revisi',
        ]));

        $response->assertStatus(200);

        // Memuat seluruh pengajuan pada tab revisi (2 item)
        $paginator = $response->viewData('submissions');
        $this->assertEquals(2, $paginator->total());

        // Banner peringatan hijau sudah tidak ada sama sekali
        $response->assertDontSee('Menampilkan 1 pengajuan terpilih dari notifikasi');
        $response->assertDontSee('Tampilkan Semua Pengajuan');
    }
}
