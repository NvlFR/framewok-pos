<?php

namespace Database\Seeders;

use App\Models\Variant;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['name' => 'Small', 'width' => '10.0', 'height' => '10.0'],
            ['name' => 'Medium', 'width' => '20.0', 'height' => '20.0'],
            ['name' => 'Large', 'width' => '30.0', 'height' => '30.0'],
            ['name' => 'Extra Large', 'width' => '40.0', 'height' => '40.0'],
        ];

        foreach ($sizes as $size) {
            Variant::firstOrCreate(['name' => $size['name']], $size);
        }
    }
}
