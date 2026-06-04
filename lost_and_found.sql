-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2026 at 01:13 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lost_and_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `arsip_barang`
--

CREATE TABLE `arsip_barang` (
  `arsip_id` int NOT NULL,
  `barang_id` int DEFAULT NULL,
  `tanggal_arsip` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `barang_id` int NOT NULL,
  `kode_barang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_barang` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `foto_barang` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori_barang_id` int DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`barang_id`, `kode_barang`, `nama_barang`, `deskripsi`, `foto_barang`, `kategori_barang_id`, `status`, `deleted_at`) VALUES
(19, 'HIL-0019', 'l', 'okk', 'pfUptbwijg8sSLG2l0rY5KbcQNRFW8PUv84IvyHy.jpg', 6, 'belum_ditemukan', NULL),
(20, 'HIL-0020', 'awan', 'adadadada', 'ct0BOiC2H0ZuaI9dj1F3fwz99fiBitc1rbRMG1Mz.png', 1, 'ditemukan', NULL),
(21, 'HIL-0021', 'konts', 'cadad', '1dvw9yg3xRs4YBLvv1UPUqsvDCcdV7KCBS5Qw3v5.jpg', 6, 'dikembalikan', NULL),
(23, 'HIL-0023', 'MBG', 'aaaa', 'kAfyoxRKY854ZmxEe0pxymdGCGiLiY12CKP8vJVV.jpg', 1, 'dikembalikan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `barang_temuan`
--

CREATE TABLE `barang_temuan` (
  `temuan_id` int NOT NULL,
  `barang_id` int DEFAULT NULL,
  `gedung_ditemukan_id` int DEFAULT NULL,
  `tanggal_ditemukan` date DEFAULT NULL,
  `tanggal_diunggah` date DEFAULT NULL,
  `lokasi_ditemukan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ditemukan_oleh` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_temuan`
--

INSERT INTO `barang_temuan` (`temuan_id`, `barang_id`, `gedung_ditemukan_id`, `tanggal_ditemukan`, `tanggal_diunggah`, `lokasi_ditemukan`, `ditemukan_oleh`, `status`, `deleted_at`) VALUES
(10, 23, NULL, '2026-05-27', '2026-05-27', 'Vokasi Veteran - Gedung BNI', NULL, 'dikembalikan', NULL),
(11, 20, NULL, '2026-05-30', '2026-05-30', 'Vokasi Veteran - Gedung Perbankan', 'lan', 'diunggah', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bukti_pengembalian`
--

CREATE TABLE `bukti_pengembalian` (
  `bukti_id` int NOT NULL,
  `klaim_id` int DEFAULT NULL,
  `kode_pengambilan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gedung_pengambilan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bukti_pengembalian`
--

INSERT INTO `bukti_pengembalian` (`bukti_id`, `klaim_id`, `kode_pengambilan`, `gedung_pengambilan_id`) VALUES
(3, 8, 'PNM-0008', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `dosen_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nip` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `connection` text COLLATE utf8mb4_general_ci NOT NULL,
  `queue` text COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `gedung_id` int NOT NULL,
  `nama_gedung` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gedung`
--

INSERT INTO `gedung` (`gedung_id`, `nama_gedung`) VALUES
(1, 'Vokasi Veteran - Gedung BNI'),
(2, 'Vokasi Veteran - Gedung Perbankan'),
(3, 'Vokasi Dieng');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `kategori_barang_id` int NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`kategori_barang_id`, `nama_kategori`) VALUES
(1, 'elektronik'),
(5, 'uang'),
(6, 'buku');

-- --------------------------------------------------------

--
-- Table structure for table `klaim_barang`
--

CREATE TABLE `klaim_barang` (
  `klaim_id` int NOT NULL,
  `temuan_id` int DEFAULT NULL,
  `tanggal_klaim` date DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `bukti_foto` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `verifikasi_kepemilikan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_kehilangan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klaim_barang`
--

INSERT INTO `klaim_barang` (`klaim_id`, `temuan_id`, `tanggal_klaim`, `status`, `user_id`, `bukti_foto`, `verifikasi_kepemilikan`, `tempat_kehilangan`) VALUES
(8, 10, '2026-05-27', 'disetujui', 3, 'smRUrdbWLFwhzM2nkzhdTN6EdLnstc80YQca0LCr.jpg', 'aadad', 'adada');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_kehilangan`
--

CREATE TABLE `laporan_kehilangan` (
  `laporan_id` int NOT NULL,
  `barang_id` int DEFAULT NULL,
  `gedung_id` int DEFAULT NULL,
  `lokasi_detail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_hilang` date DEFAULT NULL,
  `tanggal_lapor` date DEFAULT NULL,
  `status_laporan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_kehilangan`
--

INSERT INTO `laporan_kehilangan` (`laporan_id`, `barang_id`, `gedung_id`, `lokasi_detail`, `tanggal_hilang`, `tanggal_lapor`, `status_laporan`, `user_id`, `deleted_at`) VALUES
(19, 19, 1, 'pp', '2026-05-28', '2026-05-24', 'menunggu', 3, NULL),
(20, 20, 2, 'dda', '2026-06-05', '2026-05-24', 'ditemukan', 3, NULL),
(21, 21, 1, 'adada', '2026-05-01', '2026-05-24', 'selesai', 3, NULL),
(23, 23, 1, 'ppdd', '2026-05-28', '2026-05-27', 'ditemukan', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `mahasiswa_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nim` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `program_studi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`mahasiswa_id`, `user_id`, `nim`, `program_studi`) VALUES
(3, 10, '254140707111039', 'Teknologi Informasi');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_05_13_132753_create_items_table', 1),
(6, '2026_05_15_034450_update_barang_status_enum', 2),
(7, '2026_05_15_035905_add_ditemukan_oleh_to_barang_temuan', 3),
(8, '2026_05_15_040000_add_lokasi_ditemukan_to_barang_temuan', 4),
(9, '2026_05_15_040240_add_lokasi_ditemukan_to_barang_temuan', 4),
(10, '2026_05_24_084517_add_soft_deletes_to_barang_tables', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_general_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(6, 'App\\Models\\User', 9, 'auth_token', '262c8ed9d0d56807c8dd4c38d2e9ffec1f5e49d13d260e7788e4f6beccc46ba8', '[\"*\"]', '2026-05-17 07:02:53', NULL, '2026-05-17 07:02:02', '2026-05-17 07:02:53'),
(10, 'App\\Models\\User', 10, 'auth_token', '8e17a3fcbb2d783e01f5e985839c7b1894f2a7d4dbdcc4a372294d44974e2c46', '[\"*\"]', '2026-05-23 08:28:05', NULL, '2026-05-23 08:04:13', '2026-05-23 08:28:05'),
(13, 'App\\Models\\User', 3, 'auth_token', '4088d30812403da6fe9ba23b6f368efdc102f1e55cc559cbe5e9befee41c1b41', '[\"*\"]', '2026-05-29 19:21:53', NULL, '2026-05-27 06:10:04', '2026-05-29 19:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('mahasiswa','dosen','staff') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `nama`, `email`, `password`, `role`) VALUES
(3, 'Staff Vokasi UB', 'staff@ub.ac.id', '$2y$10$gXW6A6goAb4aEtQ585sTQeEfvGbFsOadeKF35EExWwTlcChA2zmey', 'staff'),
(9, 'Lan', 'pieeesusuuu09@gmail.com', '$2y$10$Np0KpxoOQo8H2wuqvwTznukgslcxVdVRHsEuRjz.bNKy/HOBLbGTu', 'mahasiswa'),
(10, 'Khair Dylan', 'test@gmail', '$2y$10$oyKi1x5uOElaL7AjAM74c.IHAECSAwO/s15BsZIOqQjVUkGX3R3Pu', 'mahasiswa');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip_barang`
--
ALTER TABLE `arsip_barang`
  ADD PRIMARY KEY (`arsip_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `kategori_barang_id` (`kategori_barang_id`);

--
-- Indexes for table `barang_temuan`
--
ALTER TABLE `barang_temuan`
  ADD PRIMARY KEY (`temuan_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `gedung_ditemukan_id` (`gedung_ditemukan_id`);

--
-- Indexes for table `bukti_pengembalian`
--
ALTER TABLE `bukti_pengembalian`
  ADD PRIMARY KEY (`bukti_id`),
  ADD KEY `klaim_id` (`klaim_id`),
  ADD KEY `gedung_pengambilan_id` (`gedung_pengambilan_id`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`dosen_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`gedung_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`kategori_barang_id`);

--
-- Indexes for table `klaim_barang`
--
ALTER TABLE `klaim_barang`
  ADD PRIMARY KEY (`klaim_id`),
  ADD KEY `temuan_id` (`temuan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  ADD PRIMARY KEY (`laporan_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `gedung_id` (`gedung_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`mahasiswa_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip_barang`
--
ALTER TABLE `arsip_barang`
  MODIFY `arsip_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `barang_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `barang_temuan`
--
ALTER TABLE `barang_temuan`
  MODIFY `temuan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bukti_pengembalian`
--
ALTER TABLE `bukti_pengembalian`
  MODIFY `bukti_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `dosen_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `gedung_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `kategori_barang_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `klaim_barang`
--
ALTER TABLE `klaim_barang`
  MODIFY `klaim_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  MODIFY `laporan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `mahasiswa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arsip_barang`
--
ALTER TABLE `arsip_barang`
  ADD CONSTRAINT `arsip_barang_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`) ON DELETE CASCADE;

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kategori_barang_id`) REFERENCES `kategori_barang` (`kategori_barang_id`);

--
-- Constraints for table `barang_temuan`
--
ALTER TABLE `barang_temuan`
  ADD CONSTRAINT `barang_temuan_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`),
  ADD CONSTRAINT `barang_temuan_ibfk_2` FOREIGN KEY (`gedung_ditemukan_id`) REFERENCES `gedung` (`gedung_id`);

--
-- Constraints for table `bukti_pengembalian`
--
ALTER TABLE `bukti_pengembalian`
  ADD CONSTRAINT `bukti_pengembalian_ibfk_1` FOREIGN KEY (`klaim_id`) REFERENCES `klaim_barang` (`klaim_id`),
  ADD CONSTRAINT `bukti_pengembalian_ibfk_2` FOREIGN KEY (`gedung_pengambilan_id`) REFERENCES `gedung` (`gedung_id`);

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `klaim_barang`
--
ALTER TABLE `klaim_barang`
  ADD CONSTRAINT `klaim_barang_ibfk_1` FOREIGN KEY (`temuan_id`) REFERENCES `barang_temuan` (`temuan_id`),
  ADD CONSTRAINT `klaim_barang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  ADD CONSTRAINT `laporan_kehilangan_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`barang_id`),
  ADD CONSTRAINT `laporan_kehilangan_ibfk_3` FOREIGN KEY (`gedung_id`) REFERENCES `gedung` (`gedung_id`),
  ADD CONSTRAINT `laporan_kehilangan_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
