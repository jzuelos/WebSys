-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 03:33 PM
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
-- Database: `magx`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(50) NOT NULL,
  `ad_un` varchar(255) NOT NULL,
  `ad_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `ad_un`, `ad_password`) VALUES
(1, 'lloyd', '$2y$10$gQhNCWQoywQmRYNOANCmoOWUevI74/pEY9/pvsdb73/yxDkIVoBdq');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer') NOT NULL,
  `c_fname` varchar(50) NOT NULL,
  `c_sname` varchar(50) DEFAULT NULL,
  `address` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `bday` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `username`, `password`, `role`, `c_fname`, `c_sname`, `address`, `email`, `bday`) VALUES
(1, 'lloyd', 'user', 'customer', 'john lloyd', NULL, 'Labo', 'jzuelos@gmail.com', '2024-12-03'),
(2, 'melanie', '$2y$10$uIYaG1eoPM4URTDyIzOt8u.7jV1/fUmfb60H6dsw.eTPWY2ghmKSO', 'customer', '', NULL, '', '', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(50) NOT NULL,
  `p_brand` varchar(200) NOT NULL,
  `p_name` varchar(200) NOT NULL,
  `p_desc` text NOT NULL,
  `p_price` int(50) NOT NULL,
  `p_active` enum('0','1') NOT NULL,
  `p_creation` date NOT NULL,
  `p_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `p_brand`, `p_name`, `p_desc`, `p_price`, `p_active`, `p_creation`, `p_image`) VALUES
(28, 'BomX', 'BomX Star Mags', 'A high-performance mag wheel designed for speed and durability, ideal for street and track racing motorcycles. Suitable for Indonesian Concept.', 3000, '1', '2024-12-12', 'uploads/675ad8f508086_BomX.jpg'),
(31, 'Asio', 'Asio Mags', 'Known for its sleek design and strength, this mag wheel is perfect for urban commuting and light off-road riding. Suitable for Big Bike Concept.', 2500, '1', '2024-12-12', 'uploads/675ae1bf8aa35_Asio.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
