-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 04:23 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peminjaman_buku_naifa`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'si kancil\' untuk alasan: tugas indo', '2026-04-23 00:29:58', '2026-04-23 00:29:58'),
(2, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 00:30:12', '2026-04-23 00:30:12'),
(3, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'si kancil\' dengan catatan: sudah selesai meminjamnya dan buku kondisinya baik', '2026-04-23 00:30:39', '2026-04-23 00:30:39'),
(4, 1, 'Verifikasi Kembali', 'Admin memverifikasi pengembalian buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 00:31:04', '2026-04-23 00:31:04'),
(5, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 1 buku \'Laskar Pelangi\' untuk alasan: tugas indo', '2026-04-23 00:31:45', '2026-04-23 00:31:45'),
(6, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 1 buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-23 00:31:54', '2026-04-23 00:31:54'),
(7, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Laskar Pelangi\' dengan catatan: saya sudah menyelesaikan tugasnya dan kondisi bukunya sobek halamannya', '2026-04-23 00:32:29', '2026-04-23 00:32:29'),
(8, 1, 'Verifikasi Kembali', 'Admin memverifikasi pengembalian buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-23 00:33:23', '2026-04-23 00:33:23'),
(9, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'Laskar Pelangi\'', '2026-04-23 00:33:44', '2026-04-23 00:33:44'),
(10, 1, 'Persetujuan Pembayaran', 'Admin menyetujui pembayaran denda buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-23 00:33:56', '2026-04-23 00:33:56'),
(11, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'Bumi Manusia\' untuk alasan: tugas sejarah', '2026-04-23 01:23:05', '2026-04-23 01:23:05'),
(12, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'Bumi Manusia\' oleh \'Budi Santoso\'', '2026-04-23 01:27:44', '2026-04-23 01:27:44'),
(13, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Bumi Manusia\' dengan catatan: sudah selesai tugas sejarah nya saya ingin mengembalikannya dan kondisi bukunya itu sedikit robek', '2026-04-23 01:28:30', '2026-04-23 01:28:30'),
(14, 1, 'Verifikasi Kembali', 'Admin memverifikasi pengembalian buku \'Bumi Manusia\' oleh \'Budi Santoso\'', '2026-04-23 01:29:13', '2026-04-23 01:29:13'),
(15, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran ganti_buku untuk denda buku \'Bumi Manusia\'', '2026-04-23 01:29:23', '2026-04-23 01:29:23'),
(16, 1, 'Persetujuan Pembayaran', 'Admin menyetujui pembayaran denda buku \'Bumi Manusia\' oleh \'Budi Santoso\'', '2026-04-23 01:29:49', '2026-04-23 01:29:49'),
(17, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'Sejarah Nasional Indonesia\' untuk alasan: tugas sejarah', '2026-04-23 01:30:37', '2026-04-23 01:30:37'),
(18, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'Sejarah Nasional Indonesia\' oleh \'Budi Santoso\'', '2026-04-23 01:31:18', '2026-04-23 01:31:18'),
(19, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Sejarah Nasional Indonesia\' dengan catatan: saya sudah selesai meminjam nya dan kondisi buku sediki rusak', '2026-04-23 01:31:49', '2026-04-23 01:31:49'),
(20, 1, 'Verifikasi Kembali', 'Admin memverifikasi pengembalian buku \'Sejarah Nasional Indonesia\' oleh \'Budi Santoso\'', '2026-04-23 01:33:42', '2026-04-23 01:33:42'),
(21, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'Sejarah Nasional Indonesia\'', '2026-04-23 01:35:37', '2026-04-23 01:35:37'),
(22, 1, 'Persetujuan Pembayaran', 'Admin menyetujui pembayaran denda buku \'Sejarah Nasional Indonesia\' oleh \'Budi Santoso\'', '2026-04-23 01:36:58', '2026-04-23 01:36:58'),
(23, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 1 buku \'si kancil\' untuk alasan: tugas indo', '2026-04-23 02:17:15', '2026-04-23 02:17:15'),
(24, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 1 buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 02:17:49', '2026-04-23 02:17:49'),
(25, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'si kancil\' dengan catatan: saya sudah selesai meminjamnya kondisi bukunya rusa karna jatuh di got', '2026-04-23 02:18:34', '2026-04-23 02:18:34'),
(26, 1, 'Verifikasi Kembali', 'Admin memverifikasi pengembalian buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 02:19:16', '2026-04-23 02:19:16'),
(27, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'si kancil\'', '2026-04-23 02:29:53', '2026-04-23 02:29:53'),
(28, 1, 'Persetujuan Pembayaran', 'Admin menyetujui pembayaran denda buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 02:33:16', '2026-04-23 02:33:16'),
(29, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'Laskar Pelangi\' untuk alasan: tugas indo', '2026-04-23 11:58:34', '2026-04-23 11:58:34'),
(30, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-23 12:00:00', '2026-04-23 12:00:00'),
(31, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Laskar Pelangi\' dengan catatan: saya sudah mengembalika buku dan kondisinya rusak', '2026-04-23 12:00:44', '2026-04-23 12:00:44'),
(32, 1, 'Pengembalian Buku', 'Admin menandai buku \'Laskar Pelangi\' milik \'Budi Santoso\' sebagai sudah dikembalikan', '2026-04-23 12:34:49', '2026-04-23 12:34:49'),
(33, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran ganti_buku untuk denda buku \'Laskar Pelangi\'', '2026-04-23 12:35:28', '2026-04-23 12:35:28'),
(34, 1, 'Pelunasan Denda', 'Admin menandai denda buku \'Laskar Pelangi\' oleh \'Budi Santoso\' sebagai lunas', '2026-04-23 12:36:20', '2026-04-23 12:36:20'),
(35, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'si kancil\' untuk alasan: tugas cerpen indo', '2026-04-23 14:13:43', '2026-04-23 14:13:43'),
(36, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'si kancil\' oleh \'Budi Santoso\'', '2026-04-23 14:14:41', '2026-04-23 14:14:41'),
(37, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'si kancil\' dengan catatan: sudah selesai meminjamnya dan maaf untuk kondisi bukunya sobek di halaman 2', '2026-04-23 14:15:54', '2026-04-23 14:15:54'),
(38, 1, 'Pengembalian Buku', 'Admin menandai buku \'si kancil\' milik \'Budi Santoso\' sebagai sudah dikembalikan', '2026-04-23 14:16:42', '2026-04-23 14:16:42'),
(39, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'si kancil\'', '2026-04-23 14:16:53', '2026-04-23 14:16:53'),
(40, 1, 'Pelunasan Denda', 'Admin menandai denda buku \'si kancil\' oleh \'Budi Santoso\' sebagai lunas', '2026-04-23 14:18:29', '2026-04-23 14:18:29'),
(41, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 3 buku \'Laskar Pelangi\' untuk alasan: tugas indo', '2026-04-24 01:13:51', '2026-04-24 01:13:51'),
(42, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 3 buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-24 01:14:15', '2026-04-24 01:14:15'),
(43, 2, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Laskar Pelangi\' dengan catatan: saya sudah meminjam nya dan kondisi bukunya sobek diki', '2026-04-24 01:14:38', '2026-04-24 01:14:38'),
(44, 1, 'Pengembalian Buku', 'Admin menandai buku \'Laskar Pelangi\' milik \'Budi Santoso\' sebagai sudah dikembalikan', '2026-04-24 01:15:11', '2026-04-24 01:15:11'),
(45, 2, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'Laskar Pelangi\' dan mengunggah bukti pembayaran.', '2026-04-24 01:30:05', '2026-04-24 01:30:05'),
(46, 1, 'Pelunasan Denda', 'Admin menandai denda buku \'Laskar Pelangi\' oleh \'Budi Santoso\' sebagai lunas', '2026-04-24 01:30:53', '2026-04-24 01:30:53'),
(47, 2, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'Laskar Pelangi\' untuk alasan: tugas indo', '2026-04-24 01:31:38', '2026-04-24 01:31:38'),
(48, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'Laskar Pelangi\' oleh \'Budi Santoso\'', '2026-04-24 01:31:46', '2026-04-24 01:31:46'),
(49, 8, 'Pengajuan Pinjam', 'Pengguna mengajukan pinjam 2 buku \'Putri Duyung (Mermaid)\' untuk alasan: tugas bahasa indonesia', '2026-04-24 03:18:19', '2026-04-24 03:18:19'),
(50, 1, 'Persetujuan Pinjam', 'Admin menyetujui peminjaman 2 buku \'Putri Duyung (Mermaid)\' oleh \'Naifatricandra\'', '2026-04-24 03:19:16', '2026-04-24 03:19:16'),
(51, 8, 'Pengembalian Buku (User)', 'Pengguna mengembalikan buku \'Putri Duyung (Mermaid)\' dengan catatan: sudah selesai meminjam bukunya kondi bukunya sedikisoboke di halaman ke 2', '2026-04-24 03:19:59', '2026-04-24 03:19:59'),
(52, 1, 'Pengembalian Buku', 'Admin menandai buku \'Putri Duyung (Mermaid)\' milik \'Naifatricandra\' sebagai sudah dikembalikan', '2026-04-24 03:20:36', '2026-04-24 03:20:36'),
(53, 8, 'Konfirmasi Denda', 'Pengguna memilih metode pembayaran qris untuk denda buku \'Putri Duyung (Mermaid)\' dan mengunggah bukti pembayaran.', '2026-04-24 03:21:12', '2026-04-24 03:21:12'),
(54, 1, 'Pelunasan Denda', 'Admin menandai denda buku \'Putri Duyung (Mermaid)\' oleh \'Naifatricandra\' sebagai lunas', '2026-04-24 03:21:43', '2026-04-24 03:21:43');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rack_location` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `published_year` int NOT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_count` smallint UNSIGNED DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT '4.0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `genre_tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('tersedia','dipinjam','habis') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `rack_location`, `category_id`, `stock`, `published_year`, `publisher`, `page_count`, `rating`, `description`, `genre_tags`, `cover_image`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', '9789793062791', NULL, 1, 4, 2005, 'Bentang Pustaka', 529, 4.8, 'Kisah perjuangan anak-anak di Belitung.', 'Inspiratif, Persahabatan, Pendidikan', 'covers/4FCPIoVh5qshchcLLm1pnOcm304yGvzKkdEEeRdl.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-24 03:12:51'),
(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', '9789799731234', NULL, 1, 3, 1980, 'Lentera Dipantara', 535, 4.9, 'Karya masterpiece sastra Indonesia era kolonial.', 'Klasik, Sejarah, Drama', 'covers/vFgCD0Iwif8w8Yqgyeri4qbMhhwSS4gO1qIjiOpG.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 01:29:49'),
(3, 'Negeri 5 Menara', 'A. Fuadi', '9789792257458', NULL, 1, 7, 2009, 'Gramedia Pustaka Utama', 423, 4.7, 'Man Jadda Wajada, siapa bersungguh-sungguh akan berhasil.', 'Motivasi, Pendidikan, Petualangan', 'covers/BfTRFBmw37lKZ0ayEdncjnfS6K7QxokTxFwPjRlB.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 00:23:00'),
(4, 'Sejarah Nasional Indonesia', 'Sartono Kartodirdjo', '9789794615456', NULL, 2, 2, 2010, 'Balai Pustaka', 388, 4.4, 'Buku standar sejarah resmi Indonesia.', 'Nasional, Referensi, Akademik', 'covers/MbPqReq7YtA53dDlfU7YGUWEmRV2lIrhnCFhgFrr.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 01:36:58'),
(5, 'Belajar Laravel 11 untuk Pemula', 'Eko Kurniawan Khannedy', '9786020444555', NULL, 3, 10, 2024, 'Informatika Nusantara', 312, 4.6, 'Panduan lengkap membangun web dengan Laravel terbaru.', 'Laravel, Web Dev, Pemrograman', 'covers/TucwYukzNJY5Wq3WAxIoBnrRy1waNaFIZLQaVb8j.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 00:25:34'),
(6, 'Fiqih Sunnah', 'Sayyid Sabiq', '9786022501234', NULL, 4, 4, 2015, 'Pustaka Al-Kautsar', 448, 4.5, 'Tuntunan ibadah sesuai sunnah Rasulullah.', 'Ibadah, Referensi, Keislaman', 'covers/0gMxG66xHepn3P4Lt7e2x3fGzFvLuHNoZVuwrOse.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 00:26:08'),
(7, 'Habibie & Ainun', 'B.J. Habibie', '9789791227000', NULL, 6, 6, 2010, 'THC Mandiri', 323, 4.7, 'Kisah cinta abadi Presiden ke-3 RI.', 'Biografi, Inspiratif, Romansa', 'covers/NCHqBZ7fpZw6P6aVcPfTxcs9WrEGyiajycjoKfRs.jpg', 'tersedia', NULL, '2026-04-22 17:13:29', '2026-04-23 00:27:17'),
(8, 'si kancil', 'naifatric', '23456787654321', NULL, 1, 23, 2022, NULL, NULL, 4.0, 'sangat seru dan menyenang kan', NULL, 'covers/gXaLz64fWOn5cqk8QGrbVSi2Qpomsw323LiiI98L.jpg', 'tersedia', NULL, '2026-04-22 23:53:51', '2026-04-23 14:16:42'),
(9, 'putri duyung', 'Hans Christian Andersen', '9786230312960', NULL, 1, 30, 2022, 'hans', 400, 4.5, 'buku ini bercerita seorang mermaid', NULL, NULL, 'tersedia', '2026-04-23 03:51:53', '2026-04-23 03:49:53', '2026-04-23 03:51:53'),
(10, 'Putri Duyung (Mermaid)', 'M. Rantissi', '9786022184102', NULL, 1, 20, 2018, 'ranti', 300, 4.5, 'buku ini bercerita tentang seorang mermaid', NULL, 'covers/Ca7w6t8wETHJcqJF8Zwfx91XopYoiZRyoBtjuI0j.jpg', 'tersedia', NULL, '2026-04-23 03:55:03', '2026-04-24 03:20:36'),
(11, 'boboyyy', 'boy', '9786020478760.', 'rak16', 1, 23, 2020, 'nai', 200, 4.5, 'aappapapapa', NULL, 'covers/pRysoxYDC2WovGLba8f53nSzZk4Z6SUgoAtZRqpM.jpg', 'tersedia', '2026-04-23 14:10:41', '2026-04-23 14:10:15', '2026-04-23 14:10:41'),
(12, 'jasmani olahraga', 'naifa', '333333354454', 'rak2', 10, 20, 2020, 'siti', 200, 4.5, 'ini adala buku jasmani olahraga', NULL, 'covers/EDqjRp2fAVQ0Qtoix6ypKJ1BDfoITPlscxJaF8G0.jpg', 'tersedia', NULL, '2026-04-24 03:15:12', '2026-04-24 03:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `book_favorites`
--

CREATE TABLE `book_favorites` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `book_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_favorites`
--

INSERT INTO `book_favorites` (`id`, `user_id`, `book_id`, `created_at`, `updated_at`) VALUES
(1, 2, 10, '2026-04-23 10:26:55', '2026-04-23 10:26:55'),
(2, 2, 8, '2026-04-23 14:13:02', '2026-04-23 14:13:02'),
(3, 8, 12, '2026-04-24 03:17:35', '2026-04-24 03:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `book_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `borrow_reason` text COLLATE utf8mb4_unicode_ci,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `fine_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `late_fee_per_day` decimal(10,2) NOT NULL DEFAULT '5000.00',
  `max_fine_amount` decimal(10,2) DEFAULT NULL,
  `grace_period_days` int UNSIGNED NOT NULL DEFAULT '0',
  `loan_duration_days` int UNSIGNED NOT NULL DEFAULT '7',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `return_notes` text COLLATE utf8mb4_unicode_ci,
  `book_condition` enum('baik','rusak_ringan','rusak_berat','hilang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`id`, `user_id`, `book_id`, `quantity`, `borrow_reason`, `borrow_date`, `due_date`, `return_date`, `status`, `fine_amount`, `late_fee_per_day`, `max_fine_amount`, `grace_period_days`, `loan_duration_days`, `created_at`, `updated_at`, `return_notes`, `book_condition`, `admin_notes`) VALUES
(1, 2, 8, 2, 'tugas indo', '2026-04-23', '2026-04-30', '2026-04-23', 'selesai', 0.00, 5000.00, 50000.00, 0, 7, '2026-04-23 00:29:58', '2026-04-23 00:31:04', 'sudah selesai meminjamnya dan buku kondisinya baik', 'baik', 'baik saya kamu jujur'),
(2, 2, 1, 1, 'tugas indo', '2026-04-23', '2026-04-30', '2026-04-23', 'selesai', 20000.00, 5000.00, 50000.00, 0, 7, '2026-04-23 00:31:45', '2026-04-23 00:33:56', 'saya sudah menyelesaikan tugasnya dan kondisi bukunya sobek halamannya', 'rusak_ringan', 'terimakasih sudah jujur terhadap saya dan kamu di kenakan denda'),
(3, 2, 2, 2, 'tugas sejarah', '2026-04-23', '2026-04-30', '2026-04-23', 'selesai', 20000.00, 5000.00, 50000.00, 0, 7, '2026-04-23 01:23:05', '2026-04-23 01:29:49', 'sudah selesai tugas sejarah nya saya ingin mengembalikannya dan kondisi bukunya itu sedikit robek', 'rusak_ringan', 'baik terimakasih sudah mengembalikan dan berkata jujur'),
(4, 2, 4, 2, 'tugas sejarah', '2026-04-23', '2026-04-30', '2026-04-23', 'selesai', 20000.00, 5000.00, 50000.00, 0, 7, '2026-04-23 01:30:37', '2026-04-23 01:36:58', 'saya sudah selesai meminjam nya dan kondisi buku sediki rusak', 'rusak_ringan', 'baik terimakasih'),
(5, 2, 8, 1, 'tugas indo', '2026-04-23', '2026-04-30', '2026-04-23', 'selesai', 30000.00, 5000.00, 50000.00, 0, 7, '2026-04-23 02:17:15', '2026-04-23 02:33:16', 'saya sudah selesai meminjamnya kondisi bukunya rusa karna jatuh di got', 'rusak_berat', 'terimakasih sudah berkata jujur dan kamu harus membayar denda kerusakan'),
(6, 2, 1, 2, 'tugas indo', '2026-04-23', '2026-04-23', '2026-04-23', 'selesai', 30000.00, 5000.00, 50000.00, 0, 1, '2026-04-23 11:58:34', '2026-04-23 12:36:20', 'saya sudah mengembalika buku dan kondisinya rusak', 'rusak_ringan', 'terimakasih'),
(7, 2, 8, 2, 'tugas cerpen indo', '2026-04-23', '2026-04-25', '2026-04-23', 'selesai', 20000.00, 5000.00, 50000.00, 0, 2, '2026-04-23 14:13:43', '2026-04-23 14:18:29', 'sudah selesai meminjamnya dan maaf untuk kondisi bukunya sobek di halaman 2', 'rusak_ringan', 'membayar denda kerusaka sobek di halaman 2'),
(8, 2, 1, 3, 'tugas indo', '2026-04-24', '2026-04-25', '2026-04-24', 'selesai', 19997.00, 5000.00, 50000.00, 0, 1, '2026-04-24 01:13:51', '2026-04-24 01:30:53', 'saya sudah meminjam nya dan kondisi bukunya sobek diki', 'rusak_ringan', 'rusak ringan karna sobek'),
(9, 2, 1, 2, 'tugas indo', '2026-04-24', '2026-04-25', NULL, 'dipinjam', 0.00, 5000.00, 50000.00, 0, 1, '2026-04-24 01:31:38', '2026-04-24 01:31:46', NULL, NULL, NULL),
(10, 8, 10, 2, 'tugas bahasa indonesia', '2026-04-24', '2026-04-25', '2026-04-24', 'selesai', 20000.00, 5000.00, 50000.00, 0, 1, '2026-04-24 03:18:19', '2026-04-24 03:21:43', 'sudah selesai meminjam bukunya kondi bukunya sedikisoboke di halaman ke 2', 'rusak_ringan', 'membayar denda');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Fiksi', 'fiksi', 'Koleksi novel dan cerita fiksi', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(2, 'Sejarah', 'sejarah', 'Buku sejarah Indonesia dan Dunia', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(3, 'Teknologi', 'teknologi', 'Pemrograman, AI, dan Gadget', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(4, 'Agama', 'agama', 'Buku spiritual dan tuntunan agama', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(5, 'Sains', 'sains', 'Ilmu pengetahuan alam dan biologi', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(6, 'Biografi', 'biografi', 'Kisah hidup tokoh inspiratif', NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27'),
(7, 'boboy', 'dudul', 'bagaimana', '2026-04-23 14:07:54', '2026-04-23 03:56:56', '2026-04-23 14:07:54'),
(8, 'biologi', 'biologi', 'apasajaa', '2026-04-23 14:08:00', '2026-04-23 14:07:47', '2026-04-23 14:08:00'),
(9, 'sayuraan', 'sayur', 'apapapapa', NULL, '2026-04-24 00:13:06', '2026-04-24 00:13:06'),
(10, 'pjok', 'pjok', 'jasmani olahraga', NULL, '2026-04-24 03:12:12', '2026-04-24 03:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `id` bigint UNSIGNED NOT NULL,
  `borrowing_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `damage_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `days_late` int NOT NULL,
  `late_fee_per_day` decimal(10,2) NOT NULL DEFAULT '5000.00',
  `max_fine_amount` decimal(10,2) DEFAULT NULL,
  `grace_period_days` int UNSIGNED NOT NULL DEFAULT '0',
  `raw_late_days` int UNSIGNED NOT NULL DEFAULT '0',
  `charged_late_days` int UNSIGNED NOT NULL DEFAULT '0',
  `late_fee_subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('belum_lunas','lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_lunas',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`id`, `borrowing_id`, `amount`, `damage_amount`, `days_late`, `late_fee_per_day`, `max_fine_amount`, `grace_period_days`, `raw_late_days`, `charged_late_days`, `late_fee_subtotal`, `status`, `paid_at`, `created_at`, `updated_at`, `payment_method`, `payment_proof`) VALUES
(1, 2, 20000.00, 20000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 00:33:56', '2026-04-23 00:33:23', '2026-04-23 00:33:56', 'qris', NULL),
(2, 3, 20000.00, 20000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 01:29:49', '2026-04-23 01:29:13', '2026-04-23 01:29:49', 'ganti_buku', NULL),
(3, 4, 20000.00, 20000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 01:36:58', '2026-04-23 01:33:42', '2026-04-23 01:36:58', 'qris', NULL),
(4, 5, 30000.00, 30000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 02:33:16', '2026-04-23 02:19:16', '2026-04-23 02:33:16', 'qris', NULL),
(5, 6, 30000.00, 30000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 12:36:20', '2026-04-23 12:34:49', '2026-04-23 12:36:20', 'ganti_buku', NULL),
(6, 7, 20000.00, 20000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-23 14:18:29', '2026-04-23 14:16:42', '2026-04-23 14:18:29', 'qris', NULL),
(7, 8, 19997.00, 19997.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-24 01:30:53', '2026-04-24 01:15:11', '2026-04-24 01:30:53', 'qris', 'payment-proofs/4J4ATIV2agPKEB3sDLssXgW7CUWkLATrjzjfV4rV.jpg'),
(8, 10, 20000.00, 20000.00, 0, 5000.00, 50000.00, 0, 0, 0, 0.00, 'lunas', '2026-04-24 03:21:43', '2026-04-24 03:20:36', '2026-04-24 03:21:43', 'qris', 'payment-proofs/AdtS0pGDOwaz8rxTUGFoRcOVajniLLw91d49Cbme.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `fine_settings`
--

CREATE TABLE `fine_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `late_fee_per_day` decimal(10,2) NOT NULL DEFAULT '5000.00',
  `max_fine_amount` decimal(10,2) DEFAULT NULL,
  `grace_period_days` int UNSIGNED NOT NULL DEFAULT '0',
  `default_loan_duration_days` int UNSIGNED NOT NULL DEFAULT '7',
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fine_settings`
--

INSERT INTO `fine_settings` (`id`, `late_fee_per_day`, `max_fine_amount`, `grace_period_days`, `default_loan_duration_days`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 5000.00, 50000.00, 0, 7, NULL, '2026-04-22 17:13:27', '2026-04-22 17:13:27');

-- --------------------------------------------------------

--
-- Table structure for table `fine_setting_histories`
--

CREATE TABLE `fine_setting_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `fine_setting_id` bigint UNSIGNED NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `old_late_fee_per_day` decimal(10,2) DEFAULT NULL,
  `new_late_fee_per_day` decimal(10,2) NOT NULL,
  `old_max_fine_amount` decimal(10,2) DEFAULT NULL,
  `new_max_fine_amount` decimal(10,2) DEFAULT NULL,
  `old_grace_period_days` int UNSIGNED DEFAULT NULL,
  `new_grace_period_days` int UNSIGNED NOT NULL,
  `old_default_loan_duration_days` int UNSIGNED DEFAULT NULL,
  `new_default_loan_duration_days` int UNSIGNED NOT NULL,
  `changed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_22_134653_create_categories_table', 1),
(5, '2026_04_22_134654_create_books_table', 1),
(6, '2026_04_22_134655_create_borrowings_table', 1),
(7, '2026_04_22_134655_create_fines_table', 1),
(8, '2026_04_22_134656_create_activity_logs_table', 1),
(9, '2026_04_22_223842_update_borrowings_and_fines_table_for_new_flow', 1),
(10, '2026_04_22_225713_add_condition_and_qris_to_flow', 1),
(11, '2026_04_22_231308_update_flow_with_quantity_and_details', 1),
(12, '2026_04_22_232920_create_fine_settings_table', 1),
(13, '2026_04_23_090000_add_catalog_metadata_to_books_table', 2),
(14, '2026_04_23_110000_repair_catalog_metadata_columns_on_books_table', 3),
(15, '2026_04_23_120000_add_rack_location_to_books_table', 4),
(16, '2026_04_23_130000_add_status_and_last_login_to_users_table', 4),
(17, '2026_04_23_170000_expand_fine_settings_table', 5),
(18, '2026_04_23_170100_create_fine_setting_histories_table', 5),
(19, '2026_04_23_170200_add_fine_policy_snapshots_to_borrowings_and_fines', 5),
(20, '2026_04_23_180000_create_book_favorites_table', 5),
(21, '2026_04_24_090000_add_payment_proof_to_fines_table', 6),
(22, '2026_04_24_120000_add_avatar_to_users_table', 7),
(23, '2026_04_24_123000_add_nisn_to_users_table', 7),
(24, '2026_04_24_124000_backfill_nisn_for_existing_users', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3jlUrwWyzzzRc8Ohu6NI8HG60bJydar20DKAJfJY', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJjOXNpeUR0c2t5N3ozNVRpVUV3UlNvYXBYMFBMZktwallaSzNkWVQ4IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BlbWluamFtXC9ib3Jyb3dpbmdzIiwicm91dGUiOiJwZW1pbmphbS5ib3Jyb3dpbmdzLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo4fQ==', 1777003939),
('X87AxgfF5b7fhN0kCKWuDQOBznjLIbUYB6LFCslR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhVlZ4SnhHc1RqWjkycVA5WjdpMFFJM0ROV3ZuUmM4M3hEQ0dKOER1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1777003933);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','peminjam') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'peminjam',
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `nisn`, `account_status`, `phone`, `address`, `avatar`, `remember_token`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Admin SIPBUK', 'admin@gmail.com', NULL, '$2y$12$0q7oxcxlqDiHCDQGLltzcezG2UiORzkGjvKeqtLLUOZrHF.PmVG7u', 'admin', '9000000001', 'aktif', '081234567890', 'Jl. Merdeka No. 1, Jakarta', NULL, NULL, '2026-04-23 23:57:38', '2026-04-22 17:13:28', '2026-04-23 23:57:38'),
(2, 'Budi Santoso', 'budi@gmail.com', NULL, '$2y$12$DEGSMYgzrec6iHG/WNto/uftXEISXl6wvb5fIAZxznnYUMLwM3fPK', 'peminjam', '9000000002', 'aktif', '082133445566', 'Jl. Mawar No. 12, Bandung', NULL, NULL, '2026-04-23 23:58:00', '2026-04-22 17:13:29', '2026-04-23 23:58:00'),
(3, 'Siti Aminah', 'siti@gmail.com', NULL, '$2y$12$x3KAyQPS2/PeujymYxtrD.faQkT02lEclPP74G.oJf93XRNWmRW2u', 'peminjam', '9000000003', 'nonaktif', '085711223344', 'Griya Asri Blok C, Surabaya', NULL, NULL, NULL, '2026-04-22 17:13:29', '2026-04-24 02:34:33'),
(8, 'Naifatricandra', 'naifa@gmail.com', NULL, '$2y$12$YCNinwJ0nA8Wiza2dETosO3r/3Jzu23UnZ7X6nwCKLODZwmy6MMoa', 'peminjam', '1234678907', 'aktif', '089639283697', NULL, NULL, NULL, '2026-04-24 02:40:51', '2026-04-24 02:23:40', '2026-04-24 02:40:51'),
(9, 'sitiratna', 'ratna@gmail.com', NULL, '$2y$12$ZoHSnvckeuaKDEuW20UhGuQh.2CxcAGzcZnxBe7QugdLqWrzJ8kE2', 'peminjam', '0085188048', 'aktif', '0882135452687', 'jalan ciomas', NULL, NULL, NULL, '2026-04-24 03:17:10', '2026-04-24 03:17:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_isbn_unique` (`isbn`),
  ADD KEY `books_category_id_foreign` (`category_id`);

--
-- Indexes for table `book_favorites`
--
ALTER TABLE `book_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_favorites_user_id_book_id_unique` (`user_id`,`book_id`),
  ADD KEY `book_favorites_book_id_foreign` (`book_id`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrowings_user_id_foreign` (`user_id`),
  ADD KEY `borrowings_book_id_foreign` (`book_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fines_borrowing_id_foreign` (`borrowing_id`);

--
-- Indexes for table `fine_settings`
--
ALTER TABLE `fine_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fine_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `fine_setting_histories`
--
ALTER TABLE `fine_setting_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fine_setting_histories_fine_setting_id_foreign` (`fine_setting_id`),
  ADD KEY `fine_setting_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nisn_unique` (`nisn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `book_favorites`
--
ALTER TABLE `book_favorites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fine_settings`
--
ALTER TABLE `fine_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fine_setting_histories`
--
ALTER TABLE `fine_setting_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_favorites`
--
ALTER TABLE `book_favorites`
  ADD CONSTRAINT `book_favorites_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrowings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_borrowing_id_foreign` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fine_settings`
--
ALTER TABLE `fine_settings`
  ADD CONSTRAINT `fine_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fine_setting_histories`
--
ALTER TABLE `fine_setting_histories`
  ADD CONSTRAINT `fine_setting_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fine_setting_histories_fine_setting_id_foreign` FOREIGN KEY (`fine_setting_id`) REFERENCES `fine_settings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
