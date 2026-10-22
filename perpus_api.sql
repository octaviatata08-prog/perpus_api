-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 04:27 AM
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
-- Database: `perpus_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `kode_buku` varchar(20) DEFAULT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `kode_buku`, `judul`, `penulis`, `kategori`, `tahun_terbit`, `penerbit`) VALUES
(1, 'BK001', 'Laskar Pelangi', 'Andrea Hirata', 'Novel', '2005', 'Bentang Pustaka'),
(2, 'BK002', 'Bumi', 'Tere Liye', 'Novel', '2014', 'Gramedia'),
(3, 'BK003', 'Pemrograman PHP', 'Abdul Kadir', 'Teknologi', '2020', 'Andi'),
(4, 'BK004', 'Belajar HTML dan CSS', 'Jubilee Enterprise', 'Teknologi', '2021', 'Elex Media'),
(5, 'BK005', 'Dasar-Dasar JavaScript', 'Wahana Komputer', 'Teknologi', '2022', 'Andi'),
(6, 'BK006', 'Negeri 5 Menara', 'Ahmad Fuadi', 'Novel', '2009', 'Gramedia'),
(7, 'BK007', 'Dilan 1990', 'Pidi Baiq', 'Novel', '2014', 'Pastel Books'),
(8, 'BK008', 'Pemrograman Web', 'Rosa A.S.', 'Teknologi', '2021', 'Informatika'),
(9, 'BK009', 'Algoritma dan Pemrograman', 'Budi Raharjo', 'Pendidikan', '2020', 'Informatika'),
(10, 'BK010', 'Belajar MySQL', 'Kurniawan', 'Database', '2022', 'Elex Media'),
(12, 'BK011', 'Belajar Laravel', 'Gita', 'Teknologi', '2024', 'Informatika'),
(14, 'BK012', 'Cara Menjadi CEO Sawit', 'Putra', 'Fiksi', '2026', 'CeritaFiksi'),
(15, 'BK013', 'Pemrograman Python Dasar', 'Budi Raharjo', 'Teknologi', '2023', 'Informatika'),
(16, 'BK014', 'Belajar Laravel', 'Rosa A.S.', 'Teknologi', '2024', 'Informatika'),
(17, 'BK015', 'Pendidikan Pancasila', 'Yudi Latif', 'Pendidikan', '2022', 'Kompas'),
(18, 'BK016', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Novel', '2019', 'Lentera Dipantara'),
(19, 'BK017', 'Matematika Dasar', 'Dedi Supriyadi', 'Pendidikan', '2021', 'Erlangga'),
(20, 'BK018', 'Pemrograman Java', 'Abdul Kadir', 'Teknologi', '2023', 'Andi'),
(21, 'BK019', 'Basis Data MySQL', 'Kurniawan', 'Database', '2024', 'Elex Media'),
(22, 'BK020', 'Algoritma Pemrograman Lanjutan', 'Budi Raharjo', 'Teknologi', '2025', 'Informatika'),
(23, 'BK0076', 'Petualangan Fantasi Misteri', 'Alexa', 'Fiksi', '2022', 'Alexa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
