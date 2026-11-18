<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixNotificationFormatSeeder extends Seeder
{
    /**
     * Perbaiki semua notifikasi admin lama agar memiliki field format=filament
     * sehingga dapat ditampilkan di bell panel Filament.
     */
    public function run(): void
    {
        $notifications = DB::table('notifications')->get();

        $fixed = 0;

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);

            if (($data['format'] ?? null) === 'filament') {
                continue;
            }

            $data['format'] = 'filament';

            // Tambahkan icon jika belum ada berdasarkan tipe notifikasi
            if (! isset($data['icon'])) {
                $data['icon'] = match ($data['type'] ?? '') {
                    'submission_created' => 'heroicon-o-document-plus',
                    'revision_submitted' => 'heroicon-o-arrow-path',
                    'submission_cancelled' => 'heroicon-o-x-circle',
                    default => 'heroicon-o-bell',
                };
            }

            if (! isset($data['iconColor'])) {
                $data['iconColor'] = match ($data['type'] ?? '') {
                    'submission_created' => 'success',
                    'revision_submitted' => 'info',
                    'submission_cancelled' => 'danger',
                    default => 'primary',
                };
            }

            // Tambahkan actions agar tombol navigasi muncul
            if (! isset($data['actions']) && isset($data['url'])) {
                $label = match ($data['type'] ?? '') {
                    'submission_created' => 'Tinjau Sekarang',
                    'revision_submitted' => 'Periksa Berkas',
                    'submission_cancelled' => 'Lihat Riwayat',
                    default => 'Lihat Detail',
                };

                $data['actions'] = [
                    [
                        'name' => 'view',
                        'label' => $label,
                        'url' => $data['url'],
                        'shouldOpenUrlInNewTab' => false,
                        'color' => 'primary',
                        'size' => 'sm',
                        'isOutlined' => false,
                        'emitEvent' => null,
                        'markAsRead' => true,
                    ],
                ];
            }

            DB::table('notifications')
                ->where('id', $notification->id)
                ->update(['data' => json_encode($data)]);

            $fixed++;
        }

        $this->command->info("Fixed {$fixed} notification records.");
    }
}
