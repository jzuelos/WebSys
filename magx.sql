-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 03:59 PM
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
  `phone` varchar(20) NOT NULL,
  `bday` date NOT NULL,
  `c_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `username`, `password`, `role`, `c_fname`, `c_sname`, `address`, `email`, `phone`, `bday`, `c_creation`) VALUES
(9, 'melanie', '$2y$10$2qTbkaSnjmwC85tk3qarX.gqkQrwCOJdDTG4BQjpEGThaumr2Bk26', 'customer', 'Melanie', 'Villacruel', 'Camarines Norte', 'cyzuz@yahoo.com', '09123456789', '2024-12-13', '2024-12-13');

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
(54, 'BomX', 'BomX Star Mags', 'A high-performance mag wheel designed for speed and durability, ideal for street and track racing motorcycles. Suitable for Indonesian Concept.', 2500, '1', '2024-12-16', 'uploads/67603da1b70d8_boomx.png'),
(55, 'Asio', 'Asio Mags', 'Known for its sleek design and strength, this mag wheel is perfect for urban commuting and light off-road riding. Suitable for Big Bike Concept.', 2400, '1', '2024-12-16', 'uploads/67603de07a36d_asio.png'),
(56, 'TRC', 'TRC CNC Mags', 'Designed for long-distance rides with a focus on comfort and style, the TRC mag wheel combines durability with a sleek, modern look. Suitable for Malaysian DNA.', 2600, '1', '2024-12-16', 'uploads/67603e071c2ef_trc.png'),
(57, 'RCB', 'RCB Sport Rim', 'A lightweight yet sturdy option, RCB mag wheels are ideal for both street racing and daily rides. Suitable for track and perfomance.', 2700, '1', '2024-12-16', 'uploads/67603e4f0ef33_RCB.png'),
(58, 'Mattaru', 'Mattaru CNC Mags', 'Built for style and stability, Muttaru wheels are perfect for custom cruiser builds and casual rides. Suitable Thailand concept and Malaysian DNA.', 2900, '1', '2024-12-16', 'uploads/67603f609b185_Mattaru.png');

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
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
