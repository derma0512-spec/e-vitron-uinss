<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.manage-settings';

    public function getHeading(): string
    {
        return 'Pengaturan Sistem';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola pejabat penandatangan, kebijakan pengajuan, status layanan, dan kontak helpdesk E-VITRON.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public string $activeTab = 'signatory'; // 'signatory', 'policy', 'service', 'helpdesk'

    protected $queryString = [
        'activeTab' => ['as' => 'tab', 'except' => 'signatory'],
    ];

    // Tab 1: Penandatangan & Kop Surat
    public string $signatory_admin_title = '';

    public string $signatory_admin_unit = '';

    public string $signatory_admin_name = '';

    public string $institution_line_1 = '';

    public string $institution_line_2 = '';

    public string $institution_line_3 = '';

    public string $institution_address = '';

    public string $institution_web = '';

    public string $institution_email = '';

    // Tab 2: Kebijakan & Aturan Pengajuan
    public int $min_days_before = 3;

    public bool $limit_display_days_enabled = false;

    public int $max_display_days = 7;

    public int $max_video_duration = 30;

    public int $max_file_size = 50;

    public string $template_doc_url = '';

    // Tab 3: Status Layanan & Pengumuman
    public bool $service_active = true;

    public string $service_closed_message = '';

    public bool $announcement_active = false;

    public string $announcement_banner_text = '';

    // Tab 4: Kontak & Helpdesk
    public string $helpdesk_whatsapp = '';

    public string $helpdesk_email = '';

    public string $helpdesk_hours = '';

    public string $helpdesk_location = '';

    public function mount(): void
    {
        $requestedTab = request()->query('tab');
        if (is_string($requestedTab) && in_array($requestedTab, ['signatory', 'policy', 'service', 'helpdesk'], true)) {
            $this->activeTab = $requestedTab;
        }

        $settings = Setting::allCached();

        // Tab 1
        $this->signatory_admin_title = (string) ($settings['signatory_admin_title'] ?? 'Tim Pengelola E-VITRON');
        $this->signatory_admin_unit = (string) ($settings['signatory_admin_unit'] ?? 'UIN Siber Syekh Nurjati Cirebon');
        $this->signatory_admin_name = (string) ($settings['signatory_admin_name'] ?? 'Tim Admin E-VITRON');
        $this->institution_line_1 = (string) ($settings['institution_line_1'] ?? 'KEMENTERIAN AGAMA REPUBLIK INDONESIA');
        $this->institution_line_2 = (string) ($settings['institution_line_2'] ?? 'UNIVERSITAS ISLAM NEGERI SIBER SYEKH NURJATI CIREBON');
        $this->institution_line_3 = (string) ($settings['institution_line_3'] ?? 'LAYANAN PENGAJUAN PENAYANGAN VIDEOTRON (E-VITRON)');
        $this->institution_address = (string) ($settings['institution_address'] ?? 'Jalan Perjuangan By Pass Sunyaragi, Kota Cirebon, Jawa Barat 45132');
        $this->institution_web = (string) ($settings['institution_web'] ?? 'https://uinssc.ac.id');
        $this->institution_email = (string) ($settings['institution_email'] ?? 'info@uinssc.ac.id');

        // Tab 2
        $this->min_days_before = (int) ($settings['min_days_before'] ?? 3);
        $savedMaxDays = (int) ($settings['max_display_days'] ?? 0);
        $this->limit_display_days_enabled = (bool) ($settings['limit_display_days_enabled'] ?? false);
        $this->max_display_days = $savedMaxDays > 0 ? $savedMaxDays : 7;
        $this->max_video_duration = (int) ($settings['max_video_duration'] ?? 30);
        $this->max_file_size = (int) ($settings['max_file_size'] ?? 50);
        $rawTemplateUrl = (string) ($settings['template_doc_url'] ?? '');
        $this->template_doc_url = (blank($rawTemplateUrl) || $rawTemplateUrl === Setting::LEGACY_SAMPLE_TEMPLATE_URL)
            ? Setting::DEFAULT_TEMPLATE_DOC_URL
            : $rawTemplateUrl;

        // Tab 3
        $this->service_active = (bool) ($settings['service_active'] ?? true);
        $this->service_closed_message = (string) ($settings['service_closed_message'] ?? 'Layanan pengajuan penayangan videotron sedang ditutup sementara untuk pemeliharaan teknis berkala.');
        $this->announcement_active = (bool) ($settings['announcement_active'] ?? false);
        $this->announcement_banner_text = (string) ($settings['announcement_banner_text'] ?? 'Perhatian: Penayangan konten pada masa libur operasional kampus dijadwalkan secara terbatas.');

        // Tab 4
        $this->helpdesk_whatsapp = (string) ($settings['helpdesk_whatsapp'] ?? ($settings['contact_whatsapp'] ?? '081234567890'));
        $this->helpdesk_email = (string) ($settings['helpdesk_email'] ?? ($settings['contact_email'] ?? 'info@uinssc.ac.id'));
        $this->helpdesk_hours = (string) ($settings['helpdesk_hours'] ?? ($settings['contact_service_hours'] ?? 'Senin – Jumat, 08.00 – 16.00 WIB'));
        $this->helpdesk_location = (string) ($settings['helpdesk_location'] ?? ($settings['contact_office_location'] ?? 'Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon'));
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function save(): void
    {
        $this->validate([
            'signatory_admin_title' => 'required|string|max:255',
            'signatory_admin_name' => 'required|string|max:255',
            'min_days_before' => 'required|integer|min:0|max:30',
            'max_display_days' => 'nullable|integer|min:1|max:365',
            'max_video_duration' => 'required|integer|min:5|max:300',
            'max_file_size' => 'required|integer|min:5|max:500',
        ]);

        $effectiveMaxDays = $this->limit_display_days_enabled ? max(1, (int) $this->max_display_days) : 0;

        $data = [
            'signatory_admin_title' => ['value' => $this->signatory_admin_title, 'type' => 'string', 'group' => 'signatory'],
            'signatory_admin_unit' => ['value' => $this->signatory_admin_unit, 'type' => 'string', 'group' => 'signatory'],
            'signatory_admin_name' => ['value' => $this->signatory_admin_name, 'type' => 'string', 'group' => 'signatory'],
            'institution_line_1' => ['value' => $this->institution_line_1, 'type' => 'string', 'group' => 'signatory'],
            'institution_line_2' => ['value' => $this->institution_line_2, 'type' => 'string', 'group' => 'signatory'],
            'institution_line_3' => ['value' => $this->institution_line_3, 'type' => 'string', 'group' => 'signatory'],
            'institution_address' => ['value' => $this->institution_address, 'type' => 'string', 'group' => 'signatory'],
            'institution_web' => ['value' => $this->institution_web, 'type' => 'string', 'group' => 'signatory'],
            'institution_email' => ['value' => $this->institution_email, 'type' => 'string', 'group' => 'signatory'],

            'min_days_before' => ['value' => $this->min_days_before, 'type' => 'integer', 'group' => 'policy'],
            'limit_display_days_enabled' => ['value' => $this->limit_display_days_enabled, 'type' => 'boolean', 'group' => 'policy'],
            'max_display_days' => ['value' => $effectiveMaxDays, 'type' => 'integer', 'group' => 'policy'],
            'max_video_duration' => ['value' => $this->max_video_duration, 'type' => 'integer', 'group' => 'policy'],
            'max_file_size' => ['value' => $this->max_file_size, 'type' => 'integer', 'group' => 'policy'],
            'template_doc_url' => [
                'value' => filled(trim((string) $this->template_doc_url))
                    ? trim((string) $this->template_doc_url)
                    : Setting::DEFAULT_TEMPLATE_DOC_URL,
                'type' => 'string',
                'group' => 'policy',
            ],

            'service_active' => ['value' => $this->service_active, 'type' => 'boolean', 'group' => 'service'],
            'service_closed_message' => ['value' => $this->service_closed_message, 'type' => 'string', 'group' => 'service'],
            'announcement_active' => ['value' => $this->announcement_active, 'type' => 'boolean', 'group' => 'service'],
            'announcement_banner_text' => ['value' => $this->announcement_banner_text, 'type' => 'string', 'group' => 'service'],

            'helpdesk_whatsapp' => ['value' => $this->helpdesk_whatsapp, 'type' => 'string', 'group' => 'helpdesk'],
            'contact_whatsapp' => ['value' => $this->helpdesk_whatsapp, 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_email' => ['value' => $this->helpdesk_email, 'type' => 'string', 'group' => 'helpdesk'],
            'contact_email' => ['value' => $this->helpdesk_email, 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_hours' => ['value' => $this->helpdesk_hours, 'type' => 'string', 'group' => 'helpdesk'],
            'contact_service_hours' => ['value' => $this->helpdesk_hours, 'type' => 'string', 'group' => 'helpdesk'],
            'helpdesk_location' => ['value' => $this->helpdesk_location, 'type' => 'string', 'group' => 'helpdesk'],
            'contact_office_location' => ['value' => $this->helpdesk_location, 'type' => 'string', 'group' => 'helpdesk'],
        ];

        foreach ($data as $key => $item) {
            Setting::set($key, $item['value'], $item['type'], $item['group']);
        }

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->body('Seluruh perubahan konfigurasi sistem telah aktif dan diperbarui.')
            ->success()
            ->send();
    }
}
