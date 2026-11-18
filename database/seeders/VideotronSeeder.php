<?php

namespace Database\Seeders;

use App\Models\Videotron;
use Illuminate\Database\Seeder;

class VideotronSeeder extends Seeder
{
    /**
     * Seed data master 6 titik perangkat videotron kampus UIN Siber Syekh Nurjati Cirebon.
     */
    public function run(): void
    {
        self::seedMasterVideotrons();
    }

    /**
     * Menyimpan atau memperbarui data master 6 perangkat videotron dan mengembalikan instance-nya.
     *
     * @return array<string, Videotron>
     */
    public static function seedMasterVideotrons(): array
    {
        $definitions = [
            'masjid' => [
                'name' => 'Videotron Masjid',
                'location' => 'Kampus Utama, depan masjid',
                'resolution' => '1920x1080',
                'orientation' => 'landscape',
                'operating_hours' => '06.00 – 22.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
            'rektorat' => [
                'name' => 'Videotron Rektorat',
                'location' => 'Kampus Utama, dekat gedung rektorat',
                'resolution' => '1920x1080',
                'orientation' => 'landscape',
                'operating_hours' => '06.00 – 22.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
            'syariah' => [
                'name' => 'Videotron Syariah',
                'location' => 'Fakultas Syariah & Hukum',
                'resolution' => '960x480',
                'orientation' => 'landscape',
                'operating_hours' => '06.00 – 22.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
            'fitk' => [
                'name' => 'Videotron FITK',
                'location' => 'Gedung Fakultas Ilmu Tarbiyah & Keguruan',
                'resolution' => '1200x720',
                'orientation' => 'landscape',
                'operating_hours' => '06.00 – 22.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
            'siber' => [
                'name' => 'Videotron Siber',
                'location' => 'Gedung Siber & SSC',
                'resolution' => '1152x768',
                'orientation' => 'landscape',
                'operating_hours' => '06.00 – 22.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
            'febi' => [
                'name' => 'Videotron FEBI',
                'location' => 'Outdoor FEBI, di atas gedung FEBI',
                'resolution' => '1680x960',
                'orientation' => 'landscape',
                'operating_hours' => '07.00 – 24.00 WIB',
                'condition' => 'normal',
                'is_active' => true,
            ],
        ];

        $seeded = [];
        foreach ($definitions as $key => $attributes) {
            $seeded[$key] = Videotron::updateOrCreate(
                ['name' => $attributes['name']],
                $attributes
            );
        }

        return $seeded;
    }
}
