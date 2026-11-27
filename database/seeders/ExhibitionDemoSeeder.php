<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\User;
use App\Notifications\AdminAlertNotification;
use App\Notifications\SubmissionStatusNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExhibitionDemoSeeder extends Seeder
{
    /**
     * Seed akun Admin Kurator & Pemohon Demo khusus pameran (Railway) jika variabel DEMO_ADMIN_EMAIL diset.
     */
    public function run(): void
    {
        $adminEmail = trim((string) config('services.demo.admin_email'));
        $adminPassword = (string) config('services.demo.admin_password');

        if ($adminEmail === '' || $adminPassword === '') {
            return;
        }

        $applicantIdentifier = trim((string) config('services.demo.applicant_identifier', '2208101001'));
        $applicantEmail = trim((string) config('services.demo.applicant_email', '2208101001@student.uin.ac.id'));
        $applicantPassword = (string) (config('services.demo.applicant_password') ?: $adminPassword);

        $videotrons = VideotronSeeder::seedMasterVideotrons();

        $admin = User::updateOrCreate(
            ['email' => strtolower($adminEmail)],
            [
                'name' => 'Admin E-VITRON',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'identifier' => 'ADM-001',
                'phone' => '081234567890',
                'unit_or_prodi' => 'Tim Pengelola E-VITRON UIN SSC',
                'auth_type' => 'local',
                'account_status' => 'approved',
            ]
        );
        $admin->is_super_admin = false;
        $admin->save();

        $applicant = User::updateOrCreate(
            ['identifier' => $applicantIdentifier],
            [
                'name' => 'Ahmad Fadhil',
                'email' => strtolower($applicantEmail),
                'password' => Hash::make($applicantPassword),
                'role' => 'mahasiswa',
                'phone' => '081234567890',
                'unit_or_prodi' => 'Informatika',
                'auth_type' => 'campus_sso',
                'account_status' => 'approved',
            ]
        );

        // Hanya buat contoh pengajuan awal jika akun pemohon demo belum memiliki pengajuan sama sekali
        if ($applicant->submissions()->count() > 0) {
            return;
        }

        $subApproved = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0001'],
            [
                'user_id' => $applicant->id,
                'title' => 'Pengumuman Wisuda & Gladi Bersih UIN SSC',
                'content_type' => 'video',
                'video_duration_seconds' => 30,
                'description' => 'Video publikasi jadwal gladi bersih dan pelaksanaan wisuda sarjana & pascasarjana UIN Siber Syekh Nurjati Cirebon.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Bagian Akademik & Kemahasiswaan',
                'whatsapp' => '081234567890',
                'gdrive_link' => 'https://drive.google.com/drive/folders/wisuda-uinssc-demo',
                'start_date' => now()->addDays(2)->format('Y-m-d'),
                'end_date' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'disetujui',
            ]
        );
        $subApproved->videotrons()->sync([$videotrons['rektorat']->id, $videotrons['febi']->id]);

        $subRevision = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0002'],
            [
                'user_id' => $applicant->id,
                'title' => 'Undangan Kuliah Umum Ekonomi Syariah Digital',
                'content_type' => 'poster',
                'description' => 'Publikasi kuliah umum nasional transformasi ekonomi syariah digital bagi sivitas akademika.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'DEMA Fakultas Syariah',
                'whatsapp' => '081234567890',
                'gdrive_link' => 'https://drive.google.com/drive/folders/kuliah-umum-syariah-demo',
                'start_date' => now()->addDays(4)->format('Y-m-d'),
                'end_date' => now()->addDays(6)->format('Y-m-d'),
                'status' => 'revisi',
                'admin_notes' => 'Tautan Google Drive masih terkunci (Restricted). Mohon ubah izin akses menjadi Anyone with the link (Siapa saja yang memiliki link).',
            ]
        );
        $subRevision->videotrons()->sync([$videotrons['syariah']->id]);

        $subPending = Submission::updateOrCreate(
            ['ticket_code' => 'VTR-202610-0003'],
            [
                'user_id' => $applicant->id,
                'title' => 'Sosialisasi Pameran Inovasi Digital & AI Kampus Siber',
                'content_type' => 'video',
                'video_duration_seconds' => 30,
                'description' => 'Publikasi kegiatan pameran karya inovasi teknologi informasi dan kecerdasan buatan mahasiswa UIN SSC.',
                'pic_name' => 'Ahmad Fadhil',
                'organization' => 'Himpunan Mahasiswa Informatika',
                'whatsapp' => '081234567890',
                'gdrive_link' => 'https://drive.google.com/drive/folders/pameran-ai-uinssc-demo',
                'start_date' => now()->addDays(3)->format('Y-m-d'),
                'end_date' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'menunggu',
            ]
        );
        $subPending->videotrons()->sync([$videotrons['masjid']->id, $videotrons['siber']->id]);

        if ($applicant->notifications()->count() === 0) {
            $applicant->notify(new SubmissionStatusNotification($subPending, 'submitted'));
            $applicant->notify(new SubmissionStatusNotification($subRevision, 'revisi', $subRevision->admin_notes));
            $applicant->notify(new SubmissionStatusNotification($subApproved, 'disetujui'));
        }

        if ($admin->notifications()->count() === 0) {
            $admin->notify(new AdminAlertNotification(
                title: 'Pengajuan Baru Masuk',
                message: "Pengajuan baru {$subPending->ticket_code} ({$subPending->title}) siap ditinjau.",
                type: 'submission_created',
                url: url("/admin/submissions?selected={$subPending->id}&from=notification"),
                ticketCode: $subPending->ticket_code,
                submissionId: $subPending->id
            ));
        }
    }
}
