<?php

return [
    'label' => 'Pagination navigation',

    'overview' => '{1} Menampilkan 1 data|[2,*] Menampilkan :first-:last dari :total data',

    'fields' => [
        'records_per_page' => [
            'label' => 'Tampilkan',
            'options' => [
                'all' => 'Semua',
            ],
        ],
    ],

    'actions' => [
        'first' => [
            'label' => 'Pertama',
        ],
        'go_to_page' => [
            'label' => 'Ke halaman :page',
        ],
        'last' => [
            'label' => 'Terakhir',
        ],
        'next' => [
            'label' => 'Selanjutnya',
        ],
        'previous' => [
            'label' => 'Sebelumnya',
        ],
    ],
];
