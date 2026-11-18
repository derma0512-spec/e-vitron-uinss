<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\AdminAlertNotification;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed data master perangkat videotron (aman dijalankan di semua environment)
        $videotrons = VideotronSeeder::seedMasterVideotrons();

        // Jangan pernah membuat akun demo berpassword lemah maupun pengajuan dummy di environment production
        if (app()->isProduction()) {
            $this->command?->warn('Akun demo dan pengajuan dummy dilewati pada environment production.');

            return;
        }

        $v1 = $videotrons['masjid'];
        $v2 = $videotrons['rektorat'];
        $vSyariah = $videotrons['syariah'];
        $vFITK = $videotrons['fitk'];
        $vSiber = $videotrons['siber'];
        $vFebi = $videotrons['febi'];

        // 2. Akun Admin Demo E-VITRON (Hanya untuk local / testing)
        $admin = User::updateOrCreate(
            ['email' => 'admin@uin.ac.id'],
            [
                'name' => 'Admin E-VITRON',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'identifier' => 'ADM-001',
                'phone' => '081234567890',
                'unit_or_prodi' => 'Tim Pengelola E-VITRON UIN SSC',
                'auth_type' => 'local',
            ]
        );

        // 3. Akun Mahasiswa Demo Utama (Ahmad Fadhil · Teknik Informatika)
        $userFadhil = User::updateOrCreate(
            ['identifier' => '2208101001'],
            [
                'name' => 'Ahmad Fadhil',
                'email' => '2208101001@student.uin.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone' => '08123456789',
                'unit_or_prodi' => 'Teknik Informatika',
                'auth_type' => 'campus_sso',
            ]
        );

        // 4. Akun Mahasiswa Tambahan (Rizky Budianto · FITK)
        $userMhs = User::updateOrCreate(
            ['identifier' => '2388010999'],
            [
                'name' => 'Rizky Budianto',
                'email' => '2388010999@student.uin.ac.id',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone' => '082198765432',
                'unit_or_prodi' => 'FITK',
                'auth_type' => 'campus_sso',
            ]
        );

        // 5. Akun Dosen
        User::updateOrCreate(
            ['identifier' => '198501012015011002'],
            [
                'name' => 'Dr. H. Syarifudin, M.Kom',
                'email' => 'syarifudin@uin.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'phone' => '081345678910',
                'unit_or_prodi' => 'Fakultas Sains dan Teknologi',
                'auth_type' => 'campus_sso',
            ]
        );

        // 6. Data Dummy Pengajuan Lengkap (Dimiliki oleh Akun Demo Ahmad Fadhil)
        // Card 1: Sosialisasi Penerimaan Mahasiswa Baru (Menunggu)
        $sub1 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202508-0001'],
            [
                'user_id' => $userFadhil->id,
                'title' => 'Sosialisasi Penerimaan Mahasiswa Baru',
                'content_type' => 'poster',
                'description' => 'Publikasi jalur pendaftaran mahasiswa baru UIN Siber SSC tahun akademik 2025/2026.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Humas Panitia PMB',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/pmb-uin-2025',
                'start_date' => '2026-10-23',
                'end_date' => '2026-10-25',
                'status' => 'menunggu',
                'admin_notes' => null,
                'material_downloaded_at' => null,
                'created_at' => '2026-10-13 17:17:05',
            ]
        );
        $sub1->videotrons()->sync([$v1->id]);
        $sub1->statusHistories()->delete();

        // Card 2: Pengumuman Wisuda UIN SSC 2025 (Disetujui)
        $sub2 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202508-0002'],
            [
                'user_id' => $userFadhil->id,
                'title' => 'Pengumuman Wisuda UIN SSC 2025',
                'content_type' => 'video',
                'video_duration_seconds' => 30,
                'description' => 'Video teaser jadwal gladi dan pelaksanaan wisuda sarjana & magister UIN SSC.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Bagian Akademik',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/wisuda-ssc-2025',
                'start_date' => '2025-08-20',
                'end_date' => '2025-08-22',
                'status' => 'disetujui',
                'created_at' => '2026-10-13 17:17:04',
            ]
        );
        $sub2->videotrons()->sync([$vFebi->id]);

        // Card 3: Undangan Kuliah Umum Fakultas Syariah (Perlu Revisi)
        $sub3 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202509-0003'],
            [
                'user_id' => $userFadhil->id,
                'title' => 'Undangan Kuliah Umum Fakultas Syariah',
                'content_type' => 'poster',
                'description' => 'Kuliah umum pakar hukum ekonomi syariah nasional.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'DEMA Fakultas Syariah',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/kuliah-umum-syariah',
                'start_date' => '2025-09-05',
                'end_date' => '2025-09-07',
                'status' => 'revisi',
                'admin_notes' => 'Tautan Google Drive masih belum di-set open public. Mohon ubah akses menjadi Anyone with link.',
                'created_at' => '2026-10-13 17:17:03',
            ]
        );
        $sub3->videotrons()->sync([$vFITK->id]);

        // Card 4: Workshop UI/UX Design & Frontend Modern UIN SSC (Menunggu)
        $sub4 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0004'],
            [
                'user_id' => $userFadhil->id,
                'title' => 'Workshop UI/UX Design & Frontend Modern UIN SSC',
                'content_type' => 'poster',
                'description' => 'Publikasi kegiatan workshop desain antarmuka bagi mahasiswa Teknik Informatika.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Himpunan Mahasiswa Informatika',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/workshop-uiux-2026',
                'start_date' => '2026-10-15',
                'end_date' => '2026-10-18',
                'status' => 'menunggu',
                'created_at' => '2026-10-13 21:16:13',
            ]
        );
        $sub4->videotrons()->sync([$vSiber->id, $vFebi->id]);

        // Card 5: Sosialisasi AI dan Pemrograman (Menunggu)
        $sub5 = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0005'],
            [
                'user_id' => $userFadhil->id,
                'title' => 'Sosialisasi AI dan Pemrograman',
                'content_type' => 'video',
                'video_duration_seconds' => 30,
                'description' => 'Sosialisasi pemanfaatan artificial intelligence dalam pemrograman modern untuk mahasiswa UIN SSC.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Himpunan Mahasiswa Informatika',
                'whatsapp' => '08123456789',
                'gdrive_link' => 'https://drive.google.com/drive/folders/sosialisasi-ai-2026',
                'start_date' => '2026-10-25',
                'end_date' => '2026-10-28',
                'status' => 'menunggu',
                'admin_notes' => null,
                'material_downloaded_at' => null,
                'created_at' => '2026-10-14 00:36:16',
            ]
        );
        $sub5->videotrons()->sync([$v1->id]);
        $sub5->statusHistories()->delete();

        // 7. Notifikasi awal untuk pengujian sistem
        if ($userFadhil->notifications()->count() === 0) {
            $userFadhil->notify(new SubmissionStatusNotification($sub5, 'submitted'));
            $userFadhil->notify(new SubmissionStatusNotification($sub4, 'submitted'));
            $userFadhil->notify(new SubmissionStatusNotification($sub1, 'submitted'));
            $userFadhil->notify(new SubmissionStatusNotification($sub2, 'disetujui'));
            $userFadhil->notify(new SubmissionStatusNotification($sub3, 'revisi', $sub3->admin_notes));
        }

        if ($admin->notifications()->count() === 0) {
            $admin->notify(new AdminAlertNotification(
                title: 'Pengajuan Baru Masuk',
                message: 'Pengajuan baru VTR-202610-0005 (Sosialisasi AI dan Pemrograman) siap ditinjau.',
                type: 'submission_created',
                url: url("/admin/submissions?selected={$sub5->id}&from=notification"),
                ticketCode: $sub5->ticket_code,
                submissionId: $sub5->id
            ));
            $admin->notify(new AdminAlertNotification(
                title: 'Pengajuan Baru Masuk',
                message: 'Pengajuan baru VTR-202610-0004 (Workshop UI/UX Design System) siap ditinjau.',
                type: 'submission_created',
                url: url("/admin/submissions?selected={$sub4->id}&from=notification"),
                ticketCode: $sub4->ticket_code,
                submissionId: $sub4->id
            ));
            $admin->notify(new AdminAlertNotification(
                title: 'Revisi Pengajuan Dikirim',
                message: 'Pengaju telah mengirim perbaikan untuk tiket VTR-202610-0003.',
                type: 'submission_revised',
                url: url("/admin/submissions?selected={$sub3->id}&from=notification"),
                ticketCode: $sub3->ticket_code,
                submissionId: $sub3->id
            ));
        }
    }
}
