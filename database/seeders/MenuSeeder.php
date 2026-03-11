<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuAddon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Kantin Utama user_id (assuming it's the first admin created)
        $kantinUtama = \App\Models\User::where('role', 'admin')->first();
        
        if (!$kantinUtama) {
            $this->command->error('Admin user not found. Please run DatabaseSeeder first.');
            return;
        }

        $menus = [
            // Makanan Berat
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Nasi Goreng Kampung',
                'price' => 15000,
                'category' => 'Makanan Berat',
                'status' => 'tersedia',
                'average_rating' => 4.5,
                'rating_count' => 28,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Telur Ceplok', 'price' => 5000],
                    ['name' => 'Ayam Goreng', 'price' => 8000],
                    ['name' => 'Kerupuk', 'price' => 2000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Nasi Putih',
                'price' => 4000,
                'category' => 'Makanan Berat',
                'status' => 'tersedia',
                'average_rating' => 4.8,
                'rating_count' => 150,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4, 5],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Ayam Goreng Kalasan',
                'price' => 18000,
                'category' => 'Olahan Ayam',
                'status' => 'tersedia',
                'average_rating' => 4.7,
                'rating_count' => 45,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Nasi Putih', 'price' => 4000],
                    ['name' => 'Tahu Tempe', 'price' => 5000],
                    ['name' => 'Sambal Terasi', 'price' => 2000],
                ],
            ],

            // Bakso & Mie
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Bakso Medan',
                'price' => 15000,
                'category' => 'Bakso & Mie',
                'status' => 'tersedia',
                'average_rating' => 4.6,
                'rating_count' => 67,
                'has_variants' => true,
                'has_addons' => true,
                'is_daily' => false,
                'variants' => [
                    ['name' => 'Bakso Biasa', 'price_adjustment' => 0],
                    ['name' => 'Bakso Urat', 'price_adjustment' => 3000],
                    ['name' => 'Bakso Jumbo', 'price_adjustment' => 8000],
                ],
                'addons' => [
                    ['name' => 'Telur Puyuh', 'price' => 3000],
                    ['name' => 'Pangsit Goreng', 'price' => 4000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Mie Goreng Jawa',
                'price' => 12000,
                'category' => 'Olahan Mie',
                'status' => 'tersedia',
                'average_rating' => 4.4,
                'rating_count' => 89,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Telur Goreng', 'price' => 5000],
                    ['name' => 'Ayam Suwir', 'price' => 7000],
                    ['name' => 'Kerupuk', 'price' => 2000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Mie Rebus Spesial',
                'price' => 13000,
                'category' => 'Olahan Mie',
                'status' => 'tersedia',
                'average_rating' => 4.3,
                'rating_count' => 54,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Sosis', 'price' => 5000],
                    ['name' => 'Bakso', 'price' => 6000],
                    ['name' => 'Keju', 'price' => 4000],
                ],
            ],

            // Gorengan
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Gorengan Mix (3 pcs)',
                'price' => 5000,
                'category' => 'Gorengan',
                'status' => 'tersedia',
                'average_rating' => 4.9,
                'rating_count' => 200,
                'has_variants' => true,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4, 5],
                'variants' => [
                    ['name' => 'Tahu', 'price_adjustment' => 0],
                    ['name' => 'Tempe', 'price_adjustment' => 0],
                    ['name' => ' Pisang Goreng', 'price_adjustment' => 0],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Pisang Goreng Krispi',
                'price' => 6000,
                'category' => 'Gorengan',
                'status' => 'tersedia',
                'average_rating' => 4.7,
                'rating_count' => 120,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Keju Parut', 'price' => 3000],
                    ['name' => 'Coklat Leleh', 'price' => 3000],
                ],
            ],

            // Olahan Nasi
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Nasi Uduk Komplet',
                'price' => 18000,
                'category' => 'Olahan Nasi',
                'status' => 'tersedia',
                'average_rating' => 4.6,
                'rating_count' => 78,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Nasi Rames',
                'price' => 12000,
                'category' => 'Olahan Nasi',
                'status' => 'tersedia',
                'average_rating' => 4.5,
                'rating_count' => 95,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4, 5],
            ],

            // Olahan Telur
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Telur Dadar Mix Veggie',
                'price' => 10000,
                'category' => 'Olahan Telur',
                'status' => 'tersedia',
                'average_rating' => 4.4,
                'rating_count' => 62,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Nasi Putih', 'price' => 4000],
                    ['name' => 'Tahu Goreng', 'price' => 3000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Telur Puyuh Goreng',
                'price' => 8000,
                'category' => 'Olahan Telur',
                'status' => 'tersedia',
                'average_rating' => 4.2,
                'rating_count' => 35,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],

            // Minuman Dingin
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Es Jeruk Segar',
                'price' => 6000,
                'category' => 'Minuman Dingin',
                'status' => 'tersedia',
                'average_rating' => 4.8,
                'rating_count' => 145,
                'has_variants' => true,
                'has_addons' => false,
                'is_daily' => false,
                'variants' => [
                    ['name' => 'Regular', 'price_adjustment' => 0],
                    ['name' => 'Large', 'price_adjustment' => 3000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Es Teh Manis',
                'price' => 4000,
                'category' => 'Minuman Dingin',
                'status' => 'tersedia',
                'average_rating' => 4.9,
                'rating_count' => 250,
                'has_variants' => true,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4, 5],
                'variants' => [
                    ['name' => 'Regular', 'price_adjustment' => 0],
                    ['name' => 'Large', 'price_adjustment' => 2000],
                ],
            ],

            // Minuman Hangat
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Kopi Susu Hangat',
                'price' => 8000,
                'category' => 'Minuman Hangat',
                'status' => 'tersedia',
                'average_rating' => 4.3,
                'rating_count' => 88,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Extra Gula', 'price' => 0],
                    ['name' => 'Less Sugar', 'price' => 0],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Cappuccino Hangat',
                'price' => 10000,
                'category' => 'Minuman Hangat',
                'status' => 'tersedia',
                'average_rating' => 4.5,
                'rating_count' => 42,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],

            // Minuman Kemasan
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Aqua Botol 600ml',
                'price' => 5000,
                'category' => 'Minuman Kemasan',
                'status' => 'tersedia',
                'average_rating' => 5.0,
                'rating_count' => 180,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => true,
                'available_days' => [1, 2, 3, 4, 5],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Teh Botol Sosro',
                'price' => 6000,
                'category' => 'Minuman Kemasan',
                'status' => 'tersedia',
                'average_rating' => 4.7,
                'rating_count' => 165,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],

            // Makanan Ringan / Jajanan Tradisional
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Kue Lapis',
                'price' => 5000,
                'category' => 'Jajanan Tradisional',
                'status' => 'tersedia',
                'average_rating' => 4.4,
                'rating_count' => 55,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Kue Bugis',
                'price' => 4000,
                'category' => 'Jajanan Tradisional',
                'status' => 'tersedia',
                'average_rating' => 4.3,
                'rating_count' => 48,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],

            // Roti & Bakery
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Roti Bakar Coklat Keju',
                'price' => 10000,
                'category' => 'Roti & Bakery',
                'status' => 'tersedia',
                'average_rating' => 4.6,
                'rating_count' => 92,
                'has_variants' => true,
                'has_addons' => false,
                'is_daily' => false,
                'variants' => [
                    ['name' => 'Original', 'price_adjustment' => 0],
                    ['name' => 'Special (Double Topping)', 'price_adjustment' => 5000],
                ],
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Donat Gula',
                'price' => 5000,
                'category' => 'Roti & Bakery',
                'status' => 'tersedia',
                'average_rating' => 4.8,
                'rating_count' => 135,
                'has_variants' => false,
                'has_addons' => true,
                'is_daily' => false,
                'addons' => [
                    ['name' => 'Coklat Glaze', 'price' => 2000],
                    ['name' => 'Keju Parut', 'price' => 3000],
                ],
            ],

            // Dessert
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Pudding Coklat',
                'price' => 8000,
                'category' => 'Dessert',
                'status' => 'tersedia',
                'average_rating' => 4.5,
                'rating_count' => 70,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],
            [
                'user_id' => $kantinUtama->id,
                'name' => 'Jeli Cup',
                'price' => 5000,
                'category' => 'Dessert',
                'status' => 'tersedia',
                'average_rating' => 4.2,
                'rating_count' => 45,
                'has_variants' => false,
                'has_addons' => false,
                'is_daily' => false,
            ],
        ];

        foreach ($menus as $menuData) {
            // Extract variants and addons before creating menu
            $variants = $menuData['variants'] ?? [];
            $addons = $menuData['addons'] ?? [];
            unset($menuData['variants'], $menuData['addons']);

            // Handle available_days
            if (isset($menuData['available_days'])) {
                $menuData['available_days'] = json_encode($menuData['available_days']);
            }

            // Create menu
            $menu = Menu::create($menuData);

            // Create variants if any
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    MenuVariant::create([
                        'menu_id' => $menu->id,
                        'name' => $variant['name'],
                        'price_adjustment' => $variant['price_adjustment'],
                    ]);
                }
            }

            // Create addons if any
            if (!empty($addons)) {
                foreach ($addons as $addon) {
                    MenuAddon::create([
                        'menu_id' => $menu->id,
                        'name' => $addon['name'],
                        'price' => $addon['price'],
                    ]);
                }
            }
        }

        $this->command->info('Successfully seeded 15+ menu items for Kantin Utama!');
    }
}

