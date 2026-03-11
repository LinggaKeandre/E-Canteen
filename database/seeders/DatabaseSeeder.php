<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\SellerProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user with role 'user'
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'balance' => 500000,
        ]);

        // Create admin users (sellers) with their profiles and menus
        $admin1 = User::create([
            'name' => 'Kantin Utama',
            'email' => 'kantin1@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'balance' => 0,
        ]);

        SellerProfile::create([
            'user_id' => $admin1->id,
            'store_name' => 'Kantin Utama',
            'store_description' => 'Kantin utama sekolah dengan berbagai pilihan menu makanan dan minuman',
        ]);

        // Create menus for admin1
        Menu::create([
            'user_id' => $admin1->id,
            'name' => 'Nasi Goreng Spesial',
            'price' => 15000,
            'status' => 'tersedia',
            'average_rating' => 4.5,
            'rating_count' => 12,
        ]);

        Menu::create([
            'user_id' => $admin1->id,
            'name' => 'Mie Ayam',
            'price' => 12000,
            'status' => 'tersedia',
            'average_rating' => 4.2,
            'rating_count' => 18,
        ]);

        Menu::create([
            'user_id' => $admin1->id,
            'name' => 'Soto Ayam',
            'price' => 10000,
            'status' => 'tersedia',
            'average_rating' => 4.7,
            'rating_count' => 25,
        ]);

        // Create another admin user (seller)
        $admin2 = User::create([
            'name' => 'Kantin Sayuran',
            'email' => 'kantin2@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'balance' => 0,
        ]);

        SellerProfile::create([
            'user_id' => $admin2->id,
            'store_name' => 'Kantin Sayuran',
            'store_description' => 'Kantin sehat dengan menu makanan bergizi tinggi',
        ]);

        // Create menus for admin2
        Menu::create([
            'user_id' => $admin2->id,
            'name' => 'Salad Sayur Segar',
            'price' => 18000,
            'status' => 'tersedia',
            'average_rating' => 4.6,
            'rating_count' => 8,
        ]);

        Menu::create([
            'user_id' => $admin2->id,
            'name' => 'Smoothie Buah',
            'price' => 14000,
            'status' => 'tersedia',
            'average_rating' => 4.8,
            'rating_count' => 15,
        ]);

        Menu::create([
            'user_id' => $admin2->id,
            'name' => 'Telur Dadar Sayuran',
            'price' => 11000,
            'status' => 'tersedia',
            'average_rating' => 4.3,
            'rating_count' => 10,
        ]);
        
        // Run the MenuSeeder to add 15+ random products
        $this->call([
            MenuSeeder::class,
        ]);
    }
}

