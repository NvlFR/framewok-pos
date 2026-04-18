<?php

namespace Database\Seeders;

use App\Models\Variant;
use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Service tanpa matrix (harga flat)
        Service::firstOrCreate(['name' => 'Layanan Standar'], [
            'category' => 'reguler',
            'base_price' => 15000,
            'unit' => 'pcs',
            'has_matrix_pricing' => false,
            'description' => 'Layanan reguler dengan harga flat',
        ]);

        Service::firstOrCreate(['name' => 'Layanan Ekstra'], [
            'category' => 'ekstra',
            'base_price' => 30000,
            'unit' => 'pcs',
            'has_matrix_pricing' => false,
            'description' => 'Layanan ekstra',
        ]);

        // 2. Service dengan matrix (Produk Dinamis)
        $dynamicProduct = Service::firstOrCreate(['name' => 'Produk Dinamis'], [
            'category' => 'premium',
            'base_price' => 0, // Harga akan ambil dari matrix
            'unit' => 'pcs',
            'has_matrix_pricing' => true,
            'description' => 'Produk dengan variasi harga berdasarkan ukuran dan tipe',
        ]);

        if ($dynamicProduct->wasRecentlyCreated) {
            $smallId = Variant::where('name', 'Small')->value('id');
            $mediumId = Variant::where('name', 'Medium')->value('id');

            $prices = [
                ['variant_id' => $smallId, 'modifier' => 'standar', 'price' => 50000],
                ['variant_id' => $smallId, 'modifier' => 'premium', 'price' => 75000],
                ['variant_id' => $mediumId, 'modifier' => 'standar', 'price' => 60000],
                ['variant_id' => $mediumId, 'modifier' => 'premium', 'price' => 85000],
            ];

            foreach ($prices as $price) {
                if ($price['variant_id']) {
                    ServicePrice::firstOrCreate(
                        ['service_id' => $dynamicProduct->id, 'variant_id' => $price['variant_id'], 'modifier' => $price['modifier']],
                        ['price' => $price['price']]
                    );
                }
            }
        }

        // 3. Service dengan dimensi / custom size
        $customSize = Service::firstOrCreate(['name' => 'Produk Custom Ukuran'], [
            'category' => 'custom',
            'base_price' => 25000,
            'unit' => 'meter',
            'has_matrix_pricing' => false,
            'description' => 'Produk dengan perhitungan harga per dimensi (meter persegi)',
        ]);
    }
}
