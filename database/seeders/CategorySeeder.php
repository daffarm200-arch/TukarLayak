<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik & Gadget',
                'description' => 'Perangkat elektronik, aksesoris komputer, kabel, dan gadget layak pakai.',
            ],
            [
                'name' => 'Pakaian & Aksesoris',
                'description' => 'Baju, celana, jaket, tas, dan aksesoris fashion bersih dan layak guna.',
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'description' => 'Buku pelajaran, novel, komik, dan perlengkapan belajar.',
            ],
            [
                'name' => 'Perabotan Rumah',
                'description' => 'Perabot rumah tangga, dekorasi kamar, dan perkakas.',
            ],
            [
                'name' => 'Hobi & Mainan',
                'description' => 'Peralatan olahraga, alat musik, board game, dan mainan.',
            ],
            [
                'name' => 'Lain-lain',
                'description' => 'Barang bermanfaat lainnya yang belum masuk kategori di atas.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
