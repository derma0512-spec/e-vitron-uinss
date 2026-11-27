<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'identifier', 'phone', 'unit_or_prodi', 'avatar_url', 'auth_type', 'account_status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'account_status' => 'approved',
        'is_super_admin' => false,
    ];

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActiveAdmin(): bool
    {
        return $this->isAdmin() && $this->isApproved();
    }

    public function isSuperAdmin(): bool
    {
        return $this->isActiveAdmin() && (bool) $this->is_super_admin;
    }

    public function adminRoleLabel(): string
    {
        return $this->is_super_admin ? 'Super Admin' : 'Admin Operator';
    }

    /**
     * Scope khusus seluruh admin yang masih aktif (untuk penerimaan notifikasi & pemeriksaan kuorum).
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeActiveAdmins(Builder $query): Builder
    {
        return $query
            ->where('role', 'admin')
            ->where(function (Builder $subQuery): void {
                $subQuery->whereNull('account_status')
                    ->orWhere('account_status', 'approved');
            });
    }

    public function isApproved(): bool
    {
        return ($this->account_status ?? 'approved') === 'approved';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isActiveAdmin();
    }

    /**
     * Deteksi kategori akun pendaftar ('tendik' vs 'mitra') dari nama unit kerja atau email.
     */
    public static function detectApplicantRole(?string $unitOrProdi, ?string $email, string $fallbackRole = 'mitra'): string
    {
        $lowerEmail = strtolower(trim((string) $email));
        if (str_ends_with($lowerEmail, '@uinssc.ac.id') || str_ends_with($lowerEmail, '@syekhnurjati.ac.id')) {
            return 'tendik';
        }

        $lowerUnit = strtolower(trim((string) $unitOrProdi));
        $tendikKeywords = [
            'bagian',
            'subbag',
            'biro',
            'upt',
            'pustikom',
            'ptipd',
            'tipd',
            'lp2m',
            'lpm',
            'spi',
            'perpustakaan',
            'fakultas',
            'jurusan',
            'prodi',
            'program studi',
            'rektorat',
            'dekanat',
            'akademik',
            'kemahasiswaan',
            'keuangan',
            'kepegawaian',
            'tata usaha',
            'staf',
            'staff',
            'tendik',
            'uin',
            'siber',
            'syekh nurjati',
            'mahad',
            "ma'had",
            'pusat',
            'lembaga',
            'unit',
            'laboratorium',
            'pascasarjana',
            'humas',
            'umum',
        ];

        foreach ($tendikKeywords as $keyword) {
            if (str_contains($lowerUnit, $keyword)) {
                return 'tendik';
            }
        }

        return $fallbackRole ?: 'mitra';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }
}
