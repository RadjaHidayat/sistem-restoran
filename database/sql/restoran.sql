-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 10, 2026 at 02:13 PM
-- Server version: 8.4.3
-- PHP Version: 8.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoran`
--
CREATE DATABASE IF NOT EXISTS `restoran` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `restoran`;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
CREATE TABLE `detail_pesanan` (
  `id_detail` int NOT NULL,
  `id_pesanan` int NOT NULL,
  `id_menu` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `jumlah`, `harga_satuan`, `subtotal`, `catatan`) VALUES
(1, 1, 4, 1, 24500.00, 24500.00, 'Pedas Maksimal'),
(2, 2, 4, 1, 24500.00, 24500.00, 'Jangan terlalu pedas'),
(3, 3, 4, 1, 24500.00, 24500.00, NULL),
(4, 4, 4, 1, 24500.00, 24500.00, NULL),
(5, 5, 4, 1, 24500.00, 24500.00, NULL),
(6, 6, 4, 2, 24500.00, 49000.00, NULL),
(7, 7, 4, 2, 24500.00, 49000.00, NULL),
(8, 8, 4, 1, 24500.00, 24500.00, NULL),
(9, 9, 4, 1, 24500.00, 24500.00, NULL),
(10, 10, 4, 1, 24500.00, 24500.00, NULL),
(11, 11, 4, 1, 24500.00, 24500.00, NULL),
(12, 12, 4, 1, 24500.00, 24500.00, NULL),
(13, 13, 4, 1, 24500.00, 24500.00, NULL),
(14, 14, 4, 1, 24500.00, 24500.00, NULL),
(15, 14, 5, 1, 10000.00, 10000.00, NULL),
(16, 15, 4, 1, 24500.00, 24500.00, NULL),
(17, 16, 5, 1, 10000.00, 10000.00, NULL),
(18, 17, 4, 1, 24500.00, 24500.00, NULL),
(19, 17, 5, 1, 10000.00, 10000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(4, 'Makanan'),
(5, 'Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

DROP TABLE IF EXISTS `meja`;
CREATE TABLE `meja` (
  `id_meja` int NOT NULL,
  `nomor_meja` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `kode_qr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('kosong','terisi','dipesan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kosong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id_meja`, `nomor_meja`, `kapasitas`, `kode_qr`, `status`) VALUES
(3, 'A1', 2, 'MEJA-W1BOLEILU9', 'kosong'),
(4, 'A2', 4, 'MEJA-IEGGCZYWBW', 'kosong');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nama_menu` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `harga` decimal(10,2) NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tersedia` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `id_kategori`, `nama_menu`, `deskripsi`, `harga`, `foto`, `tersedia`) VALUES
(4, 4, 'Nasi Goreng Seafood', NULL, 24500.00, 'menu/jMOj72UJS3V2MZx2B0T0qRSsawRM9gnEqNGqMzb6.jpg', 1),
(5, 5, 'Es Lemon Tea', NULL, 10000.00, 'menu/y43cSUQqzWW3IHmJ0YuoQWhuVBg4P4sF5B2eshaq.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_pesanan` int NOT NULL,
  `id_kasir` int DEFAULT NULL,
  `metode` enum('tunai','qris','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `status` enum('belum_bayar','lunas','gagal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_bayar',
  `waktu_bayar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `id_kasir`, `metode`, `jumlah_bayar`, `status`, `waktu_bayar`) VALUES
(1, 4, 1, 'tunai', 30000.00, 'lunas', '2026-11-10 02:30:11'),
(2, 5, 1, 'tunai', 30000.00, 'lunas', '2026-11-10 02:31:23'),
(3, 6, 2, 'tunai', 50000.00, 'lunas', '2026-11-10 08:41:33'),
(4, 7, 1, 'tunai', 50000.00, 'lunas', '2026-11-10 08:53:56'),
(5, 8, 1, 'tunai', 25000.00, 'lunas', '2026-11-10 09:50:03'),
(6, 9, 1, 'tunai', 25000.00, 'lunas', '2026-11-10 09:50:51'),
(7, 10, 1, 'tunai', 50000.00, 'lunas', '2026-11-10 12:25:51'),
(8, 12, 1, 'qris', 24500.00, 'lunas', '2026-11-10 15:47:23'),
(9, 11, 1, 'tunai', 25000.00, 'lunas', '2026-11-10 19:26:44'),
(10, 13, 1, 'tunai', 24500.00, 'lunas', '2026-11-10 19:26:56'),
(11, 14, 1, 'tunai', 50000.00, 'lunas', '2026-11-10 19:40:46'),
(12, 1, 2, 'tunai', 25000.00, 'lunas', '2026-11-10 19:43:15'),
(13, 2, 2, 'tunai', 25000.00, 'lunas', '2026-11-10 19:43:36'),
(14, 15, 1, 'tunai', 50000.00, 'lunas', '2026-11-10 19:49:23'),
(15, 16, 1, 'tunai', 10000.00, 'lunas', '2026-11-10 19:49:51'),
(16, 17, 1, 'tunai', 35000.00, 'lunas', '2026-11-10 19:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `kode_pesanan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_meja` int DEFAULT NULL,
  `id_petugas` int DEFAULT NULL,
  `tipe_pesanan` enum('dine_in','takeaway','delivery') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('baru','dimasak','siap','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `total_harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `alamat_antar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `kode_pesanan`, `id_user`, `id_meja`, `id_petugas`, `tipe_pesanan`, `status`, `total_harga`, `alamat_antar`, `created_at`) VALUES
(1, 'ORD-261109-FUJAW', NULL, 3, 1, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-09 18:58:48'),
(2, 'ORD-261109-CEEEK', NULL, NULL, 1, 'delivery', 'selesai', 24500.00, 'Bumi huda persada', '2026-11-09 19:00:06'),
(3, 'ORD-261109-AGRNZ', NULL, 3, 1, 'dine_in', 'dibatalkan', 24500.00, NULL, '2026-11-09 19:03:04'),
(4, 'ORD-261110-FARMP', NULL, 3, 1, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-09 19:27:17'),
(5, 'ORD-261110-PQEB9', NULL, NULL, 1, 'takeaway', 'selesai', 24500.00, NULL, '2026-11-09 19:30:30'),
(6, 'ORD-261110-EPZXG', 4, NULL, 1, 'takeaway', 'selesai', 49000.00, NULL, '2026-11-10 01:39:47'),
(7, 'ORD-261110-UM8BK', NULL, 3, NULL, 'dine_in', 'selesai', 49000.00, NULL, '2026-11-10 01:48:27'),
(8, 'ORD-261110-FL4G1', NULL, 3, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 02:42:05'),
(9, 'ORD-261110-7QPFV', 6, 3, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 02:47:23'),
(10, 'ORD-261110-T5XXX', 4, 3, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 02:52:45'),
(11, 'ORD-261110-OZLPJ', 4, NULL, NULL, 'takeaway', 'selesai', 24500.00, NULL, '2026-11-10 08:35:53'),
(12, 'ORD-261110-ODHXV', NULL, 3, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 08:45:48'),
(13, 'ORD-261110-0YKIW', 4, 3, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 08:48:58'),
(14, 'ORD-261110-JTSAZ', 4, NULL, NULL, 'delivery', 'selesai', 34500.00, 'Bumi', '2026-11-10 12:38:40'),
(15, 'ORD-261110-12ICM', 4, 4, NULL, 'dine_in', 'selesai', 24500.00, NULL, '2026-11-10 12:46:47'),
(16, 'ORD-261110-JN6XI', 4, 4, NULL, 'dine_in', 'selesai', 10000.00, NULL, '2026-11-10 12:46:52'),
(17, 'ORD-261110-8FHFM', 4, 4, NULL, 'dine_in', 'selesai', 34500.00, NULL, '2026-11-10 12:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

DROP TABLE IF EXISTS `reservasi`;
CREATE TABLE `reservasi` (
  `id_reservasi` int NOT NULL,
  `id_user` int NOT NULL,
  `id_meja` int DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `jumlah_orang` int NOT NULL,
  `status` enum('menunggu','dikonfirmasi','dibatalkan','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `catatan` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_user`, `id_meja`, `tanggal`, `jam`, `jumlah_orang`, `status`, `catatan`) VALUES
(1, 4, 3, '2026-12-01', '19:00:00', 2, 'dibatalkan', NULL),
(2, 6, 3, '2026-11-12', '13:30:00', 2, 'dibatalkan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id_role` int NOT NULL,
  `nama_role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `nama_role`) VALUES
(1, 'admin'),
(2, 'kasir'),
(3, 'koki'),
(4, 'pelanggan');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `id_role` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `id_role`, `nama`, `email`, `password`, `no_hp`, `alamat`, `created_at`) VALUES
(1, 1, 'Admin', 'admin@restoran.test', '$2y$12$7p.MhX6nEbWRzfG43GX8.Ov0odBxuXqvpBxqlI1HAu6hQ6CuM4NTe', NULL, NULL, '2026-11-09 09:16:20'),
(2, 2, 'Budi', 'kasir@restoran.test', '$2y$12$FtXtSwWycxuoLBj4baHzEulXIHHKoW/4VkqaFcVSJyBDrmn2sMk7S', NULL, NULL, '2026-11-09 11:46:26'),
(4, 4, 'Joko', 'joko@restoran.test', '$2y$12$u5nUJrw43qgiIzpyRprsf.C0duiqUig41TAEWhoGLnNGbtlg7RiSG', NULL, NULL, '2026-11-10 01:02:14'),
(5, 3, 'Mirai', 'koki@restoran.test', '$2y$12$HxEhZtMEVSdYX76tznekYeqX.s4cWOcHLquIoOtqxBcL.0634mo5y', NULL, NULL, '2026-11-10 01:38:11'),
(6, 4, 'kaka', 'kaka@restoran.test', '$2y$12$LOH5j.9lawg2YIE07jjvVemZqxdAw3EK7Bua3zvE1l1t0fVHG1Kcq', '0812345678', NULL, '2026-11-10 02:37:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_pesanan` (`id_pesanan`),
  ADD KEY `fk_detail_menu` (`id_menu`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`),
  ADD UNIQUE KEY `nomor_meja` (`nomor_meja`),
  ADD UNIQUE KEY `kode_qr` (`kode_qr`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `fk_menu_kategori` (`id_kategori`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `fk_pembayaran_kasir` (`id_kasir`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  ADD KEY `fk_pesanan_user` (`id_user`),
  ADD KEY `fk_pesanan_meja` (`id_meja`),
  ADD KEY `fk_pesanan_petugas` (`id_petugas`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `fk_reservasi_user` (`id_user`),
  ADD KEY `fk_reservasi_meja` (`id_meja`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `nama_role` (`nama_role`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `fk_menu_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_kasir` FOREIGN KEY (`id_kasir`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_meja` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`),
  ADD CONSTRAINT `fk_pesanan_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `fk_reservasi_meja` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`),
  ADD CONSTRAINT `fk_reservasi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
