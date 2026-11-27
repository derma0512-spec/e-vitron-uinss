<?php

namespace App\Services;

use App\Models\User;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminAccountService
{
    /**
     * Membuat akun admin baru (Admin Operator atau Super Admin) secara aman.
     *
     * @param  array{name: string, email: string, password: string, phone?: ?string, unit_or_prodi?: ?string}  $data
     *
     * @throws AuthorizationException
     * @throws DomainException
     */
    public function createAdmin(array $data, bool $isSuperAdmin = false, ?User $actor = null): User
    {
        $this->assertActorCanManage($actor);

        $email = strtolower(trim($data['email']));
        if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw new DomainException("Alamat email {$email} sudah terdaftar di sistem.");
        }

        return DB::transaction(function () use ($data, $email, $isSuperAdmin): User {
            $user = new User([
                'name' => trim($data['name']),
                'email' => $email,
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'phone' => filled($data['phone'] ?? null) ? trim((string) $data['phone']) : null,
                'unit_or_prodi' => filled($data['unit_or_prodi'] ?? null)
                    ? trim((string) $data['unit_or_prodi'])
                    : 'Tim Pengelola E-VITRON',
                'auth_type' => 'local',
                'account_status' => 'approved',
            ]);

            $user->email_verified_at = now();
            $user->is_super_admin = $isSuperAdmin;
            $user->save();

            return $user;
        });
    }

    /**
     * Menentukan apakah akun admin merupakan akun demo yang dikunci selama mode DEMO_QUICK_LOGIN aktif.
     */
    public static function isProtectedDemoAdmin(User $user): bool
    {
        if (! config('services.demo.quick_login')) {
            return false;
        }

        $demoAdminEmail = strtolower(trim((string) config('services.demo.admin_email')));

        return $demoAdminEmail !== ''
            && $user->isAdmin()
            && strtolower(trim((string) $user->email)) === $demoAdminEmail;
    }

    /**
     * Mempromosikan atau menurunkan hak Super Admin pada akun admin lain.
     *
     * @throws AuthorizationException
     * @throws DomainException
     */
    public function updateAdminRole(User $target, bool $makeSuperAdmin, ?User $actor = null): User
    {
        $this->assertActorCanManage($actor);

        if (! $target->isAdmin()) {
            throw new DomainException('Hanya akun dengan peran admin yang dapat diubah tingkat aksesnya.');
        }

        if ($actor !== null && self::isProtectedDemoAdmin($target)) {
            throw new DomainException('Akun Admin demo terkunci selama mode demo aktif agar tetap dapat diakses oleh penguji lain.');
        }

        if ($actor !== null && (int) $actor->id === (int) $target->id && ! $makeSuperAdmin) {
            throw new DomainException('Anda tidak dapat menurunkan peran Super Admin pada akun Anda sendiri.');
        }

        return DB::transaction(function () use ($target, $makeSuperAdmin): User {
            $locked = User::query()->lockForUpdate()->findOrFail($target->id);

            if (! $makeSuperAdmin && $locked->is_super_admin) {
                $remainingSuperAdmins = User::query()
                    ->activeAdmins()
                    ->where('is_super_admin', true)
                    ->where('id', '!=', $locked->id)
                    ->lockForUpdate()
                    ->count();

                if ($remainingSuperAdmins < 1) {
                    throw new DomainException('Minimal harus tersisa 1 Super Admin aktif di sistem.');
                }
            }

            $locked->is_super_admin = $makeSuperAdmin;
            $locked->save();

            return $locked;
        });
    }

    /**
     * Mengaktifkan atau menonaktifkan akun admin tanpa menghapus riwayat audit.
     *
     * @throws AuthorizationException
     * @throws DomainException
     */
    public function setAdminActiveStatus(User $target, bool $active, ?User $actor = null): User
    {
        $this->assertActorCanManage($actor);

        if (! $target->isAdmin()) {
            throw new DomainException('Hanya akun dengan peran admin yang dapat dikelola di sini.');
        }

        if ($actor !== null && self::isProtectedDemoAdmin($target)) {
            throw new DomainException('Akun Admin demo terkunci selama mode demo aktif agar tetap dapat diakses oleh penguji lain.');
        }

        if ($actor !== null && (int) $actor->id === (int) $target->id && ! $active) {
            throw new DomainException('Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $updated = DB::transaction(function () use ($target, $active): User {
            $locked = User::query()->lockForUpdate()->findOrFail($target->id);

            if (! $active && $locked->is_super_admin) {
                $remainingSuperAdmins = User::query()
                    ->activeAdmins()
                    ->where('is_super_admin', true)
                    ->where('id', '!=', $locked->id)
                    ->lockForUpdate()
                    ->count();

                if ($remainingSuperAdmins < 1) {
                    throw new DomainException('Tidak dapat menonaktifkan Super Admin aktif terakhir. Promosikan atau tambahkan Super Admin lain terlebih dahulu.');
                }
            }

            $locked->account_status = $active ? 'approved' : 'inactive';

            if (! $active) {
                $locked->setRememberToken(Str::random(60));
            }

            $locked->save();

            return $locked;
        });

        if (! $active) {
            $this->invalidateUserSessions($updated);
        }

        return $updated;
    }

    /**
     * Mereset password akun admin dan mencabut sesi remember-me sebelumnya.
     *
     * @throws AuthorizationException
     * @throws DomainException
     */
    public function resetAdminPassword(User $target, string $newPassword, ?User $actor = null): User
    {
        $this->assertActorCanManage($actor);

        if (! $target->isAdmin()) {
            throw new DomainException('Hanya password akun admin yang dapat direset dari menu ini.');
        }

        if ($actor !== null && self::isProtectedDemoAdmin($target)) {
            throw new DomainException('Akun Admin demo terkunci selama mode demo aktif agar tetap dapat diakses oleh penguji lain.');
        }

        if (mb_strlen($newPassword) < 8) {
            throw new DomainException('Password baru minimal harus terdiri dari 8 karakter.');
        }

        $updated = DB::transaction(function () use ($target, $newPassword): User {
            $locked = User::query()->lockForUpdate()->findOrFail($target->id);
            $locked->password = Hash::make($newPassword);
            $locked->setRememberToken(Str::random(60));
            $locked->save();

            return $locked;
        });

        $this->invalidateUserSessions($updated);

        return $updated;
    }

    /**
     * Memastikan pelaku UI benar-benar Super Admin aktif (CLI melewati $actor = null).
     *
     * @throws AuthorizationException
     */
    private function assertActorCanManage(?User $actor): void
    {
        if ($actor !== null && ! $actor->isSuperAdmin()) {
            throw new AuthorizationException('Hanya Super Admin yang berwenang mengelola akun admin.');
        }
    }

    /**
     * Menghapus sesi aktif pada driver database agar pencabutan akses berlaku segera.
     */
    private function invalidateUserSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $table = (string) config('session.table', 'sessions');
        if (Schema::hasTable($table)) {
            DB::table($table)->where('user_id', $user->id)->delete();
        }
    }
}
