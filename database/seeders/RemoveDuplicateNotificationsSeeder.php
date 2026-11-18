<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateNotificationsSeeder extends Seeder
{
    /**
     * Hapus notifikasi duplikat — pertahankan hanya yang paling lama per kombinasi notifiable + tipe notif.
     */
    public function run(): void
    {
        $notifications = DB::table('notifications')
            ->orderBy('created_at')
            ->get();

        $seen = [];
        $toDelete = [];

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            $type = $data['type'] ?? 'unknown';
            $key = $notification->notifiable_type.':'.$notification->notifiable_id.':'.$type;

            if (isset($seen[$key])) {
                $toDelete[] = $notification->id;
            } else {
                $seen[$key] = true;
            }
        }

        if (count($toDelete) > 0) {
            DB::table('notifications')->whereIn('id', $toDelete)->delete();
        }

        $this->command->info('Deleted '.count($toDelete).' duplicate notification(s).');
    }
}
