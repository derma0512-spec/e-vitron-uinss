<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\User;
use App\Services\AdminAccountService;
use DomainException;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

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

    public string $activeTab = 'signatory'; // 'signatory', 'policy', 'service', 'helpdesk', 'admins'

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
    public bool $min_days_before_enabled = true;

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

    // Tab 5: Tim Admin (Khusus Super Admin)
    public bool $showCreateAdminModal = false;

    public string $new_admin_name = '';

    public string $new_admin_email = '';

    public string $new_admin_unit = 'Tim Pengelola E-VITRON';

    public string $new_admin_phone = '';

    public string $new_admin_password = '';

    public string $new_admin_password_confirmation = '';

    public bool $new_admin_is_super_admin = false;

    public bool $showResetPasswordModal = false;

    public ?int $reset_password_admin_id = null;

    public string $reset_password_admin_name = '';

    public string $reset_password_admin_email = '';

    public string $reset_admin_password = '';

    public string $reset_admin_password_confirmation = '';

    public function canManageAdmins(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }

    public function mount(): void
    {
        $allowedTabs = $this->canManageAdmins()
            ? ['signatory', 'policy', 'service', 'helpdesk', 'admins']
            : ['signatory', 'policy', 'service', 'helpdesk'];

        $requestedTab = request()->query('tab');
        if (is_string($requestedTab) && in_array($requestedTab, $allowedTabs, true)) {
            $this->activeTab = $requestedTab;
        } else {
            $this->activeTab = 'signatory';
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
        $savedMinDays = (int) ($settings['min_days_before'] ?? 3);
        $this->min_days_before_enabled = array_key_exists('min_days_before_enabled', $settings)
            ? (bool) $settings['min_days_before_enabled']
            : ($savedMinDays > 0);
        $this->min_days_before = $savedMinDays > 0
            ? $savedMinDays
            : max(1, (int) ($settings['min_days_before_last'] ?? 3));
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

    public function updatedActiveTab(string $value): void
    {
        if ($value === 'admins' && ! $this->canManageAdmins()) {
            $this->activeTab = 'signatory';
        }
    }

    public function setTab(string $tab): void
    {
        if ($tab === 'admins' && ! $this->canManageAdmins()) {
            abort(403);
        }

        if (in_array($tab, ['signatory', 'policy', 'service', 'helpdesk', 'admins'], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Daftar seluruh akun admin (aktif & nonaktif) khusus untuk Super Admin.
     *
     * @return Collection<int, User>
     */
    public function getAdminAccountsProperty(): Collection
    {
        if (! $this->canManageAdmins()) {
            return new Collection;
        }

        return User::query()
            ->where('role', 'admin')
            ->orderByRaw("CASE WHEN account_status IS NULL OR account_status = 'approved' THEN 0 ELSE 1 END")
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->get();
    }

    public function openCreateAdminModal(): void
    {
        $this->authorizeAdminManagement();
        $this->resetValidation();
        $this->new_admin_name = '';
        $this->new_admin_email = '';
        $this->new_admin_unit = 'Tim Pengelola E-VITRON';
        $this->new_admin_phone = '';
        $this->new_admin_password = '';
        $this->new_admin_password_confirmation = '';
        $this->new_admin_is_super_admin = false;
        $this->showCreateAdminModal = true;
    }

    public function closeCreateAdminModal(): void
    {
        $this->showCreateAdminModal = false;
        $this->new_admin_password = '';
        $this->new_admin_password_confirmation = '';
        $this->resetValidation();
    }

    public function createAdminAccount(): void
    {
        $this->authorizeAdminManagement();

        $validated = $this->validate([
            'new_admin_name' => 'required|string|max:100',
            'new_admin_email' => 'required|email|max:100|unique:users,email',
            'new_admin_unit' => 'nullable|string|max:150',
            'new_admin_phone' => ['nullable', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'new_admin_password' => 'required|string|min:8|confirmed',
            'new_admin_is_super_admin' => 'boolean',
        ], [
            'new_admin_name.required' => 'Nama lengkap admin wajib diisi.',
            'new_admin_email.required' => 'Alamat email login wajib diisi.',
            'new_admin_email.email' => 'Format alamat email tidak valid.',
            'new_admin_email.unique' => 'Alamat email ini sudah digunakan akun lain.',
            'new_admin_phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan tanda + / -.',
            'new_admin_password.required' => 'Password awal wajib diisi.',
            'new_admin_password.min' => 'Password awal minimal 8 karakter.',
            'new_admin_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            $created = app(AdminAccountService::class)->createAdmin([
                'name' => $validated['new_admin_name'],
                'email' => $validated['new_admin_email'],
                'unit_or_prodi' => $validated['new_admin_unit'] ?? null,
                'phone' => $validated['new_admin_phone'] ?? null,
                'password' => $validated['new_admin_password'],
            ], (bool) $validated['new_admin_is_super_admin'], auth()->user());

            $this->closeCreateAdminModal();
            unset($this->adminAccounts);

            Notification::make()
                ->title('Akun Admin Berhasil Ditambahkan')
                ->body("{$created->name} ({$created->email}) aktif sebagai {$created->adminRoleLabel()}.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Gagal Menambahkan Admin')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function openResetPasswordModal(int $adminId): void
    {
        $this->authorizeAdminManagement();

        $target = User::query()->where('role', 'admin')->findOrFail($adminId);

        $this->resetValidation();
        $this->reset_password_admin_id = $target->id;
        $this->reset_password_admin_name = $target->name;
        $this->reset_password_admin_email = $target->email;
        $this->reset_admin_password = '';
        $this->reset_admin_password_confirmation = '';
        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal(): void
    {
        $this->showResetPasswordModal = false;
        $this->reset_password_admin_id = null;
        $this->reset_password_admin_name = '';
        $this->reset_password_admin_email = '';
        $this->reset_admin_password = '';
        $this->reset_admin_password_confirmation = '';
        $this->resetValidation();
    }

    public function confirmResetAdminPassword(): void
    {
        $this->authorizeAdminManagement();

        $this->validate([
            'reset_password_admin_id' => 'required|integer',
            'reset_admin_password' => 'required|string|min:8|confirmed',
        ], [
            'reset_admin_password.required' => 'Password baru wajib diisi.',
            'reset_admin_password.min' => 'Password baru minimal 8 karakter.',
            'reset_admin_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $target = User::query()->where('role', 'admin')->findOrFail((int) $this->reset_password_admin_id);

        try {
            app(AdminAccountService::class)->resetAdminPassword(
                $target,
                $this->reset_admin_password,
                auth()->user()
            );

            $targetName = $target->name;
            $this->closeResetPasswordModal();

            Notification::make()
                ->title('Password Admin Berhasil Direset')
                ->body("Password baru untuk {$targetName} telah disimpan. Sesi login lama telah dicabut.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Gagal Mereset Password')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function toggleAdminStatus(int $adminId): void
    {
        $this->authorizeAdminManagement();

        $target = User::query()->where('role', 'admin')->findOrFail($adminId);
        $nextActive = ! $target->isApproved();

        try {
            $updated = app(AdminAccountService::class)->setAdminActiveStatus(
                $target,
                $nextActive,
                auth()->user()
            );

            unset($this->adminAccounts);

            Notification::make()
                ->title($nextActive ? 'Akun Admin Diaktifkan Kembali' : 'Akun Admin Dinonaktifkan')
                ->body($nextActive
                    ? "{$updated->name} kini dapat kembali masuk ke panel admin E-VITRON."
                    : "{$updated->name} telah dinonaktifkan. Seluruh jejak riwayat audit tetap tersimpan utuh.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Perubahan Status Ditolak')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function toggleSuperAdminRole(int $adminId): void
    {
        $this->authorizeAdminManagement();

        $target = User::query()->where('role', 'admin')->findOrFail($adminId);
        $makeSuperAdmin = ! $target->is_super_admin;

        try {
            $updated = app(AdminAccountService::class)->updateAdminRole(
                $target,
                $makeSuperAdmin,
                auth()->user()
            );

            unset($this->adminAccounts);

            Notification::make()
                ->title('Tingkat Akses Admin Diperbarui')
                ->body("{$updated->name} kini berperan sebagai {$updated->adminRoleLabel()}.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Perubahan Peran Ditolak')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function authorizeAdminManagement(): void
    {
        abort_unless($this->canManageAdmins(), 403);
    }

    public function save(): void
    {
        $this->validate([
            'signatory_admin_title' => 'required|string|max:255',
            'signatory_admin_name' => 'required|string|max:255',
            'min_days_before' => $this->min_days_before_enabled ? 'required|integer|min:1|max:30' : 'nullable|integer|min:0|max:30',
            'max_display_days' => 'nullable|integer|min:1|max:365',
            'max_video_duration' => 'required|integer|min:5|max:300',
            'max_file_size' => 'required|integer|min:5|max:500',
        ]);

        $rememberedMinDays = max(1, min(30, (int) ($this->min_days_before ?: 3)));
        $effectiveMinDays = $this->min_days_before_enabled ? $rememberedMinDays : 0;
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

            'min_days_before_enabled' => ['value' => $this->min_days_before_enabled, 'type' => 'boolean', 'group' => 'policy'],
            'min_days_before_last' => ['value' => $rememberedMinDays, 'type' => 'integer', 'group' => 'policy'],
            'min_days_before' => ['value' => $effectiveMinDays, 'type' => 'integer', 'group' => 'policy'],
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
