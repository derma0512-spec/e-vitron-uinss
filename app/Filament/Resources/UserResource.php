<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Notifications\AccountApprovalNotification;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Throwable;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Persetujuan Akun';

    protected static ?string $modelLabel = 'Akun Pendaftar';

    protected static ?string $pluralModelLabel = 'Persetujuan Akun';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = User::whereNotIn('role', ['admin', 'mahasiswa', 'dosen'])
                ->where('auth_type', 'local')
                ->where('account_status', 'pending')
                ->count();

            return $count > 0 ? (string) $count : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotIn('role', ['admin', 'mahasiswa', 'dosen'])
            ->where('auth_type', 'local')
            ->orderByRaw("CASE WHEN account_status = 'pending' THEN 0 WHEN account_status = 'approved' THEN 1 ELSE 2 END")
            ->latest();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Cari nama, email, atau instansi...')
            ->searchDebounce('300ms')
            ->hiddenFilterIndicators()
            ->emptyStateHeading('Belum ada pendaftaran akun')
            ->emptyStateDescription('Akun Mitra atau Tendik yang mendaftar melalui halaman registrasi akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama & Email Pendaftar')
                    ->weight('bold')
                    ->description(fn (User $record): string => (string) $record->email)
                    ->searchable(['name', 'email'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Kategori')
                    ->badge()
                    ->state(fn (User $record): string => self::resolveApplicantCategory($record))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tendik' => 'Tendik / Staf',
                        'mitra' => 'Mitra Luar',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'tendik' => 'info',
                        'mitra' => 'primary',
                        default => 'gray',
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('unit_or_prodi')
                    ->label('Instansi / Unit & WhatsApp')
                    ->default('-')
                    ->description(fn (User $record): string => 'WA: '.($record->phone ?: '-'))
                    ->searchable(['unit_or_prodi', 'phone'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->dateTime('d M Y, H:i')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('account_status')
                    ->label('Status Akun')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Menunggu Approve',
                        'approved' => 'Disetujui (Aktif)',
                        'inactive' => 'Nonaktif',
                        'rejected' => 'Ditolak',
                        default => 'Disetujui (Aktif)',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'inactive' => 'gray',
                        'rejected' => 'danger',
                        default => 'success',
                    })
                    ->alignCenter(),
            ])
            ->filters([])
            ->actionsColumnLabel('Aksi Verifikasi')
            ->actionsAlignment(Alignment::Center->value)
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->button()
                    ->size('xs')
                    ->color('success')
                    ->visible(fn (User $record): bool => in_array($record->account_status ?? 'approved', ['pending', 'rejected'], true))
                    ->modalWidth('md')
                    ->modalHeading('Setujui Pendaftaran Akun')
                    ->modalDescription(fn (User $record): string => "Setujui akun {$record->name} ({$record->email})? Sistem akan otomatis mengirim email pemberitahuan bahwa akun telah aktif dan siap digunakan untuk login.")
                    ->modalIcon('heroicon-o-check-badge')
                    ->modalIconColor('success')
                    ->modalAlignment(Alignment::Left)
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label(new HtmlString('<span wire:loading.remove wire:target="callMountedTableAction">Setujui & Kirim Email</span><span wire:loading wire:target="callMountedTableAction">Memproses...</span>'))
                        ->color('success')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan (Opsional)')
                            ->placeholder('Contoh: Akun Anda telah diverifikasi oleh PUSTIKOM. Silakan login.')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'account_status' => 'approved',
                        ]);

                        $emailSent = true;
                        try {
                            $record->notify(new AccountApprovalNotification(
                                $record,
                                'approved',
                                ! empty($data['notes']) ? trim($data['notes']) : null
                            ));
                        } catch (Throwable $e) {
                            $emailSent = false;
                            Log::warning('Pengiriman email persetujuan akun gagal.', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                                'mailer' => config('mail.default'),
                                'exception' => $e->getMessage(),
                            ]);
                        }

                        Notification::make()
                            ->title('Akun Berhasil Disetujui')
                            ->body($emailSent
                                ? "Akun {$record->name} telah aktif dan email pemberitahuan berhasil dikirim ke {$record->email}."
                                : "Akun {$record->name} telah aktif (Pengiriman email tertunda karena konfigurasi SMTP).")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->button()
                    ->size('xs')
                    ->color('danger')
                    ->visible(fn (User $record): bool => ($record->account_status ?? 'approved') === 'pending')
                    ->modalWidth('md')
                    ->modalHeading('Tolak Pendaftaran Akun')
                    ->modalDescription(fn (User $record): string => "Tolak pendaftaran akun {$record->name} ({$record->email})?")
                    ->modalIcon('heroicon-o-x-circle')
                    ->modalIconColor('danger')
                    ->modalAlignment(Alignment::Left)
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label('Tolak Pendaftaran')
                        ->color('danger')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan Penolakan (Opsional)')
                            ->placeholder('Tuliskan alasan penolakan yang akan dikirim ke email pendaftar...')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'account_status' => 'rejected',
                        ]);

                        try {
                            $record->notify(new AccountApprovalNotification(
                                $record,
                                'rejected',
                                ! empty($data['notes']) ? trim($data['notes']) : null
                            ));
                        } catch (Throwable $e) {
                            Log::warning('Pengiriman email penolakan akun gagal.', [
                                'user_id' => $record->id,
                                'email' => $record->email,
                                'mailer' => config('mail.default'),
                                'exception' => $e->getMessage(),
                            ]);
                        }

                        Notification::make()
                            ->title('Pendaftaran Akun Ditolak')
                            ->body("Pendaftaran akun {$record->name} telah ditolak.")
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('deactivate')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-m-no-symbol')
                    ->button()
                    ->size('xs')
                    ->color('gray')
                    ->visible(fn (User $record): bool => ($record->account_status ?? 'approved') === 'approved')
                    ->modalWidth('md')
                    ->modalHeading('Nonaktifkan Akun Pendaftar')
                    ->modalDescription(fn (User $record): string => "Nonaktifkan akses login untuk akun {$record->name} ({$record->email})? Akun ini tidak akan bisa masuk ke portal E-VITRON sampai diaktifkan kembali oleh Admin.")
                    ->modalIcon('heroicon-o-no-symbol')
                    ->modalIconColor('danger')
                    ->modalAlignment(Alignment::Left)
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label('Ya, Nonaktifkan')
                        ->color('danger')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->action(function (User $record): void {
                        $record->update([
                            'account_status' => 'inactive',
                        ]);

                        Notification::make()
                            ->title('Akun Dinonaktifkan')
                            ->body("Akses login untuk akun {$record->name} telah dinonaktifkan.")
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('reactivate')
                    ->label('Aktifkan')
                    ->icon('heroicon-m-check-circle')
                    ->button()
                    ->size('xs')
                    ->color('success')
                    ->visible(fn (User $record): bool => ($record->account_status ?? 'approved') === 'inactive')
                    ->modalWidth('md')
                    ->modalHeading('Aktifkan Kembali Akun')
                    ->modalDescription(fn (User $record): string => "Aktifkan kembali akses login untuk akun {$record->name} ({$record->email})? Pengguna akan langsung dapat masuk kembali ke portal E-VITRON.")
                    ->modalIcon('heroicon-o-check-badge')
                    ->modalIconColor('success')
                    ->modalAlignment(Alignment::Left)
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label('Ya, Aktifkan')
                        ->color('success')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->action(function (User $record): void {
                        $record->update([
                            'account_status' => 'approved',
                        ]);

                        Notification::make()
                            ->title('Akun Diaktifkan Kembali')
                            ->body("Akses login untuk akun {$record->name} telah aktif kembali.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }

    /**
     * Resolusi kategori akun pendaftar ('tendik' vs 'mitra') secara konsisten dari unit kerja / email.
     */
    public static function resolveApplicantCategory(User $record): string
    {
        if ($record->role === 'tendik') {
            return 'tendik';
        }

        return User::detectApplicantRole($record->unit_or_prodi, $record->email, $record->role ?: 'mitra');
    }
}

