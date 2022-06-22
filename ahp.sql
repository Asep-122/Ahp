-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2022 at 07:04 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ahp`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_masteritem`
--

CREATE TABLE `tbl_masteritem` (
  `kode_item` varchar(6) NOT NULL,
  `jenis_item` varchar(100) NOT NULL,
  `nama_item` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_masteritem`
--

INSERT INTO `tbl_masteritem` (`kode_item`, `jenis_item`, `nama_item`) VALUES
('BRG001', 'Food', 'telur'),
('BRG002', 'Furniture', 'Kursi'),
('BRG003', 'Food', 'Minya Goreng'),
('BRG004', 'Furniture', 'Meja');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mastersupplier`
--

CREATE TABLE `tbl_mastersupplier` (
  `kode_supplier` varchar(10) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_mastersupplier`
--

INSERT INTO `tbl_mastersupplier` (`kode_supplier`, `nama_supplier`) VALUES
('S001', 'PT Adicipta'),
('S002', 'PT Kirana'),
('S003', 'PT Delta'),
('S004', 'PT Simphony');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi`
--

CREATE TABLE `tbl_transaksi` (
  `kode_supplier` varchar(10) NOT NULL,
  `kode_item` varchar(6) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` float NOT NULL,
  `tgl` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_transaksi`
--

INSERT INTO `tbl_transaksi` (`kode_supplier`, `kode_item`, `qty`, `harga`, `tgl`) VALUES
('S001', 'BRG002', 1, 120000, '2022-06-22 00:00:00'),
('S001', 'BRG004', 1, 600000, '2022-06-22 00:00:00'),
('S002', 'BRG002', 1, 140000, '2022-06-22 00:00:00'),
('S002', 'BRG004', 1, 550000, '2022-06-22 00:00:00'),
('S003', 'BRG002', 1, 95000, '2022-06-22 00:00:00'),
('S003', 'BRG004', 1, 70000, '2022-06-22 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_masteritem`
--
ALTER TABLE `tbl_masteritem`
  ADD PRIMARY KEY (`kode_item`);

--
-- Indexes for table `tbl_mastersupplier`
--
ALTER TABLE `tbl_mastersupplier`
  ADD PRIMARY KEY (`kode_supplier`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
