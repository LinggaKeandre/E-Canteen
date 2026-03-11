-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Mar 2026 pada 16.59
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecanteen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `balance_transactions`
--

CREATE TABLE `balance_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('debit','credit') NOT NULL,
  `amount` int(11) NOT NULL,
  `balance_before` int(11) NOT NULL,
  `balance_after` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `balance_transactions`
--

INSERT INTO `balance_transactions` (`id`, `user_id`, `order_id`, `type`, `amount`, `balance_before`, `balance_after`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, 'credit', 100000, 0, 100000, 'Top up saldo', '2026-03-05 06:14:23', '2026-03-05 06:14:23'),
(2, 4, NULL, 'credit', 100000, 100000, 200000, 'Top up saldo', '2026-03-05 06:14:42', '2026-03-05 06:14:42'),
(3, 4, NULL, 'credit', 100000, 200000, 300000, 'Top up saldo', '2026-03-05 06:14:49', '2026-03-05 06:14:49'),
(4, 4, NULL, 'credit', 100000, 300000, 400000, 'Top up saldo', '2026-03-05 06:14:55', '2026-03-05 06:14:55'),
(5, 4, NULL, 'credit', 100000, 400000, 500000, 'Top up saldo', '2026-03-05 06:15:02', '2026-03-05 06:15:02'),
(6, 4, 1, 'debit', 14000, 500000, 486000, 'Pembayaran pesanan #1 (Dana di Escrow)', '2026-03-05 06:15:41', '2026-03-05 06:15:41'),
(7, 2, 1, 'credit', 14000, 0, 14000, 'Penerimaan dana dari pesanan #1 (Escrow Release - Buyer Confirmed)', '2026-03-05 06:58:21', '2026-03-05 06:58:21'),
(8, 5, NULL, 'credit', 100000, 0, 100000, 'Top up saldo', '2026-03-05 07:24:18', '2026-03-05 07:24:18'),
(9, 5, 2, 'debit', 10000, 100000, 90000, 'Pembayaran pesanan #2 (Dana di Escrow)', '2026-03-05 07:24:36', '2026-03-05 07:24:36'),
(10, 4, 3, 'debit', 15000, 486000, 471000, 'Pembayaran pesanan #3 (Dana di Escrow)', '2026-03-05 07:24:44', '2026-03-05 07:24:44'),
(11, 2, 2, 'credit', 10000, 14000, 24000, 'Penerimaan dana dari pesanan #2 (Escrow Release - Buyer Confirmed)', '2026-03-05 07:27:03', '2026-03-05 07:27:03'),
(12, 2, 3, 'credit', 15000, 24000, 39000, 'Penerimaan dana dari pesanan #3 (Escrow Release - Buyer Confirmed)', '2026-03-05 07:27:22', '2026-03-05 07:27:22'),
(13, 4, 4, 'debit', 37000, 471000, 434000, 'Pembayaran pesanan #4 (Dana di Escrow)', '2026-03-07 23:41:11', '2026-03-07 23:41:11'),
(14, 4, 5, 'debit', 29000, 434000, 405000, 'Pembayaran pesanan #5 (Dana di Escrow)', '2026-03-07 23:42:12', '2026-03-07 23:42:12'),
(15, 2, 4, 'credit', 37000, 39000, 76000, 'Penerimaan dana dari pesanan #4 (Escrow Release - Buyer Confirmed)', '2026-03-07 23:44:09', '2026-03-07 23:44:09'),
(16, 2, 5, 'credit', 29000, 76000, 105000, 'Penerimaan dana dari pesanan #5 (Escrow Release - Buyer Confirmed)', '2026-03-07 23:44:12', '2026-03-07 23:44:12'),
(17, 4, NULL, 'credit', 10000, 405000, 415000, 'Top up saldo', '2026-03-07 23:53:43', '2026-03-07 23:53:43'),
(18, 4, 6, 'debit', 23000, 415000, 392000, 'Pembayaran pesanan dari 2 toko (Dana di Escrow)', '2026-03-08 00:20:50', '2026-03-08 00:20:50'),
(19, 4, 6, 'credit', 12000, 392000, 404000, 'Pembatalan pesanan #6', '2026-03-08 00:21:26', '2026-03-08 00:21:26'),
(20, 3, 7, 'credit', 11000, 0, 11000, 'Penerimaan dana dari pesanan #7 (Escrow Release - Buyer Confirmed)', '2026-03-08 00:21:57', '2026-03-08 00:21:57'),
(21, 2, NULL, 'debit', 10000, 105000, 95000, 'Penarikan saldo ke  ()', '2026-03-08 06:55:17', '2026-03-08 06:55:17'),
(22, 4, 8, 'debit', 10000, 404000, 394000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 07:00:05', '2026-03-08 07:00:05'),
(23, 4, 9, 'debit', 12000, 394000, 382000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 07:01:06', '2026-03-08 07:01:06'),
(24, 4, 8, 'credit', 10000, 382000, 392000, 'Pembatalan pesanan #8', '2026-03-08 07:01:39', '2026-03-08 07:01:39'),
(25, 4, NULL, 'credit', 100000, 392000, 492000, 'Top up saldo', '2026-03-08 07:49:54', '2026-03-08 07:49:54'),
(26, 4, 10, 'debit', 10000, 492000, 482000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 07:50:23', '2026-03-08 07:50:23'),
(27, 4, 11, 'debit', 15000, 482000, 467000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 07:54:37', '2026-03-08 07:54:37'),
(28, 4, NULL, 'credit', 100000, 467000, 567000, 'Top-up saldo via Transfer BNI', '2026-03-08 08:27:45', '2026-03-08 08:27:45'),
(29, 2, 11, 'credit', 15000, 95000, 110000, 'Penerimaan dana dari pesanan #11 (Escrow Release - Buyer Confirmed)', '2026-03-08 14:49:49', '2026-03-08 14:49:49'),
(30, 4, 12, 'debit', 10000, 567000, 557000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 15:05:02', '2026-03-08 15:05:02'),
(31, 2, 12, 'credit', 10000, 110000, 120000, 'Penerimaan dana dari pesanan #12 (Escrow Release - Buyer Confirmed)', '2026-03-08 15:05:56', '2026-03-08 15:05:56'),
(32, 4, 13, 'debit', 12000, 557000, 545000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 15:15:05', '2026-03-08 15:15:05'),
(33, 2, 13, 'credit', 12000, 120000, 132000, 'Penerimaan dana dari pesanan #13 (Escrow Release - Buyer Confirmed)', '2026-03-08 15:21:59', '2026-03-08 15:21:59'),
(34, 4, 14, 'debit', 12000, 545000, 533000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 16:46:14', '2026-03-08 16:46:14'),
(35, 2, 14, 'credit', 12000, 132000, 144000, 'Penerimaan dana dari pesanan #14 (Escrow Release - Buyer Confirmed)', '2026-03-08 17:07:56', '2026-03-08 17:07:56'),
(36, 4, 15, 'debit', 15000, 533000, 518000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 17:08:31', '2026-03-08 17:08:31'),
(37, 2, 15, 'credit', 15000, 144000, 159000, 'Penerimaan dana dari pesanan #15 (Escrow Release - Buyer Confirmed)', '2026-03-08 17:09:03', '2026-03-08 17:09:03'),
(38, 4, 16, 'debit', 12000, 518000, 506000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 17:16:04', '2026-03-08 17:16:04'),
(39, 2, 16, 'credit', 12000, 159000, 171000, 'Penerimaan dana dari pesanan #16 (Escrow Release - Buyer Confirmed)', '2026-03-08 17:16:42', '2026-03-08 17:16:42'),
(40, 4, 17, 'debit', 12000, 506000, 494000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-08 17:29:46', '2026-03-08 17:29:46'),
(41, 2, 17, 'credit', 12000, 171000, 183000, 'Penerimaan dana dari pesanan #17 (Escrow Release - Buyer Confirmed)', '2026-03-08 17:30:11', '2026-03-08 17:30:11'),
(42, 2, NULL, 'debit', 10000, 183000, 173000, 'Penarikan saldo ke  ()', '2026-03-08 17:40:35', '2026-03-08 17:40:35'),
(43, 4, 18, 'debit', 15000, 494000, 479000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-09 17:58:34', '2026-03-09 17:58:34'),
(44, 2, 18, 'credit', 15000, 173000, 188000, 'Penerimaan dana dari pesanan #18 (Escrow Release - Buyer Confirmed)', '2026-03-09 17:59:20', '2026-03-09 17:59:20'),
(45, 4, 19, 'debit', 30000, 479000, 449000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-09 18:07:20', '2026-03-09 18:07:20'),
(46, 4, 26, 'debit', 46000, 449000, 403000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-09 22:16:30', '2026-03-09 22:16:30'),
(47, 2, 26, 'credit', 46000, 188000, 234000, 'Penerimaan dana dari pesanan #26 (Escrow Release - Buyer Confirmed)', '2026-03-09 22:17:05', '2026-03-09 22:17:05'),
(48, 4, 27, 'debit', 34000, 403000, 369000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-09 22:18:26', '2026-03-09 22:18:26'),
(49, 2, 27, 'credit', 34000, 234000, 268000, 'Penerimaan dana dari pesanan #27 (Escrow Release - Buyer Confirmed)', '2026-03-09 22:47:49', '2026-03-09 22:47:49'),
(50, 4, 28, 'debit', 108000, 369000, 261000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-09 23:03:12', '2026-03-09 23:03:12'),
(51, 4, 29, 'debit', 12000, 261000, 249000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-10 04:43:28', '2026-03-10 04:43:28'),
(52, 4, 30, 'debit', 15000, 249000, 234000, 'Pembayaran pesanan dari 1 toko (Dana di Escrow)', '2026-03-10 08:58:02', '2026-03-10 08:58:02'),
(53, 2, 30, 'credit', 15000, 268000, 283000, 'Penerimaan dana dari pesanan #30 (Escrow Release - Buyer Confirmed)', '2026-03-10 08:58:27', '2026-03-10 08:58:27'),
(54, 2, 29, 'credit', 12000, 283000, 295000, 'Penerimaan dana dari pesanan #29 (Escrow Release - Buyer Confirmed)', '2026-03-10 08:58:53', '2026-03-10 08:58:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','habis') NOT NULL DEFAULT 'tersedia',
  `has_variants` tinyint(1) NOT NULL DEFAULT 0,
  `has_addons` tinyint(1) NOT NULL DEFAULT 0,
  `is_daily` tinyint(1) NOT NULL DEFAULT 0,
  `available_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`available_days`)),
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `menus`
--

INSERT INTO `menus` (`id`, `name`, `price`, `category`, `photo_path`, `status`, `has_variants`, `has_addons`, `is_daily`, `available_days`, `average_rating`, `rating_count`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Nasi Goreng Spesial', 15000, 'Makanan Berat', 'menus/vyaqvSFTjwkBzigJFGABAw0a6x9VOo0TViBnWCd2.jpg', 'tersedia', 1, 1, 1, '[\"2\",\"4\",\"5\"]', 5.00, 4, '2026-03-05 05:44:58', '2026-03-10 08:57:32', 2),
(2, 'Mie Ayam', 12000, 'Bakso & Mie', NULL, 'tersedia', 0, 0, 0, NULL, 5.00, 1, '2026-03-05 05:44:58', '2026-03-09 22:17:12', 2),
(3, 'Soto Ayam', 10000, 'Makanan Berat', NULL, 'tersedia', 0, 0, 0, NULL, 4.00, 1, '2026-03-05 05:44:58', '2026-03-09 17:57:58', 2),
(4, 'Salad Sayur Segar', 18000, NULL, NULL, 'tersedia', 0, 0, 0, NULL, 4.60, 8, '2026-03-05 05:44:58', '2026-03-05 05:44:58', 3),
(5, 'Smoothie Buah', 14000, NULL, NULL, 'tersedia', 0, 0, 0, NULL, 3.00, 2, '2026-03-05 05:44:58', '2026-03-07 23:45:59', 3),
(6, 'Telur Dadar Sayuran', 11000, NULL, NULL, 'tersedia', 0, 0, 0, NULL, 4.30, 10, '2026-03-05 05:44:58', '2026-03-05 05:44:58', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu_addons`
--

CREATE TABLE `menu_addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `menu_addons`
--

INSERT INTO `menu_addons` (`id`, `menu_id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(6, 1, 'Keju', 2000, '2026-03-10 08:57:32', '2026-03-10 08:57:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu_variants`
--

CREATE TABLE `menu_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_adjustment` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `menu_variants`
--

INSERT INTO `menu_variants` (`id`, `menu_id`, `name`, `price_adjustment`, `created_at`, `updated_at`) VALUES
(11, 1, 'pedas', 0, '2026-03-10 08:57:32', '2026-03-10 08:57:32'),
(12, 1, 'ekstra pedas', 0, '2026-03-10 08:57:32', '2026-03-10 08:57:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_update_users_table', 1),
(5, '2024_01_01_000002_create_menus_table', 1),
(6, '2024_01_01_000003_create_orders_table', 1),
(7, '2024_01_01_000004_create_order_items_table', 1),
(8, '2024_01_01_000005_create_balance_transactions_table', 1),
(9, '2024_01_01_000006_create_seller_profiles_table', 1),
(10, '2024_01_01_000007_add_escrow_fields_to_orders_table', 1),
(11, '2024_01_01_000008_add_auto_confirm_to_orders_table', 1),
(12, '2024_01_01_000009_add_cancel_fields_to_orders_table', 1),
(13, '2024_01_01_000010_create_ratings_table', 1),
(14, '2024_01_01_000011_add_rating_fields_to_menus_table', 1),
(15, '2024_01_01_000012_add_pending_status_to_orders_table', 1),
(16, '2024_01_01_000013_add_user_id_to_menus_table', 1),
(17, '2024_01_01_000014_add_store_banner_to_seller_profiles_table', 2),
(18, '2024_01_01_000015_add_superadmin_role_and_new_tables', 3),
(19, '2024_01_01_000016_add_seller_id_to_orders_table', 4),
(20, '2024_01_01_000017_add_category_to_menus_table', 5),
(21, '2024_01_01_000018_create_menu_variants_table', 6),
(22, '2024_01_01_000019_create_menu_addons_table', 6),
(23, '2024_01_01_000020_add_variants_addons_columns', 6),
(24, '2024_01_01_000024_add_daily_menu_columns_to_menus_table', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `pickup_slot` enum('istirahat_1','istirahat_2') NOT NULL,
  `classroom` varchar(255) NOT NULL,
  `status` enum('pending','preparing','ready') NOT NULL DEFAULT 'pending',
  `ready_at` timestamp NULL DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `is_confirmed_by_user` tinyint(1) NOT NULL DEFAULT 0,
  `is_confirmed_by_seller` tinyint(1) NOT NULL DEFAULT 0,
  `is_auto_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `cancel_request` enum('none','pending','accepted','rejected') NOT NULL DEFAULT 'none',
  `cancel_requested_at` timestamp NULL DEFAULT NULL,
  `cancel_responded_at` timestamp NULL DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `pickup_slot`, `classroom`, `status`, `ready_at`, `total_amount`, `is_paid`, `is_confirmed_by_user`, `is_confirmed_by_seller`, `is_auto_confirmed`, `cancel_request`, `cancel_requested_at`, `cancel_responded_at`, `is_completed`, `completed_at`, `created_at`, `updated_at`, `seller_id`) VALUES
(1, 4, 'istirahat_1', 'tes', 'ready', '2026-03-05 06:57:56', 14000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-05 06:58:21', '2026-03-05 06:15:41', '2026-03-05 06:58:21', NULL),
(2, 5, 'istirahat_1', 'tes1', 'ready', '2026-03-05 07:26:54', 10000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-05 07:27:03', '2026-03-05 07:24:36', '2026-03-05 07:27:03', NULL),
(3, 4, 'istirahat_2', 'tes2', 'ready', '2026-03-05 07:27:15', 15000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-05 07:27:22', '2026-03-05 07:24:44', '2026-03-05 07:27:22', NULL),
(4, 4, 'istirahat_2', 'lingga', 'ready', '2026-03-07 23:41:26', 37000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-07 23:44:09', '2026-03-07 23:41:11', '2026-03-07 23:44:09', NULL),
(5, 4, 'istirahat_2', 'tes', 'ready', '2026-03-07 23:43:58', 29000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-07 23:44:12', '2026-03-07 23:42:12', '2026-03-07 23:44:12', NULL),
(6, 4, 'istirahat_1', 'tes2 cuy', 'preparing', NULL, 12000, 1, 0, 0, 0, 'accepted', '2026-03-08 00:21:26', '2026-03-08 00:21:26', 1, '2026-03-08 00:21:26', '2026-03-08 00:20:50', '2026-03-08 00:21:26', 2),
(7, 4, 'istirahat_1', 'tes2 cuy', 'ready', '2026-03-08 00:21:51', 11000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 00:21:57', '2026-03-08 00:20:50', '2026-03-08 00:21:57', 3),
(8, 4, 'istirahat_1', 'mantap', 'preparing', NULL, 10000, 1, 0, 0, 0, 'accepted', '2026-03-08 07:01:39', '2026-03-08 07:01:39', 1, '2026-03-08 07:01:39', '2026-03-08 07:00:05', '2026-03-08 07:01:39', 2),
(9, 4, 'istirahat_2', 'mantap2', 'preparing', NULL, 12000, 1, 0, 0, 0, 'none', NULL, NULL, 0, NULL, '2026-03-08 07:01:06', '2026-03-08 07:01:15', 2),
(10, 4, 'istirahat_2', 'mantap', 'preparing', NULL, 10000, 1, 0, 0, 0, 'none', NULL, NULL, 0, NULL, '2026-03-08 07:50:23', '2026-03-08 07:53:52', 2),
(11, 4, 'istirahat_1', 'h', 'ready', '2026-03-08 14:49:26', 15000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 14:49:49', '2026-03-08 07:54:37', '2026-03-08 14:49:49', 2),
(12, 4, 'istirahat_1', 'inggris', 'ready', '2026-03-08 15:05:41', 10000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 15:05:56', '2026-03-08 15:05:02', '2026-03-08 15:05:56', 2),
(13, 4, 'istirahat_1', 'tes', 'ready', '2026-03-08 15:18:55', 12000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 15:21:59', '2026-03-08 15:15:05', '2026-03-08 15:21:59', 2),
(14, 4, 'istirahat_1', 'tes3', 'ready', '2026-03-08 16:46:38', 12000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 17:07:56', '2026-03-08 16:46:14', '2026-03-08 17:07:56', 2),
(15, 4, 'istirahat_1', 'hokben', 'ready', '2026-03-08 17:08:54', 15000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 17:09:03', '2026-03-08 17:08:31', '2026-03-08 17:09:03', 2),
(16, 4, 'istirahat_1', 'budi', 'ready', '2026-03-08 17:16:17', 12000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 17:16:42', '2026-03-08 17:16:04', '2026-03-08 17:16:42', 2),
(17, 4, 'istirahat_1', 'f', 'ready', '2026-03-08 17:29:56', 12000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-08 17:30:11', '2026-03-08 17:29:46', '2026-03-08 17:30:11', 2),
(18, 4, 'istirahat_1', 'tes1', 'ready', '2026-03-09 17:59:06', 15000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-09 17:59:20', '2026-03-09 17:58:34', '2026-03-09 17:59:20', 2),
(19, 4, 'istirahat_2', 'yw4', 'preparing', NULL, 30000, 1, 0, 0, 0, 'none', NULL, NULL, 0, NULL, '2026-03-09 18:07:20', '2026-03-09 18:07:27', 2),
(26, 4, 'istirahat_1', 'pajak', 'ready', '2026-03-09 22:16:54', 46000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-09 22:17:05', '2026-03-09 22:16:30', '2026-03-09 22:17:05', 2),
(27, 4, 'istirahat_1', '10c', 'ready', '2026-03-09 22:47:34', 34000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-09 22:47:49', '2026-03-09 22:18:26', '2026-03-09 22:47:49', 2),
(28, 4, 'istirahat_1', '10c', 'preparing', NULL, 108000, 1, 0, 0, 0, 'none', NULL, NULL, 0, NULL, '2026-03-09 23:03:12', '2026-03-09 23:03:56', 2),
(29, 4, 'istirahat_1', 'tes', 'ready', '2026-03-10 08:58:44', 12000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-10 08:58:53', '2026-03-10 04:43:28', '2026-03-10 08:58:53', 2),
(30, 4, 'istirahat_1', 'g', 'ready', '2026-03-10 08:58:15', 15000, 1, 1, 1, 0, 'none', NULL, NULL, 1, '2026-03-10 08:58:27', '2026-03-10 08:58:02', '2026-03-10 08:58:27', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `addons_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addons_json`)),
  `notes` text DEFAULT NULL,
  `subtotal` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `qty`, `price`, `variant_name`, `addons_json`, `notes`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, 14000, NULL, NULL, NULL, 14000, '2026-03-05 06:15:41', '2026-03-05 06:15:41'),
(2, 2, 3, 1, 10000, NULL, NULL, NULL, 10000, '2026-03-05 07:24:36', '2026-03-05 07:24:36'),
(3, 3, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-05 07:24:44', '2026-03-05 07:24:44'),
(4, 4, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-07 23:41:11', '2026-03-07 23:41:11'),
(5, 4, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-07 23:41:11', '2026-03-07 23:41:11'),
(6, 4, 3, 1, 10000, NULL, NULL, NULL, 10000, '2026-03-07 23:41:11', '2026-03-07 23:41:11'),
(7, 5, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-07 23:42:12', '2026-03-07 23:42:12'),
(8, 5, 5, 1, 14000, NULL, NULL, NULL, 14000, '2026-03-07 23:42:12', '2026-03-07 23:42:12'),
(9, 6, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 00:20:50', '2026-03-08 00:20:50'),
(10, 7, 6, 1, 11000, NULL, NULL, NULL, 11000, '2026-03-08 00:20:50', '2026-03-08 00:20:50'),
(11, 8, 3, 1, 10000, NULL, NULL, NULL, 10000, '2026-03-08 07:00:05', '2026-03-08 07:00:05'),
(12, 9, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 07:01:06', '2026-03-08 07:01:06'),
(13, 10, 3, 1, 10000, NULL, NULL, NULL, 10000, '2026-03-08 07:50:23', '2026-03-08 07:50:23'),
(14, 11, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-08 07:54:37', '2026-03-08 07:54:37'),
(15, 12, 3, 1, 10000, NULL, NULL, NULL, 10000, '2026-03-08 15:05:02', '2026-03-08 15:05:02'),
(16, 13, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 15:15:05', '2026-03-08 15:15:05'),
(17, 14, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 16:46:14', '2026-03-08 16:46:14'),
(18, 15, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-08 17:08:31', '2026-03-08 17:08:31'),
(19, 16, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 17:16:04', '2026-03-08 17:16:04'),
(20, 17, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-08 17:29:46', '2026-03-08 17:29:46'),
(21, 18, 1, 1, 15000, NULL, NULL, NULL, 15000, '2026-03-09 17:58:34', '2026-03-09 17:58:34'),
(22, 19, 1, 2, 15000, NULL, NULL, NULL, 30000, '2026-03-09 18:07:20', '2026-03-09 18:07:20'),
(23, 26, 1, 2, 17000, 'pedas', '[{\"id\":2,\"name\":\"Keju\",\"price\":2000}]', 'jawa', 34000, '2026-03-09 22:16:30', '2026-03-09 22:16:30'),
(24, 26, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-09 22:16:30', '2026-03-09 22:16:30'),
(25, 27, 1, 1, 17000, 'pedas', '[{\"id\":2,\"name\":\"Keju\",\"price\":2000}]', 'mantap', 17000, '2026-03-09 22:18:26', '2026-03-09 22:18:26'),
(26, 27, 1, 1, 17000, 'ekstra pedas', '[{\"id\":2,\"name\":\"Keju\",\"price\":2000}]', 'coding', 17000, '2026-03-09 22:18:26', '2026-03-09 22:18:26'),
(27, 28, 2, 9, 12000, NULL, NULL, NULL, 108000, '2026-03-09 23:03:12', '2026-03-09 23:03:12'),
(28, 29, 2, 1, 12000, NULL, NULL, NULL, 12000, '2026-03-10 04:43:28', '2026-03-10 04:43:28'),
(29, 30, 1, 1, 15000, 'pedas', '[{\"id\":4,\"name\":\"Keju\",\"price\":2000}]', NULL, 15000, '2026-03-10 08:58:02', '2026-03-10 08:58:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ratings`
--

INSERT INTO `ratings` (`id`, `order_id`, `menu_id`, `user_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 4, 5, 'tes', '2026-03-05 07:15:13', '2026-03-05 07:15:13'),
(2, 3, 1, 4, 5, NULL, '2026-03-05 07:28:23', '2026-03-05 07:28:23'),
(3, 2, 3, 5, 4, NULL, '2026-03-05 07:28:33', '2026-03-05 07:28:33'),
(4, 5, 1, 4, 5, NULL, '2026-03-07 23:45:59', '2026-03-07 23:45:59'),
(5, 5, 5, 4, 1, NULL, '2026-03-07 23:45:59', '2026-03-07 23:45:59'),
(6, 26, 1, 4, 5, NULL, '2026-03-09 22:17:12', '2026-03-09 22:17:12'),
(7, 26, 2, 4, 5, NULL, '2026-03-09 22:17:12', '2026-03-09 22:17:12'),
(8, 27, 1, 4, 5, NULL, '2026-03-09 22:47:58', '2026-03-09 22:47:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `order_id`, `reason`, `description`, `status`, `reviewed_by`, `reviewed_at`, `resolution_notes`, `created_at`, `updated_at`) VALUES
(1, 4, 18, 'missing_item', 'tes1', 'pending', NULL, NULL, NULL, '2026-03-09 17:59:32', '2026-03-09 17:59:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `seller_profiles`
--

CREATE TABLE `seller_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_logo` varchar(255) DEFAULT NULL,
  `store_banner` varchar(255) DEFAULT NULL,
  `store_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `seller_profiles`
--

INSERT INTO `seller_profiles` (`id`, `user_id`, `store_name`, `store_logo`, `store_banner`, `store_description`, `created_at`, `updated_at`) VALUES
(1, 2, 'Kantin Utama', 'seller_logos/KK6vBFPRWmSbUgbzqArP6I7X71CVF2Czk9PrAZGD.jpg', 'seller_banners/NojLt5NWtUuEjOcanJQxJaiQEgchrYXlivFhwX4x.jpg', 'Kantin utama sekolah dengan berbagai pilihan menu makanan dan minuman', '2026-03-05 05:44:58', '2026-03-05 06:04:30'),
(2, 3, 'Kantin Sayuran', NULL, NULL, 'Kantin sehat dengan menu makanan bergizi tinggi', '2026-03-05 05:44:58', '2026-03-05 05:44:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3ACw1PTmJsTjWTSVGhr0iqc7uZ9NX4gdbc0qyWcF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMHRwVHlhdDFjMjdHbWk2NnlCc0pHeWE5aURHcXNnWmVuOUhoOXlXNCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FwaS9hZG1pbi9ub3RpZmljYXRpb25zIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773156095),
('dH4ejBfVnC5ngelWWJzNR6cBGWNWLTU8BQ5N84vs', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiald6TWhwNTNBVWhFeExJRXdNMVljVTlJeUl4QTVBdTRGZFRJcW9jViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FwaS9hZG1pbi9ub3RpZmljYXRpb25zIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6OTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvb3JkZXJzL3N0YXR1cz9pZHM9MzAlMkMyOSUyQzI4JTJDMjclMkMyNiUyQzE5JTJDMTglMkMxNyUyQzE2JTJDMTUiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1773158398),
('DV1lsU4NCCkYJNuBQfzJTc58uiCtLvnxcSgze2Vs', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWmxIdHNEU25IdFpIdW9RYlM0Q0pUcWNlNXJlblJwaEFKSUhOdnJMUSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FwaS9hZG1pbi9ub3RpZmljYXRpb25zIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvYWRtaW4vbm90aWZpY2F0aW9ucyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1773158396),
('ecScRDCDnZBiqH38pNhCeBGdgKjfoSCHI8VqzfnS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOTRkQ1R1T1lVTzE0QjMwS3JWZzBvR0JMdEc1ZGc1UVRMWGFHNk9QViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FwaS9hZG1pbi9ub3RpZmljYXRpb25zIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773158369);

-- --------------------------------------------------------

--
-- Struktur dari tabel `top_up_requests`
--

CREATE TABLE `top_up_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `top_up_requests`
--

INSERT INTO `top_up_requests` (`id`, `user_id`, `amount`, `payment_method`, `payment_proof`, `status`, `processed_by`, `processed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 100000, 'Transfer BNI', 'payment_proofs/asAvj4416pLbm5A8F7Za5dRQPgMGpWptD35qIYGQ.webp', 'approved', 6, '2026-03-08 08:27:45', NULL, '2026-03-08 08:27:31', '2026-03-08 08:27:45'),
(2, 4, 100000, 'Transfer BRI', 'payment_proofs/ZXaczl3mh7ODtrEFqvhOfWyOhh7DtQhk5VF9pRty.png', 'rejected', 6, '2026-03-08 08:33:25', 'jangan', '2026-03-08 08:28:24', '2026-03-08 08:33:25'),
(3, 4, 25000, 'Transfer BNI', 'payment_proofs/3I1ZZPCtagVg1MxQ9vj1HAgj17mOoOmQ8hTcAXNn.webp', 'rejected', 6, '2026-03-08 08:50:40', 'ogah', '2026-03-08 08:50:17', '2026-03-08 08:50:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','user') NOT NULL DEFAULT 'user',
  `balance` int(11) NOT NULL DEFAULT 0,
  `daily_spending_limit` decimal(15,0) DEFAULT NULL,
  `daily_spending_limit_enabled` tinyint(1) DEFAULT 0,
  `daily_spending_limit_resets_at` timestamp NULL DEFAULT NULL,
  `daily_limit_enabled_at` timestamp NULL DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `balance`, `daily_spending_limit`, `daily_spending_limit_enabled`, `daily_spending_limit_resets_at`, `daily_limit_enabled_at`, `phone_number`, `username`, `profile_photo`, `description`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', 'user', 500000, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 05:44:57', '$2y$12$Pj5ueo9laWh1JjkiSkyuseNFfgsE3L2E6DTFOdcwAoKRUXqjvlSwq', 'KXSUTrIOmK', '2026-03-05 05:44:58', '2026-03-05 05:44:58'),
(2, 'Kantin Utama', 'kantin1@example.com', 'admin', 295000, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$YYviBwy7uCRyCOiQQxJSmOv2lotUIe5/WchBCIh5BlvVu6AfSyOe2', NULL, '2026-03-05 05:44:58', '2026-03-08 17:40:35'),
(3, 'Kantin Sayuran', 'kantin2@example.com', 'admin', 11000, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$jWOaEa.04gdRgpbdz.qcZOLSTHsc/KS8VF0bxHeZWUy0BAHM3U9eS', NULL, '2026-03-05 05:44:58', '2026-03-05 05:44:58'),
(4, 'siswa', 'siswa123@gmail.com', 'user', 234000, 0, 0, NULL, '2026-03-10 04:43:12', '082122780134', 'siswa123', NULL, NULL, NULL, '$2y$12$DJH1sY8YU13Fi5LzGijv5e73Nh87HQthALJ./0vov3nfiDpqw4nyi', NULL, '2026-03-05 05:49:57', '2026-03-10 08:57:50'),
(5, 'siswake2', 'siswake2@gmail.com', 'user', 90000, NULL, 0, NULL, NULL, '0889223040', 'siswake2', NULL, NULL, NULL, '$2y$12$zoFnkpt2V.NkD0jDCLlkTeyZDeXCDM1j6FTaBhj792qhMxIggcG3u', NULL, '2026-03-05 07:23:36', '2026-03-05 07:23:36'),
(6, 'Operator', 'operator@gmail.com', 'superadmin', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$vtT3iXiSpPrIgp9rC.iBS.agHCTun8YRYmvaTOJ2zwzXSt.mYLYNW', NULL, '2026-03-07 22:45:19', '2026-03-07 22:45:19'),
(7, 'siswa321', 'siswa321@gmail.com', 'user', 0, NULL, 0, NULL, NULL, '082190141034', 'siswa54321', NULL, NULL, NULL, '$2y$12$.T9OBauuIeeDoDd6zsvV9umgSPS9GQbXtF/f9xrSv3wpWCCbCgI.a', NULL, '2026-03-10 04:42:02', '2026-03-10 04:42:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `withdrawal_requests`
--

INSERT INTO `withdrawal_requests` (`id`, `user_id`, `amount`, `bank_name`, `account_number`, `account_name`, `status`, `processed_by`, `processed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 10000, NULL, NULL, '2026-03-16 21:53', 'rejected', 6, '2026-03-08 06:54:45', 'lawak', '2026-03-08 06:54:24', '2026-03-08 06:54:45'),
(2, 2, 10000, NULL, NULL, '2026-03-12 20:56', 'approved', 6, '2026-03-08 06:55:17', 'tes3', '2026-03-08 06:55:03', '2026-03-08 06:55:17'),
(3, 2, 20000, NULL, NULL, '2026-03-08 07:44', 'rejected', 6, '2026-03-08 07:45:16', NULL, '2026-03-08 07:44:53', '2026-03-08 07:45:16'),
(4, 2, 20000, NULL, NULL, NULL, 'rejected', 6, '2026-03-08 17:33:03', 'tidak', '2026-03-08 17:32:41', '2026-03-08 17:33:03'),
(5, 2, 10000, NULL, NULL, NULL, 'approved', 6, '2026-03-08 17:40:35', 'Janji temu: 2026-03-10 jam 10:00. Catatan: wiwok', '2026-03-08 17:40:06', '2026-03-08 17:40:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `balance_transactions`
--
ALTER TABLE `balance_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `balance_transactions_user_id_foreign` (`user_id`),
  ADD KEY `balance_transactions_order_id_foreign` (`order_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `menu_addons`
--
ALTER TABLE `menu_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_addons_menu_id_foreign` (`menu_id`);

--
-- Indeks untuk tabel `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_variants_menu_id_foreign` (`menu_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_seller_id_foreign` (`seller_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_id_foreign` (`menu_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ratings_order_id_menu_id_user_id_unique` (`order_id`,`menu_id`,`user_id`),
  ADD KEY `ratings_menu_id_foreign` (`menu_id`),
  ADD KEY `ratings_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_user_id_foreign` (`user_id`),
  ADD KEY `reports_order_id_foreign` (`order_id`),
  ADD KEY `reports_reviewed_by_foreign` (`reviewed_by`);

--
-- Indeks untuk tabel `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_profiles_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `top_up_requests`
--
ALTER TABLE `top_up_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `top_up_requests_user_id_foreign` (`user_id`),
  ADD KEY `top_up_requests_processed_by_foreign` (`processed_by`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indeks untuk tabel `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawal_requests_user_id_foreign` (`user_id`),
  ADD KEY `withdrawal_requests_processed_by_foreign` (`processed_by`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `balance_transactions`
--
ALTER TABLE `balance_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `menu_addons`
--
ALTER TABLE `menu_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `menu_variants`
--
ALTER TABLE `menu_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `seller_profiles`
--
ALTER TABLE `seller_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `top_up_requests`
--
ALTER TABLE `top_up_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `balance_transactions`
--
ALTER TABLE `balance_transactions`
  ADD CONSTRAINT `balance_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `balance_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menu_addons`
--
ALTER TABLE `menu_addons`
  ADD CONSTRAINT `menu_addons_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD CONSTRAINT `menu_variants_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD CONSTRAINT `seller_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `top_up_requests`
--
ALTER TABLE `top_up_requests`
  ADD CONSTRAINT `top_up_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `top_up_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD CONSTRAINT `withdrawal_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `withdrawal_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
