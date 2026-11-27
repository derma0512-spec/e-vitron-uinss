<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Tab 1: Pejabat & Dokumen Resmi (Kop Surat & Lembar Pengesahan)
            'signatory_head_title' => ['value' => 'Koordinator Pengelola E-VITRON', 'type' => 'string', 'group' => 'signatory'],
            'signatory_head_name' => ['value' => 'Riyanto, S.T., M.Kom.', 'type' => 'string', 'group' => 'signatory'],
            'signatory_head_nip' => ['value' => '19780512 200501 1 003', 'type' => 'string', 'group' => 'signatory'],
            'signatory_head_unit' => ['value' => 'UIN Siber Syekh Nurjati Cirebon', 'type' => 'string', 'group' => 'signatory'],
            'signatory_admin_title' => ['value' => 'Tim Pengelola E-VITRON', 'type' => 'string', 'group' => 'signatory'],
            'signatory_admin_unit' => ['value' => 'UIN Siber Syekh Nurjati Cirebon', 'type' => 'string', 'group' => 'signatory'],
            'signatory_admin_name' => ['value' => 'Tim Admin E-VITRON', 'type' => 'string', 'group' => 'signatory'],
            'institution_line_1' => ['value' => 'KEMENTERIAN AGAMA REPUBLIK INDONESIA', 'type' => 'string', 'group' => 'signatory'],
            'institution_line_2' => ['value' => 'UNIVERSITAS ISLAM NEGERI SIBER SYEKH NURJATI CIREBON', 'type' => 'string', 'group' => 'signatory'],
            'institution_line_3' => ['value' => 'LAYANAN PENGAJUAN PENAYANGAN VIDEOTRON (E-VITRON)', 'type' => 'string', 'group' => 'signatory'],
            'institution_address' => ['value' => 'Jalan Perjuangan By Pass Sunyaragi, Kota Cirebon, Jawa Barat 45132', 'type' => 'string', 'group' => 'signatory'],
            'institution_web' => ['value' => 'https://uinssc.ac.id', 'type' => 'string', 'group' => 'signatory'],
            'institution_email' => ['value' => 'info@uinssc.ac.id', 'type' => 'string', 'group' => 'signatory'],

            // Tab 2: Kebijakan & Aturan Pengajuan
            'min_days_before_enabled' => ['value' => true, 'type' => 'boolean', 'group' => 'policy'],
            'min_days_before' => ['value' => 3, 'type' => 'integer', 'group' => 'policy'],
            'limit_display_days_enabled' => ['value' => false, 'type' => 'boolean', 'group' => 'policy'],
            'max_display_days' => ['value' => 0, 'type' => 'integer', 'group' => 'policy'],
            'max_video_duration' => ['value' => 30, 'type' => 'integer', 'group' => 'policy'],
            'max_file_size' => ['value' => 50, 'type' => 'integer', 'group' => 'policy'],
            'template_doc_url' => ['value' => Setting::DEFAULT_TEMPLATE_DOC_URL, 'type' => 'string', 'group' => 'policy'],

            // Tab 3: Status Layanan & Pengumuman
            'service_active' => ['value' => true, 'type' => 'boolean', 'group' => 'service'],
            'service_closed_message' => ['value' => 'Layanan pengajuan penayangan videotron sedang ditutup sementara untuk pemeliharaan teknis berkala.', 'type' => 'string', 'group' => 'service'],
            'announcement_active' => ['value' => false, 'type' => 'boolean', 'group' => 'service'],
            'announcement_banner_text' => ['value' => 'Perhatian: Penayangan konten pada masa libur operasional kampus dijadwalkan secara terbatas.', 'type' => 'string', 'group' => 'service'],

            // Tab 4: Kontak & Helpdesk
            'helpdesk_whatsapp' => ['value' => '081234567890', 'type' => 'string', 'group' => 'helpdesk'],
            'contact_whatsapp' => ['value' => '081234567890', 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_email' => ['value' => 'info@uinssc.ac.id', 'type' => 'string', 'group' => 'helpdesk'],
            'contact_email' => ['value' => 'info@uinssc.ac.id', 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_hours' => ['value' => 'Senin – Jumat, 08.00 – 16.00 WIB', 'type' => 'string', 'group' => 'helpdesk'],
            'contact_service_hours' => ['value' => 'Senin – Jumat: 08.00 – 16.00 WIB', 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_location' => ['value' => 'Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon', 'type' => 'string', 'group' => 'helpdesk'],
            'contact_office_location' => ['value' => 'Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon', 'type' => 'string', 'group' => 'helpdesk'],
        ];

        foreach ($defaults as $key => $item) {
            if (Setting::where('key', $key)->exists()) {
                continue;
            }

            Setting::set($key, $item['value'], $item['type'], $item['group']);
        }
    }
}
