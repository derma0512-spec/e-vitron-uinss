<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdminAccountService;
use DomainException;
use Illuminate\Console\Command;

class ManageAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evitron:admin
        {--action= : Aksi langsung: create, list, promote, demote, activate, deactivate, reset-password}
        {--name= : Nama lengkap admin baru}
        {--email= : Alamat email akun admin}
        {--password= : Password baru (minimal 8 karakter)}
        {--unit= : Nama unit/bagian pengelola}
        {--phone= : Nomor WhatsApp admin}
        {--super : Buat akun baru sebagai Super Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kelola akun Admin & Super Admin E-VITRON secara aman dari terminal VPS (buat, promosi, reset password, aktif/nonaktif).';

    public function handle(AdminAccountService $adminService): int
    {
        $action = $this->option('action');

        if (! is_string($action) || trim($action) === '') {
            $action = $this->choice(
                'Pilih tindakan pengelolaan akun Admin E-VITRON',
                ['list', 'create', 'promote', 'demote', 'activate', 'deactivate', 'reset-password'],
                0
            );
        }

        $action = strtolower(trim((string) $action));

        return match ($action) {
            'list' => $this->listAdmins(),
            'create' => $this->createAdmin($adminService),
            'promote' => $this->updateRole($adminService, true),
            'demote' => $this->updateRole($adminService, false),
            'activate' => $this->updateStatus($adminService, true),
            'deactivate' => $this->updateStatus($adminService, false),
            'reset-password' => $this->resetPassword($adminService),
            default => $this->invalidAction($action),
        };
    }

    private function listAdmins(): int
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->orderByRaw("CASE WHEN account_status IS NULL OR account_status = 'approved' THEN 0 ELSE 1 END")
            ->orderByDesc('is_super_admin')
            ->orderBy('id')
            ->get();

        if ($admins->isEmpty()) {
            $this->warn('Belum ada akun admin di database. Jalankan: php artisan evitron:admin --action=create --super');

            return self::SUCCESS;
        }

        $rows = $admins->map(fn (User $admin): array => [
            $admin->id,
            $admin->name,
            $admin->email,
            $admin->adminRoleLabel(),
            $admin->isApproved() ? 'Aktif' : 'Nonaktif',
            $admin->unit_or_prodi ?? '-',
        ])->all();

        $this->table(['ID', 'Nama', 'Email', 'Peran', 'Status', 'Unit'], $rows);

        return self::SUCCESS;
    }

    private function createAdmin(AdminAccountService $adminService): int
    {
        $isWizardMode = ! filled($this->option('action'));

        $name = (string) ($this->option('name') ?: $this->ask('Nama lengkap admin'));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Alamat email login'))));

        $unitOption = $this->option('unit');
        $unit = filled($unitOption)
            ? (string) $unitOption
            : ($isWizardMode ? (string) $this->ask('Unit / Bagian', 'Tim Pengelola E-VITRON') : 'Tim Pengelola E-VITRON');

        $phoneOption = $this->option('phone');
        $phone = filled($phoneOption)
            ? (string) $phoneOption
            : ($isWizardMode ? (string) $this->ask('No. WhatsApp (opsional)', '') : '');

        $password = (string) ($this->option('password') ?: $this->secret('Password awal (minimal 8 karakter)'));

        $isSuperAdmin = (bool) $this->option('super');
        if (! $isSuperAdmin && $isWizardMode) {
            $isSuperAdmin = $this->confirm('Jadikan akun ini sebagai Super Admin (Admin Utama)?', false);
        }

        if (trim($name) === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Nama lengkap wajib diisi dan alamat email harus valid.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < 8) {
            $this->error('Password minimal harus terdiri dari 8 karakter.');

            return self::FAILURE;
        }

        try {
            $created = $adminService->createAdmin([
                'name' => $name,
                'email' => $email,
                'unit_or_prodi' => $unit,
                'phone' => $phone !== '' ? $phone : null,
                'password' => $password,
            ], $isSuperAdmin);

            $this->info("Berhasil membuat {$created->adminRoleLabel()}: {$created->name} ({$created->email}).");

            return self::SUCCESS;
        } catch (DomainException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function updateRole(AdminAccountService $adminService, bool $makeSuperAdmin): int
    {
        $target = $this->resolveAdminByEmail();
        if (! $target) {
            return self::FAILURE;
        }

        try {
            $updated = $adminService->updateAdminRole($target, $makeSuperAdmin);
            $this->info("Peran {$updated->name} ({$updated->email}) diperbarui menjadi {$updated->adminRoleLabel()}.");

            return self::SUCCESS;
        } catch (DomainException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function updateStatus(AdminAccountService $adminService, bool $active): int
    {
        $target = $this->resolveAdminByEmail();
        if (! $target) {
            return self::FAILURE;
        }

        try {
            $updated = $adminService->setAdminActiveStatus($target, $active);
            $label = $active ? 'Aktif' : 'Nonaktif';
            $this->info("Status akun admin {$updated->name} ({$updated->email}) diubah menjadi {$label}.");

            return self::SUCCESS;
        } catch (DomainException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function resetPassword(AdminAccountService $adminService): int
    {
        $target = $this->resolveAdminByEmail();
        if (! $target) {
            return self::FAILURE;
        }

        $password = (string) ($this->option('password') ?: $this->secret('Password baru (minimal 8 karakter)'));

        try {
            $adminService->resetAdminPassword($target, $password);
            $this->info("Password untuk {$target->name} ({$target->email}) berhasil direset.");

            return self::SUCCESS;
        } catch (DomainException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveAdminByEmail(): ?User
    {
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Email akun admin target'))));
        $target = User::query()
            ->where('role', 'admin')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $target) {
            $this->error("Akun admin dengan email {$email} tidak ditemukan.");

            return null;
        }

        return $target;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Aksi '{$action}' tidak dikenal. Gunakan: list, create, promote, demote, activate, deactivate, atau reset-password.");

        return self::FAILURE;
    }
}
