<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Penandatangan Laporan Cetak (PDF & Lembar Cetak)
    |--------------------------------------------------------------------------
    */
    'signatory' => [
        'head_title' => env('SIPETRON_HEAD_TITLE', 'Koordinator Pengelola E-VITRON'),
        'head_unit' => env('SIPETRON_HEAD_UNIT', 'UIN Siber Syekh Nurjati Cirebon'),
        'head_name' => env('SIPETRON_HEAD_NAME', 'Riyanto, S.T., M.Kom.'),
        'head_nip' => env('SIPETRON_HEAD_NIP', ''),

        'admin_title' => env('SIPETRON_ADMIN_TITLE', 'Tim Pengelola E-VITRON'),
        'admin_unit' => env('SIPETRON_ADMIN_UNIT', 'UIN Siber Syekh Nurjati Cirebon'),
        'admin_name' => env('SIPETRON_ADMIN_NAME', 'Tim Admin E-VITRON'),
        'admin_role' => env('SIPETRON_ADMIN_ROLE', 'Admin Pengelola Sistem'),
    ],
];
