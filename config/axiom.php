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
        'name' => env('AXIOM_BRAND_NAME', 'Axiom POS'),
        'slogan' => env('AXIOM_BRAND_SLOGAN', 'Powerful POS & Management System'),
        'address' => env('AXIOM_BRAND_ADDRESS', 'Digital Street No. 01, Metaverse'),
        'phone' => env('AXIOM_BRAND_PHONE', '0812-3456-7890'),
        'email' => env('AXIOM_BRAND_EMAIL', 'hello@axiom.pos'),
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
        // Master Labels
        'transaction' => 'Transaksi',
        'order' => 'Pesanan',
        'customer' => 'Pelanggan',
        'service' => 'Layanan',

        // Matrix & Pricing Logic
        'variant' => env('AXIOM_LABEL_VARIANT', 'Varian'),
        'attribute' => env('AXIOM_LABEL_ATTRIBUTE', 'Tipe Opsi'),
        'attribute_values' => [
            'standar' => 'Standar',
            'premium' => 'Premium',
            'special' => 'Spesial',
            'economy' => 'Ekonomi',
            'na' => 'N/A',
        ],

        // Dimension / Unit Logic
        'dimension_width' => 'Lebar',
        'dimension_height' => 'Tinggi',
        'dimension_unit' => 'm',

        // Service Categories
        'categories' => [
            'reguler' => 'Reguler',
            'ekstra' => 'Ekstra',
            'premium' => 'Premium',
            'custom' => 'Custom',
            'lainnya' => 'Lainnya',
        ],

        // Placeholders & Instructions
        'item_note_placeholder' => 'Catatan tambahan item...',
        'general_note_placeholder' => 'Catatan umum untuk pesanan...',
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
