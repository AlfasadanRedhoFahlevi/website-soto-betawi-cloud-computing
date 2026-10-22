<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'nama_menu' => 'Soto Betawi Daging Sapi',
                'kategori' => 'Makanan',
                'harga' => 35000,
                'deskripsi' => 'Soto Betawi dengan daging sapi.',
                'gambar' => 'images/soto-betawi-daging.jpg',
            ],
            [
                'nama_menu' => 'Soto Betawi Ayam',
                'kategori' => 'Makanan',
                'harga' => 28000,
                'deskripsi' => 'Soto Betawi dengan daging ayam.',
                'gambar' => 'images/soto-betawi-ayam.jpg',
            ],
            [
                'nama_menu' => 'Soto Betawi Campur (Jeroan)',
                'kategori' => 'Makanan',
                'harga' => 32000,
                'deskripsi' => 'Soto Betawi dengan campuran jeroan.',
                'gambar' => 'images/soto-betawi-jeroan.jpg',
            ],
            [
                'nama_menu' => 'Nasi Putih',
                'kategori' => 'Makanan',
                'harga' => 6000,
                'deskripsi' => 'Nasi putih hangat.',
                'gambar' => 'images/nasi-putih.jpg',
            ],
            [
                'nama_menu' => 'Es Jeruk / Teh Manis',
                'kategori' => 'Minuman',
                'harga' => 5000,
                'deskripsi' => null,
                'gambar' => 'images/esjeruk-esteh.jpg',
            ],
        ];

        foreach ($menus as $menu) {
            $existingMenu = DB::table('menus')
                ->where('nama_menu', $menu['nama_menu'])
                ->first(['id', 'gambar']);

            if ($existingMenu !== null) {
                DB::table('menus')
                    ->where('id', $existingMenu->id)
                    ->update([
                        'kategori' => $menu['kategori'],
                        'harga' => $menu['harga'],
                        'deskripsi' => $menu['deskripsi'],
                        'gambar' => $existingMenu->gambar ?: $menu['gambar'],
                        'updated_at' => now(),
                    ]);

                continue;
            }

            DB::table('menus')->insert([
                ...$menu,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
