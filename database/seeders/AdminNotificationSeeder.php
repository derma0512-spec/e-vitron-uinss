<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Seeder;

class AdminNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            return;
        }

        // Bersihkan notifikasi admin lama agar rapi dan tidak duplikat
        $admin->notifications()->delete();

        // 1. Pengajuan Baru Masuk (Tiket Menunggu)
        $subNew = Submission::where('status', 'menunggu')->latest('id')->first();
        if ($subNew) {
            $notif = Notification::make()
                ->title('Pengajuan Baru Masuk')
                ->icon('heroicon-o-document-plus')
                ->iconColor('success')
                ->body("Tiket {$subNew->ticket_code} ({$subNew->title}) baru saja dikirim oleh pengaju dan siap ditinjau.")
                ->actions([
                    Action::make('view')
                        ->label('Tinjau Sekarang')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.resources.submissions.index', ['selected' => $subNew->id]))
                        ->markAsRead(),
                ]);
            $admin->notifyNow($notif->toDatabase());
        }

        // 2. Perbaikan Revisi Dikirim (Tiket Revisi)
        $subRev = Submission::where('status', 'revisi')->latest('id')->first();
        if ($subRev) {
            $notif = Notification::make()
                ->title('Perbaikan Revisi Dikirim')
                ->icon('heroicon-o-arrow-path')
                ->iconColor('info')
                ->body("Pengaju telah mengirim perbaikan berkas untuk tiket {$subRev->ticket_code} ({$subRev->title}).")
                ->actions([
                    Action::make('view')
                        ->label('Periksa Berkas')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.resources.submissions.index', ['selected' => $subRev->id]))
                        ->markAsRead(),
                ]);
            $admin->notifyNow($notif->toDatabase());
        }

        // 3. Pengajuan Dibatalkan (Tiket Dibatalkan)
        $subCancel = Submission::where('status', 'dibatalkan')->latest('id')->first();
        if ($subCancel) {
            $notif = Notification::make()
                ->title('Pengajuan Dibatalkan')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->body("Tiket {$subCancel->ticket_code} ({$subCancel->title}) telah dibatalkan oleh pihak pemohon.")
                ->actions([
                    Action::make('view')
                        ->label('Lihat Riwayat')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.pages.history-and-recap', ['selected' => $subCancel->id]))
                        ->markAsRead(),
                ]);
            $admin->notifyNow($notif->toDatabase());
        }
    }
}
