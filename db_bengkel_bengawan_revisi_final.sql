-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 10:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bengkel_bengawan`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pengerjaan`
--

CREATE TABLE `detail_pengerjaan` (
  `id_detail_pengerjaan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_permintaan` int(11) NOT NULL,
  `status_pengerjaan` enum('assigned','pending','done') NOT NULL DEFAULT 'pending',
  `tanggal_mulai_kerja` date NOT NULL,
  `tanggal_selesai_kerja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pengerjaan`
--

INSERT INTO `detail_pengerjaan` (`id_detail_pengerjaan`, `id_pengguna`, `id_permintaan`, `status_pengerjaan`, `tanggal_mulai_kerja`, `tanggal_selesai_kerja`) VALUES
(2, 6, 7, 'pending', '2026-05-14', NULL),
(3, 6, 8, 'pending', '2026-05-15', NULL),
(4, 6, 9, 'pending', '2026-05-15', NULL),
(5, 6, 10, 'pending', '2026-05-18', NULL),
(6, 6, 11, 'assigned', '2026-05-18', NULL),
(7, 12, 12, 'done', '2026-05-19', '2026-05-19'),
(8, 6, 13, 'done', '2026-05-19', '2026-05-19'),
(9, 6, 14, 'assigned', '2026-05-19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_servis`
--

CREATE TABLE `detail_servis` (
  `id_detail_servis` int(11) NOT NULL,
  `id_permintaan` int(11) DEFAULT NULL,
  `id_jasa` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `total_biaya_jasa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_servis`
--

INSERT INTO `detail_servis` (`id_detail_servis`, `id_permintaan`, `id_jasa`, `qty`, `total_biaya_jasa`) VALUES
(2, 7, 1, 1, 500000),
(3, 8, 2, 1, 800000),
(4, 9, 1, 1, 500000),
(5, 10, 1, 1, 500000),
(6, 11, 1, 1, 500000),
(7, 12, 1, 1, 500000),
(8, 13, 1, 1, 500000),
(9, 14, 1, 1, 500000);

-- --------------------------------------------------------

--
-- Table structure for table `detail_sparepart`
--

CREATE TABLE `detail_sparepart` (
  `id_detail_sparepart` int(11) NOT NULL,
  `id_permintaan` int(11) DEFAULT NULL,
  `id_sparepart` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `harga_satuan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_sparepart`
--

INSERT INTO `detail_sparepart` (`id_detail_sparepart`, `id_permintaan`, `id_sparepart`, `qty`, `harga_satuan`) VALUES
(1, 8, 3, 1, 120000),
(2, 9, 3, 1, 120000),
(3, 9, 4, 1, 150000),
(4, 10, 3, 1, 120000),
(5, 10, 5, 1, 50000),
(8, 11, 3, 1, 120000),
(9, 11, 5, 1, 50000),
(10, 12, 5, 1, 50000),
(13, 13, 3, 1, 120000),
(14, 14, 3, 1, 120000),
(15, 14, 5, 1, 50000);

-- --------------------------------------------------------

--
-- Table structure for table `jasa`
--

CREATE TABLE `jasa` (
  `id_jasa` int(11) NOT NULL,
  `id_jenis_jasa` int(11) NOT NULL,
  `nama_jasa` varchar(255) NOT NULL,
  `estimasi_waktu` int(11) NOT NULL,
  `harga_jasa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jasa`
--

INSERT INTO `jasa` (`id_jasa`, `id_jenis_jasa`, `nama_jasa`, `estimasi_waktu`, `harga_jasa`) VALUES
(1, 1, 'Repair Custom', 90, 500000),
(2, 2, 'Refinishing Standart', 200, 800000);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_jasa`
--

CREATE TABLE `jenis_jasa` (
  `id_jenis_jasa` int(11) NOT NULL,
  `nama_jenis_jasa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_jasa`
--

INSERT INTO `jenis_jasa` (`id_jenis_jasa`, `nama_jenis_jasa`) VALUES
(1, 'Body Repair'),
(2, 'Refinishing'),
(3, 'Restoration / Detailing'),
(4, 'Extensive Services'),
(5, 'Low Rider (Ceper)');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_sparepart`
--

CREATE TABLE `kategori_sparepart` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_sparepart`
--

INSERT INTO `kategori_sparepart` (`id_kategori`, `nama_kategori`) VALUES
(6, 'test'),
(12, 'Lampu LED'),
(13, 'Oli ');

-- --------------------------------------------------------

--
-- Table structure for table `mekanik`
--

CREATE TABLE `mekanik` (
  `id_pengguna` int(11) NOT NULL,
  `nama_mekanik` varchar(100) NOT NULL,
  `spesialisasi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mekanik`
--

INSERT INTO `mekanik` (`id_pengguna`, `nama_mekanik`, `spesialisasi`) VALUES
(6, 'Edo', 'ngelas'),
(12, 'sebastian', 'Finishing');

-- --------------------------------------------------------

--
-- Table structure for table `merk`
--

CREATE TABLE `merk` (
  `id_merk` int(11) NOT NULL,
  `nama_merk` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merk`
--

INSERT INTO `merk` (`id_merk`, `nama_merk`) VALUES
(4, 'test'),
(5, 'lagi test'),
(6, 'test'),
(7, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `merk_mobil` varchar(255) NOT NULL,
  `tipe_mobil` varchar(255) NOT NULL,
  `plat_nomor` varchar(8) NOT NULL,
  `tahun_mobil` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `id_pelanggan`, `merk_mobil`, `tipe_mobil`, `plat_nomor`, `tahun_mobil`) VALUES
(2, 1, 'toyota', 'gatau', 'bw1231', NULL),
(6, 8, 'toyota', 'avanza', 'B5671711', '2012'),
(7, 9, 'Honda', 'Brio', 'B7613871', '2008'),
(9, 11, 'Toyota', 'Avanza', 'B761531', '2012');

-- --------------------------------------------------------

--
-- Table structure for table `password_requests`
--

CREATE TABLE `password_requests` (
  `id_request` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `request_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_requests`
--

INSERT INTO `password_requests` (`id_request`, `id_pengguna`, `status`, `request_date`) VALUES
(1, 6, 'completed', '2026-05-13 13:19:36'),
(2, 12, 'completed', '2026-05-18 22:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(255) NOT NULL,
  `no_telp` varchar(11) NOT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telp`, `alamat`) VALUES
(1, 'budi', '1234567', NULL),
(8, 'asep', '2147483647', 'jalan karet kuningan no.24'),
(9, 'lufti', '2147483647', 'jalan pinangsia 7'),
(11, 'Jared', '0817632122', 'Jalan pisang goreng');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_bayar` int(11) NOT NULL,
  `id_permintaan` int(11) NOT NULL,
  `jumlah_bayar` int(11) NOT NULL,
  `metode_pembayaran` enum('cash','transfer') NOT NULL,
  `status_pembayaran` enum('lunas termin1','lunas termin2','lunas termin3') DEFAULT NULL,
  `tanggal_bayar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_bayar`, `id_permintaan`, `jumlah_bayar`, `metode_pembayaran`, `status_pembayaran`, `tanggal_bayar`) VALUES
(4, 9, 770000, 'transfer', 'lunas termin1', '2026-05-15'),
(5, 10, 670000, 'cash', 'lunas termin1', '2026-05-18'),
(6, 11, 670000, 'cash', 'lunas termin1', '2026-05-18'),
(7, 9, 770000, 'cash', 'lunas termin2', '2026-05-19'),
(8, 12, 550000, 'cash', 'lunas termin1', '2026-05-19'),
(9, 13, 320000, 'transfer', 'lunas termin1', '2026-05-19'),
(10, 13, 206666, 'cash', 'lunas termin2', '2026-05-19'),
(11, 13, 206668, 'cash', 'lunas termin3', '2026-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','service_advisor','mechanic','') NOT NULL,
  `is_first_login` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `role`, `is_first_login`) VALUES
(4, 'sandi', 'e10adc3949ba59abbe56e057f20f883e', 'owner', 0),
(5, 'andi', 'e10adc3949ba59abbe56e057f20f883e', 'service_advisor', 0),
(6, 'Edo', 'e10adc3949ba59abbe56e057f20f883e', 'mechanic', 0),
(12, 'sebastian', '3a8a81bf5725274a43cddffa0b8587c2', 'mechanic', 1),
(13, 'rafael', 'e10adc3949ba59abbe56e057f20f883e', 'service_advisor', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_servis`
--

CREATE TABLE `permintaan_servis` (
  `id_permintaan` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `keluhan` varchar(255) NOT NULL,
  `tanggal_masuk` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_keluar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permintaan_servis`
--

INSERT INTO `permintaan_servis` (`id_permintaan`, `id_mobil`, `keluhan`, `tanggal_masuk`, `tanggal_keluar`) VALUES
(7, 6, 'test', '2026-05-15 01:24:45', NULL),
(8, 7, 'ganti ban mobil dll.', '2026-05-16 02:28:42', NULL),
(9, 6, 'servis biasa', '2026-05-16 02:37:47', NULL),
(10, 2, 'ban bocor dll', '2026-05-18 14:19:58', NULL),
(11, 2, 'blah', '2026-05-18 22:47:43', NULL),
(12, 9, 'mobil licin jadinya mau ganti ban mobil', '2026-05-19 18:43:44', '2026-05-19 19:47:11'),
(13, 9, 'mau benerin full', '2026-05-19 19:56:53', '2026-05-19 20:00:21'),
(14, 2, 'bleh bleh bleh', '2026-05-19 20:05:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`, `singkatan`) VALUES
(1, 'Milimeter', 'mm'),
(2, 'Liter', 'L'),
(3, 'Mili-liter', 'ml');

-- --------------------------------------------------------

--
-- Table structure for table `sparepart`
--

CREATE TABLE `sparepart` (
  `id_sparepart` int(11) NOT NULL,
  `nama_sparepart` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_merk` int(11) NOT NULL,
  `id_tipe_sp` int(11) NOT NULL,
  `id_satuan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sparepart`
--

INSERT INTO `sparepart` (`id_sparepart`, `nama_sparepart`, `stock`, `harga_jual`, `id_kategori`, `id_merk`, `id_tipe_sp`, `id_satuan`) VALUES
(3, 'test', 7, 120000, 6, 5, 5, 2),
(4, 'oli', 0, 150000, 6, 5, 4, 2),
(5, 'Ban mobil', 1, 50000, 6, 5, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tipe_sparepart`
--

CREATE TABLE `tipe_sparepart` (
  `id_tipe_sp` int(11) NOT NULL,
  `nama_tipe` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipe_sparepart`
--

INSERT INTO `tipe_sparepart` (`id_tipe_sp`, `nama_tipe`) VALUES
(1, 'testing'),
(4, 'test1'),
(5, 'test');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pengerjaan`
--
ALTER TABLE `detail_pengerjaan`
  ADD PRIMARY KEY (`id_detail_pengerjaan`),
  ADD KEY `fk_pengerjaan_pengguna` (`id_pengguna`),
  ADD KEY `fk_pengerjaan_permintaan` (`id_permintaan`);

--
-- Indexes for table `detail_servis`
--
ALTER TABLE `detail_servis`
  ADD PRIMARY KEY (`id_detail_servis`),
  ADD KEY `fk_dtl_permintaan` (`id_permintaan`),
  ADD KEY `fk_dtl_jasa` (`id_jasa`);

--
-- Indexes for table `detail_sparepart`
--
ALTER TABLE `detail_sparepart`
  ADD PRIMARY KEY (`id_detail_sparepart`),
  ADD KEY `fk_dsp_permintaan` (`id_permintaan`),
  ADD KEY `fk_dsp_sparepart` (`id_sparepart`);

--
-- Indexes for table `jasa`
--
ALTER TABLE `jasa`
  ADD PRIMARY KEY (`id_jasa`),
  ADD KEY `fk_jasa_jenis` (`id_jenis_jasa`);

--
-- Indexes for table `jenis_jasa`
--
ALTER TABLE `jenis_jasa`
  ADD PRIMARY KEY (`id_jenis_jasa`);

--
-- Indexes for table `kategori_sparepart`
--
ALTER TABLE `kategori_sparepart`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `mekanik`
--
ALTER TABLE `mekanik`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `merk`
--
ALTER TABLE `merk`
  ADD PRIMARY KEY (`id_merk`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `password_requests`
--
ALTER TABLE `password_requests`
  ADD PRIMARY KEY (`id_request`),
  ADD KEY `fk_request_pengguna` (`id_pengguna`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_bayar`),
  ADD KEY `id_permintaan` (`id_permintaan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `permintaan_servis`
--
ALTER TABLE `permintaan_servis`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `fk_servis_mobil` (`id_mobil`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `sparepart`
--
ALTER TABLE `sparepart`
  ADD PRIMARY KEY (`id_sparepart`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_merk` (`id_merk`),
  ADD KEY `id_tipe_sp` (`id_tipe_sp`),
  ADD KEY `id_satuan` (`id_satuan`) USING BTREE;

--
-- Indexes for table `tipe_sparepart`
--
ALTER TABLE `tipe_sparepart`
  ADD PRIMARY KEY (`id_tipe_sp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pengerjaan`
--
ALTER TABLE `detail_pengerjaan`
  MODIFY `id_detail_pengerjaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `detail_servis`
--
ALTER TABLE `detail_servis`
  MODIFY `id_detail_servis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `detail_sparepart`
--
ALTER TABLE `detail_sparepart`
  MODIFY `id_detail_sparepart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `jasa`
--
ALTER TABLE `jasa`
  MODIFY `id_jasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jenis_jasa`
--
ALTER TABLE `jenis_jasa`
  MODIFY `id_jenis_jasa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kategori_sparepart`
--
ALTER TABLE `kategori_sparepart`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mekanik`
--
ALTER TABLE `mekanik`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `merk`
--
ALTER TABLE `merk`
  MODIFY `id_merk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_requests`
--
ALTER TABLE `password_requests`
  MODIFY `id_request` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_bayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permintaan_servis`
--
ALTER TABLE `permintaan_servis`
  MODIFY `id_permintaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sparepart`
--
ALTER TABLE `sparepart`
  MODIFY `id_sparepart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tipe_sparepart`
--
ALTER TABLE `tipe_sparepart`
  MODIFY `id_tipe_sp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pengerjaan`
--
ALTER TABLE `detail_pengerjaan`
  ADD CONSTRAINT `fk_detail_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_servis` (`id_permintaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengerjaan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengerjaan_permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_servis` (`id_permintaan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_servis`
--
ALTER TABLE `detail_servis`
  ADD CONSTRAINT `fk_dtl_jasa` FOREIGN KEY (`id_jasa`) REFERENCES `jasa` (`id_jasa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dtl_permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_servis` (`id_permintaan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_sparepart`
--
ALTER TABLE `detail_sparepart`
  ADD CONSTRAINT `fk_dsp_permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_servis` (`id_permintaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dsp_sparepart` FOREIGN KEY (`id_sparepart`) REFERENCES `sparepart` (`id_sparepart`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jasa`
--
ALTER TABLE `jasa`
  ADD CONSTRAINT `fk_jasa_jenis` FOREIGN KEY (`id_jenis_jasa`) REFERENCES `jenis_jasa` (`id_jenis_jasa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mekanik`
--
ALTER TABLE `mekanik`
  ADD CONSTRAINT `fk_mekanik_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mobil`
--
ALTER TABLE `mobil`
  ADD CONSTRAINT `mobil_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_requests`
--
ALTER TABLE `password_requests`
  ADD CONSTRAINT `fk_req_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permintaan_servis`
--
ALTER TABLE `permintaan_servis`
  ADD CONSTRAINT `fk_servis_mobil` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`) ON DELETE CASCADE;

--
-- Constraints for table `sparepart`
--
ALTER TABLE `sparepart`
  ADD CONSTRAINT `fk_kategori_sparepart` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_sparepart` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_merk_sparepart` FOREIGN KEY (`id_merk`) REFERENCES `merk` (`id_merk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_satuan_sparepart` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tipe_sparepart` FOREIGN KEY (`id_tipe_sp`) REFERENCES `tipe_sparepart` (`id_tipe_sp`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
