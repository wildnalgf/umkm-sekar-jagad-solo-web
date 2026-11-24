-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2026 at 12:56 PM
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
-- Database: `toko_umkm`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`) VALUES
(3, 'Fashion'),
(1, 'Makanan'),
(2, 'Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int UNSIGNED NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` int UNSIGNED NOT NULL,
  `jumlah` tinyint UNSIGNED NOT NULL,
  `harga_satuan` int UNSIGNED NOT NULL,
  `catatan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `kode`, `waktu`, `nama`, `whatsapp`, `id_produk`, `jumlah`, `harga_satuan`, `catatan`) VALUES
(1, 'PSN-0001', '2026-11-12 09:28:00', 'Siti Aminah', '081234567890', 3, 2, 45000, 'Gilingan halus'),
(2, 'PSN-0002', '2026-11-12 10:05:00', 'Budi Santoso', '085711112222', 1, 5, 15000, NULL),
(3, 'PSN-0003', '2026-11-12 11:40:00', 'Rina Wulandari', '081311112222', 5, 1, 18000, NULL),
(4, 'PSN-0004', '2026-11-24 19:52:34', 'Wildan Maulana', '08487826439763', 4, 1, 350000, '');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int UNSIGNED NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kategori` int UNSIGNED NOT NULL,
  `harga` int UNSIGNED NOT NULL,
  `stok` int UNSIGNED NOT NULL DEFAULT '0',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kode`, `nama`, `id_kategori`, `harga`, `stok`, `deskripsi`, `dibuat_pada`) VALUES
(1, 'P01', 'Keripik Tempe Original', 1, 15000, 40, 'Tempe tipis digoreng renyah, tanpa pengawet. Berat 250 gram.', '2026-11-24 10:56:31'),
(2, 'P02', 'Sambal Bawang Pedas', 1, 25000, 0, 'Sambal bawang khas Solo dalam botol kaca 150 ml.', '2026-11-24 10:56:31'),
(3, 'P03', 'Kopi Robusta Lereng Lawu', 2, 45000, 8, 'Biji kopi robusta sangrai sedang. Tersedia bubuk atau biji.', '2026-11-24 10:56:31'),
(4, 'P04', 'Batik Tulis Parang', 3, 350000, 2, 'Kain batik tulis motif parang, ukuran 2 x 1,15 meter.', '2026-11-24 10:56:31'),
(5, 'P05', 'Wedang Uwuh Instan', 2, 18000, 25, 'Rempah wedang uwuh siap seduh, isi 10 sachet.', '2026-11-24 10:56:31'),
(6, 'P06', 'Serabi <Notosuman>', 1, 30000, 12, 'Serabi kuah santan, dikirim dalam kotak isi 10.', '2026-11-24 10:56:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_kategori_nama` (`nama`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pesanan_kode` (`kode`),
  ADD KEY `fk_pesanan_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_produk_kode` (`kode`),
  ADD KEY `fk_produk_kategori` (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
