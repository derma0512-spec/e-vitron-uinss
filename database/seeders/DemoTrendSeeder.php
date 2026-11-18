<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\SubmissionStatusHistory;
use App\Models\User;
use App\Models\Videotron;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTrendSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $userFadhil = User::where('email', '2208101001@student.uin.ac.id')->first() ?? User::find(2);
        $userDosen = User::where('email', 'syarifudin@uin.ac.id')->first() ?? User::find(3);
        $userRizky = User::where('email', '2388010999@student.uin.ac.id')->first() ?? User::find(4);

        $v1 = Videotron::find(1);
        $v2 = Videotron::find(2);
        $vFebi = Videotron::find(3);
        $vFITK = Videotron::find(4);
        $vSiber = Videotron::find(5);

        // 1. UPDATE created_at DATA LAMA (Mempertahankan ID dan Kode Tiket)
        // Tiket VTR-202508-0001 -> Agustus 2026
        DB::table('submissions')->where('ticket_code', 'VTR-202508-0001')->update([
            'created_at' => '2026-08-10 09:15:00',
            'updated_at' => '2026-08-10 09:15:00',
        ]);

        // Tiket VTR-202508-0002 -> Agustus 2026 (status disetujui)
        DB::table('submissions')->where('ticket_code', 'VTR-202508-0002')->update([
            'created_at' => '2026-08-18 13:20:00',
            'updated_at' => '2026-08-18 13:20:00',
        ]);
        $subWisuda = Submission::where('ticket_code', 'VTR-202508-0002')->first();
        if ($subWisuda && $vFebi) {
            $subWisuda->videotrons()->sync([
                $vFebi->id => ['playback_status' => 'selesai', 'created_at' => '2026-08-18 15:30:00', 'updated_at' => '2026-08-18 15:30:00'],
            ]);
        }

        // Tiket VTR-202509-0003 -> September 2026 (status revisi)
        DB::table('submissions')->where('ticket_code', 'VTR-202509-0003')->update([
            'created_at' => '2026-09-04 10:30:00',
            'updated_at' => '2026-09-04 10:30:00',
        ]);

        // 2. TAMBAH DATA BARU SECARA LENGKAP
        // Data Baru 1: Mei 2026 (Status: selesai)
        $subMei = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202605-0001'],
            [
                'user_id' => $userDosen->id ?? 3,
                'title' => 'Seminar Nasional Keamanan Siber dan Transformasi AI',
                'content_type' => 'poster',
                'description' => 'Publikasi seminar nasional cyber security bagi mahasiswa dan dosen UIN Siber SSC.',
                'pic_name' => 'Dr. H. Syarifudin, M.Kom',
                'organization' => 'Pusat Studi Siber & TI',
                'whatsapp' => '08123456780',
                'gdrive_link' => 'https://drive.google.com/drive/folders/seminar-cyber-2026',
                'start_date' => '2026-05-15',
                'end_date' => '2026-05-18',
                'status' => 'selesai',
                'created_at' => '2026-05-12 09:00:00',
                'updated_at' => '2026-05-12 11:30:00',
            ]
        );
        if ($vSiber && $vFITK) {
            $subMei->videotrons()->sync([
                $vSiber->id => ['playback_status' => 'selesai', 'created_at' => '2026-05-12 11:30:00', 'updated_at' => '2026-05-18 18:00:00'],
                $vFITK->id => ['playback_status' => 'selesai', 'created_at' => '2026-05-12 11:30:00', 'updated_at' => '2026-05-18 18:00:00'],
            ]);
        }

        // Data Baru 2: Juni 2026 (Status: selesai, playback_status: terganggu)
        $subJuni1 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202606-0001'],
            [
                'user_id' => $userRizky->id ?? 4,
                'title' => 'Sosialisasi Beasiswa KIP Kuliah & Prestasi 2026',
                'content_type' => 'video',
                'video_duration_seconds' => 30,
                'description' => 'Video informasi pendaftaran dan syarat beasiswa KIP Kuliah tahun 2026.',
                'pic_name' => 'Rizky Budianto',
                'organization' => 'Bagian Kemahasiswaan',
                'whatsapp' => '08123456781',
                'gdrive_link' => 'https://drive.google.com/drive/folders/beasiswa-kip-2026',
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-12',
                'status' => 'selesai',
                'created_at' => '2026-06-08 08:30:00',
                'updated_at' => '2026-06-08 10:15:00',
            ]
        );
        if ($v2) {
            $subJuni1->videotrons()->sync([
                $v2->id => ['playback_status' => 'terganggu', 'created_at' => '2026-06-08 10:15:00', 'updated_at' => '2026-06-11 14:00:00'],
            ]);
        }

        // Data Baru 3: Juni 2026 (Status: ditolak, TANPA PIVOT VIDEOTRON)
        $subJuni2 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202606-0002'],
            [
                'user_id' => $userFadhil->id ?? 2,
                'title' => 'Lomba Esai Kebangsaan DEMA FITK',
                'content_type' => 'poster',
                'description' => 'Perlombaan esai mahasiswa tingkat nasional.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'DEMA FITK',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/lomba-esai-2026',
                'start_date' => '2026-06-25',
                'end_date' => '2026-06-27',
                'status' => 'ditolak',
                'admin_notes' => 'Rasio dan resolusi poster tidak sesuai standar 1080x1920 (portrait) serta teks sulit terbaca.',
                'created_at' => '2026-06-20 14:00:00',
                'updated_at' => '2026-06-20 16:30:00',
            ]
        );
        $subJuni2->videotrons()->detach();

        // Data Baru 4: Juli 2026 (Status: selesai)
        $subJuli = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202607-0001'],
            [
                'user_id' => $userDosen->id ?? 3,
                'title' => 'Peringatan Tahun Baru Islam 1448 H & Doa Bersama',
                'content_type' => 'poster',
                'description' => 'Kajian akbar dan doa bersama civitas akademika UIN Siber SSC.',
                'pic_name' => 'Dr. H. Syarifudin, M.Kom',
                'organization' => 'Pusat Kerohanian & Masjid Kampus',
                'whatsapp' => '08123456780',
                'gdrive_link' => 'https://drive.google.com/drive/folders/muharram-1448',
                'start_date' => '2026-07-08',
                'end_date' => '2026-07-10',
                'status' => 'selesai',
                'created_at' => '2026-07-06 09:15:00',
                'updated_at' => '2026-07-06 11:45:00',
            ]
        );
        if ($vFebi && $v1 && $vFITK) {
            $subJuli->videotrons()->sync([
                $vFebi->id => ['playback_status' => 'selesai', 'created_at' => '2026-07-06 11:45:00', 'updated_at' => '2026-07-10 18:00:00'],
                $v1->id => ['playback_status' => 'selesai', 'created_at' => '2026-07-06 11:45:00', 'updated_at' => '2026-07-10 18:00:00'],
                $vFITK->id => ['playback_status' => 'selesai', 'created_at' => '2026-07-06 11:45:00', 'updated_at' => '2026-07-10 18:00:00'],
            ]);
        }

        // Data Baru 5: September 2026 (Status: ditolak, TANPA PIVOT VIDEOTRON)
        $subSept = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202609-0001'],
            [
                'user_id' => $userRizky->id ?? 4,
                'title' => 'Bazar Buku & Kuliner Kewirausahaan Mahasiswa',
                'content_type' => 'poster',
                'description' => 'Publikasi stan UMKM dan bazar buku di lapangan rektorat.',
                'pic_name' => 'Rizky Budianto',
                'organization' => 'UKM Kewirausahaan',
                'whatsapp' => '08123456781',
                'gdrive_link' => 'https://drive.google.com/drive/folders/bazar-umkm-2026',
                'start_date' => '2026-09-22',
                'end_date' => '2026-09-24',
                'status' => 'ditolak',
                'admin_notes' => 'Pengajuan ditolak karena belum melampirkan surat rekomendasi resmi dari pimpinan fakultas/rektorat.',
                'created_at' => '2026-09-18 10:00:00',
                'updated_at' => '2026-09-18 13:30:00',
            ]
        );
        $subSept->videotrons()->detach();

        // Data Baru 6: Agustus 2026 (Status: dibatalkan, TANPA PIVOT VIDEOTRON)
        $subBatal1 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202608-0003'],
            [
                'user_id' => $userFadhil->id ?? 2,
                'title' => 'Sosialisasi Lomba Debat Bahasa Arab UKM Nafilah',
                'content_type' => 'poster',
                'description' => 'Publikasi pembukaan pendaftaran debat bahasa Arab tingkat regional.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'UKM Nafilah',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/debat-arab-2026',
                'start_date' => '2026-08-25',
                'end_date' => '2026-08-27',
                'status' => 'dibatalkan',
                'admin_notes' => null,
                'created_at' => '2026-08-20 11:00:00',
                'updated_at' => '2026-08-21 09:30:00',
            ]
        );
        $subBatal1->videotrons()->detach();

        // Data Baru 7: Oktober 2026 (Status: disetujui, Sedang Tayang: start today()-2, end today()+3)
        $subTayang = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0007'],
            [
                'user_id' => $userDosen->id ?? 3,
                'title' => 'Pameran Karya Inovasi Teknologi Mahasiswa 2026',
                'content_type' => 'poster',
                'description' => 'Pameran prototipe aplikasi dan riset terapan karya mahasiswa UIN Siber SSC.',
                'pic_name' => 'Dr. H. Syarifudin, M.Kom',
                'organization' => 'Fakultas Sains & Teknologi Siber',
                'whatsapp' => '08123456780',
                'gdrive_link' => 'https://drive.google.com/drive/folders/pameran-inovasi-2026',
                'start_date' => today()->subDays(2)->toDateString(),
                'end_date' => today()->addDays(3)->toDateString(),
                'status' => 'disetujui',
                'admin_notes' => 'Pengajuan disetujui untuk tayang di Videotron Siber.',
                'created_at' => today()->subDays(4)->setTime(9, 0, 0),
                'updated_at' => today()->subDays(3)->setTime(10, 30, 0),
            ]
        );
        if ($vSiber) {
            $subTayang->videotrons()->sync([
                $vSiber->id => ['playback_status' => 'tayang', 'created_at' => today()->subDays(3)->setTime(10, 30, 0), 'updated_at' => today()->subDays(3)->setTime(10, 30, 0)],
            ]);
        }

        // Data Baru 8: Oktober 2026 (Status: dibatalkan, TANPA PIVOT VIDEOTRON)
        $subBatal2 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0008'],
            [
                'user_id' => $userRizky->id ?? 4,
                'title' => 'Workshop Penulisan Karya Ilmiah Populer BEM',
                'content_type' => 'poster',
                'description' => 'Pelatihan penulisan artikel ilmiah dan opini di media massa.',
                'pic_name' => 'Rizky Budianto',
                'organization' => 'BEM Universitas',
                'whatsapp' => '08123456781',
                'gdrive_link' => 'https://drive.google.com/drive/folders/workshop-ilmiah-2026',
                'start_date' => today()->addDays(5)->toDateString(),
                'end_date' => today()->addDays(7)->toDateString(),
                'status' => 'dibatalkan',
                'admin_notes' => null,
                'created_at' => today()->subDays(3)->setTime(14, 0, 0),
                'updated_at' => today()->subDays(2)->setTime(16, 0, 0),
            ]
        );
        $subBatal2->videotrons()->detach();

        // Data Baru 9: Oktober 2026 (Status: disetujui, Terjadwal: start today()+2, end today()+5)
        $subTerjadwal = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0009'],
            [
                'user_id' => $userFadhil->id ?? 2,
                'title' => 'Kuliah Tamu Internasional AI & Green Technology',
                'content_type' => 'poster',
                'description' => 'Kuliah umum pakar internasional bidang komputasi hijau dan kecerdasan buatan.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'DEMA FITK',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/kuliah-tamu-ai-2026',
                'start_date' => today()->addDays(2)->toDateString(),
                'end_date' => today()->addDays(5)->toDateString(),
                'status' => 'disetujui',
                'admin_notes' => 'Pengajuan disetujui untuk persiapan penayangan di Videotron Masjid.',
                'created_at' => today()->subDays(1)->setTime(10, 30, 0),
                'updated_at' => today()->subDays(1)->setTime(14, 0, 0),
            ]
        );
        if ($v1) {
            $subTerjadwal->videotrons()->sync([
                $v1->id => ['playback_status' => 'terjadwal', 'created_at' => today()->subDays(1)->setTime(14, 0, 0), 'updated_at' => today()->subDays(1)->setTime(14, 0, 0)],
            ]);
        }

        // 3. RIWAYAT STATUS (submission_status_histories)
        // ID 3: Pengumuman Wisuda UIN SSC 2025 (disetujui)
        if ($subWisuda) {
            SubmissionStatusHistory::updateOrCreate(
                ['submission_id' => $subWisuda->id, 'to_status' => 'disetujui'],
                [
                    'user_id' => $admin->id ?? 1,
                    'from_status' => 'menunggu',
                    'notes' => 'Pengajuan telah diperiksa, jadwal penayangan disetujui untuk Videotron Febi.',
                    'created_at' => '2026-08-18 15:30:00',
                    'updated_at' => '2026-08-18 15:30:00',
                ]
            );
        }

        // Histori Mei 2026 (disetujui)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subMei->id, 'to_status' => 'disetujui'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Konten seminar disetujui untuk tayang di Videotron Siber & FITK.',
                'created_at' => '2026-05-12 11:30:00',
                'updated_at' => '2026-05-12 11:30:00',
            ]
        );

        // Histori Juni 2026 #1 (disetujui)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subJuni1->id, 'to_status' => 'disetujui'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Video sosialisasi KIP disetujui tayang.',
                'created_at' => '2026-06-08 10:15:00',
                'updated_at' => '2026-06-08 10:15:00',
            ]
        );

        // Histori Juni 2026 #2 (ditolak)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subJuni2->id, 'to_status' => 'ditolak'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Rasio dan resolusi poster tidak sesuai standar 1080x1920 (portrait) serta teks sulit terbaca.',
                'created_at' => '2026-06-20 16:30:00',
                'updated_at' => '2026-06-20 16:30:00',
            ]
        );

        // Histori Juli 2026 (disetujui)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subJuli->id, 'to_status' => 'disetujui'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Poster kajian Muharram disetujui.',
                'created_at' => '2026-07-06 11:45:00',
                'updated_at' => '2026-07-06 11:45:00',
            ]
        );

        // Histori September 2026 (ditolak)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subSept->id, 'to_status' => 'ditolak'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Pengajuan ditolak karena belum melampirkan surat rekomendasi resmi dari pimpinan fakultas/rektorat.',
                'created_at' => '2026-09-18 13:30:00',
                'updated_at' => '2026-09-18 13:30:00',
            ]
        );

        // Histori Batal 1 (Agustus 2026)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subBatal1->id, 'to_status' => 'dibatalkan'],
            [
                'user_id' => $userFadhil->id ?? 2,
                'from_status' => 'menunggu',
                'notes' => 'Dibatalkan oleh pengaju.',
                'created_at' => '2026-08-21 09:30:00',
                'updated_at' => '2026-08-21 09:30:00',
            ]
        );

        // Histori Sedang Tayang (Oktober 2026)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subTayang->id, 'to_status' => 'disetujui'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Pengajuan disetujui untuk tayang di Videotron Siber.',
                'created_at' => today()->subDays(3)->setTime(10, 30, 0),
                'updated_at' => today()->subDays(3)->setTime(10, 30, 0),
            ]
        );

        // Histori Batal 2 (Oktober 2026)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subBatal2->id, 'to_status' => 'dibatalkan'],
            [
                'user_id' => $userRizky->id ?? 4,
                'from_status' => 'menunggu',
                'notes' => 'Dibatalkan oleh pengaju sebelum pemeriksaan admin.',
                'created_at' => today()->subDays(2)->setTime(16, 0, 0),
                'updated_at' => today()->subDays(2)->setTime(16, 0, 0),
            ]
        );

        // Histori Terjadwal (Oktober 2026)
        SubmissionStatusHistory::updateOrCreate(
            ['submission_id' => $subTerjadwal->id, 'to_status' => 'disetujui'],
            [
                'user_id' => $admin->id ?? 1,
                'from_status' => 'menunggu',
                'notes' => 'Pengajuan disetujui untuk persiapan penayangan di Videotron Masjid.',
                'created_at' => today()->subDays(1)->setTime(14, 0, 0),
                'updated_at' => today()->subDays(1)->setTime(14, 0, 0),
            ]
        );
    }
}
