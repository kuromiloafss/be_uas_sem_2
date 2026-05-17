-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 01:56 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

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
  `arsip_id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `tanggal_arsip` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `barang_id` int(11) NOT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto_barang` varchar(255) DEFAULT NULL,
  `kategori_barang_id` int(11) DEFAULT NULL,
  `status` enum('belum_ditemukan','diunggah','diarsipkan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `barang_temuan`
--

CREATE TABLE `barang_temuan` (
  `temuan_id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `gedung_ditemukan_id` int(11) DEFAULT NULL,
  `tanggal_ditemukan` date DEFAULT NULL,
  `tanggal_diunggah` date DEFAULT NULL,
  `status` enum('diunggah','diklaim','diarsipkan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `bukti_pengembalian`
--

CREATE TABLE `bukti_pengembalian` (
  `bukti_id` int(11) NOT NULL,
  `klaim_id` int(11) DEFAULT NULL,
  `kode_pengambilan` varchar(50) DEFAULT NULL,
  `gedung_pengambilan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `dosen_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `gedung_id` int(11) NOT NULL,
  `nama_gedung` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `kategori_barang_id` int(11) NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `klaim_barang`
--

CREATE TABLE `klaim_barang` (
  `klaim_id` int(11) NOT NULL,
  `temuan_id` int(11) DEFAULT NULL,
  `tanggal_klaim` date DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bukti_foto` varchar(50) NOT NULL,
  `verifikasi_kepemilikan` varchar(100) NOT NULL,
  `tempat_kehilangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_kehilangan`
--

CREATE TABLE `laporan_kehilangan` (
  `laporan_id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `gedung_id` int(11) DEFAULT NULL,
  `lokasi_detail` varchar(255) DEFAULT NULL,
  `tanggal_hilang` date DEFAULT NULL,
  `tanggal_lapor` date DEFAULT NULL,
  `status_laporan` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `mahasiswa_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `program_studi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('mahasiswa','dosen','staff') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`gedung_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip_barang`
--
ALTER TABLE `arsip_barang`
  MODIFY `arsip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_temuan`
--
ALTER TABLE `barang_temuan`
  MODIFY `temuan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bukti_pengembalian`
--
ALTER TABLE `bukti_pengembalian`
  MODIFY `bukti_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `dosen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `gedung_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `kategori_barang_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `klaim_barang`
--
ALTER TABLE `klaim_barang`
  MODIFY `klaim_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  MODIFY `laporan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `mahasiswa_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

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