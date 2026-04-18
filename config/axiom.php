<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Framework Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi utama starter kit. Sesuaikan nilai-nilai ini untuk
    | setiap project baru. Sebagian besar dapat dikontrol via .env.
    |
    */

    'brand' => [
        'name'      => env('APP_NAME', 'My POS App'),
        'slogan'    => env('APP_SLOGAN', 'Modern POS & Order Management'),
        'tagline'   => env('APP_TAGLINE', 'Point of Sale & Order Management System'),
        'logo_text' => env('APP_LOGO_TEXT', 'POS'),
    ],

    'commerce' => [
        'currency'           => env('APP_CURRENCY', 'IDR'),
        'currency_symbol'    => env('APP_CURRENCY_SYMBOL', 'Rp'),
        'thousand_separator' => '.',
        'decimal_separator'  => ',',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Labels
    |--------------------------------------------------------------------------
    |
    | Label yang tampil di UI. Sesuaikan sesuai konteks bisnis kamu.
    | Misalnya: 'service' bisa jadi 'Produk', 'Menu', 'Jasa', dll.
    |
    */

    'labels' => [
        // Entitas utama
        'transaction'             => 'Transaksi',
        'order'                   => 'Pesanan',
        'customer'                => 'Pelanggan',
        'service'                 => 'Layanan / Produk',

        // Matrix Pricing
        'variant'                 => 'Varian / Ukuran',
        'attribute'               => 'Tipe / Warna',
        'attribute_values' => [
            'color'               => 'Warna (Color)',
            'bw'                  => 'Hitam Putih (BW)',
        ],

        // Dimension / Per-Meter Pricing
        'dimension_width'         => 'Lebar',
        'dimension_height'        => 'Tinggi',
        'dimension_unit'          => 'm',

        // Misc
        'secondary_option'        => 'Tipe / Kategori',
        'item_note_placeholder'   => 'Catatan tambahan item...',
        'general_note_placeholder'=> 'Catatan umum untuk pesanan...',
    ],

    /*
    |--------------------------------------------------------------------------
    | Order Workflow Statuses
    |--------------------------------------------------------------------------
    |
    | Definisikan status alur kerja pesanan. Pastikan key konsisten
    | dengan yang digunakan di model Transaction.
    |
    */

    'workflow' => [
        'statuses' => [
            'pending' => ['label' => 'Pending',         'color' => 'warning'],
            'process' => ['label' => 'Diproses',        'color' => 'info'],
            'done'    => ['label' => 'Selesai',         'color' => 'success'],
            'closed'  => ['label' => 'Selesai/Diambil', 'color' => 'default'],
        ],
    ],
];
