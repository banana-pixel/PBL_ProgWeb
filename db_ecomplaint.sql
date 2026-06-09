-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 31, 2026 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecomplaint`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `nama_admin` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Admin`
--

INSERT INTO `Admin` (`id_admin`, `username`, `nama_admin`, `password`) VALUES
(1, 'admin_pusat', 'Admin Pengelola Fasilitas', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `Kategori`
--

CREATE TABLE `Kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Kategori`
--

INSERT INTO `Kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Fasilitas'),
(2, 'Akademik'),
(3, 'Keamanan');

-- --------------------------------------------------------

--
-- Table structure for table `Mahasiswa`
--

CREATE TABLE `Mahasiswa` (
  `nrp` varchar(20) NOT NULL,
  `nama_mahasiswa` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Mahasiswa`
--

INSERT INTO `Mahasiswa` (`nrp`, `nama_mahasiswa`, `email`, `no_hp`, `password`) VALUES
('2473025', 'Vito Elroy Wiratara', '2473025@maranatha.ac.id', '085155227862', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `Pengaduan`
--

CREATE TABLE `Pengaduan` (
  `id_pengaduan` varchar(20) NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `tanggal_lapor` datetime DEFAULT NULL,
  `detail_keluhan` text DEFAULT NULL,
  `foto_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('Pending','On Progress','Resolve') DEFAULT 'Pending',
  `tanggapan_admin` text DEFAULT NULL,
  `waktu_on_progress` datetime DEFAULT NULL,
  `waktu_resolve` datetime DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `komentar_mahasiswa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Pengaduan`
--

INSERT INTO `Pengaduan` (`id_pengaduan`, `nrp`, `id_kategori`, `id_admin`, `tanggal_lapor`, `detail_keluhan`, `foto_pendukung`, `status`, `tanggapan_admin`, `waktu_on_progress`, `waktu_resolve`, `rating`, `komentar_mahasiswa`) VALUES
('T-20260531-112126', '2473025', 3, 1, '2026-05-31 11:21:26', 'Meja rusak ', '1780219286_654688413_3868876966581484_6760644561962435002_n.jpg', 'Resolve', 'Meja sudah diganti dengan yang baru', '2026-05-31 16:37:05', '2026-05-31 16:37:43', 4, NULL),
('T-20260531-114353', '2473025', 1, 1, '2026-05-31 11:43:53', 'Lampu ruangan mati', '1780220633_placeholder.png', 'Resolve', 'sudah selesai', '2026-05-31 16:44:15', '2026-05-31 16:44:29', 4, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `Kategori`
--
ALTER TABLE `Kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `Mahasiswa`
--
ALTER TABLE `Mahasiswa`
  ADD PRIMARY KEY (`nrp`);

--
-- Indexes for table `Pengaduan`
--
ALTER TABLE `Pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `nrp` (`nrp`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Admin`
--
ALTER TABLE `Admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Kategori`
--
ALTER TABLE `Kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Pengaduan`
--
ALTER TABLE `Pengaduan`
  ADD CONSTRAINT `Pengaduan_ibfk_1` FOREIGN KEY (`nrp`) REFERENCES `Mahasiswa` (`nrp`),
  ADD CONSTRAINT `Pengaduan_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `Kategori` (`id_kategori`),
  ADD CONSTRAINT `Pengaduan_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `Admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
