# ECanteen Project Description

ECanteen adalah aplikasi Laravel 12 lengkap untuk pesan makanan kantin online.

## Ringkasan
- **Tipe**: Web App Kantin Digital (Laravel + MySQL)
- **Role**: User (pembeli), Seller/Admin (penjual), Superadmin
- **Fitur Inti**:
  * Dompet digital + top-up QR
  * Menu harian (Senin-Minggu) + varian/addon
  * Cart, pesanan real-time, rating, wishlist
  * Batas spending harian
  * Laporan Excel/PDF, withdrawal
  * Blokir user, approve top-up

## Struktur File Penting
```
app/Http/Controllers/
├── User/: Dashboard, Cart, Orders, Balance, Wishlist, Menu
├── Admin/: Menu CRUD, Orders Queue, Reports, Withdrawal
└── Superadmin/: Users, Sellers, TopUp, Withdrawal

app/Models/: User, Menu, Order, BalanceTransaction, SellerProfile, TopUpRequest, Wishlist

resources/views/: admin/, user/, superadmin/, layouts/
routes/web.php: Semua routes (auth, user/admin/superadmin)
database/: Migrations (28+), Seeders (MenuSeeder)
public/: Assets, index.php
composer.json: Laravel 12 + Excel + DomPDF
ecanteen.sql: DB dump siap pakai
```

## Fitur Detail
1. **Landing**: Menu harian today, top produk, seller aktif
2. **User Flow**: Browse → Cart → Checkout (potong saldo) → Konfirmasi → Rating
3. **Seller**: Kelola menu/antrian/laporan/withdrawal
4. **Superadmin**: Kelola user/seller/topup global QR
5. **API**: Status pesanan live, seller menus

## Setup XAMPP
```
cd c:/xampp/htdocs/ecanteen
composer install && npm install && npm run build
php artisan key:gen && php artisan migrate && php artisan db:seed
Start XAMPP → php artisan serve
http://localhost:8000
```
Secret Superadmin: /x7K9mP2qR5tY8vW3zA (key: ecanteen_superadmin_2024)

## Tech
- Laravel 12, PHP 8.2, MySQL
- Blade + Bootstrap + Vite
- Queue (AutoConfirmOrders)
- Exports: Excel/PDF
- Middleware: Role, BlockedUser

Siap deploy produksi. Cocok kantin sekolah/kantor Indonesia.
