-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2019 at 08:06 PM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kioskpos`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `description` varchar(32) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `description`, `price`) VALUES
(1, 'SPARERIBS', 99),
(2, 'BACKRIBS', 189),
(3, 'BLUE MARLIN', 1.2),
(4, 'PORK BBQ', 25),
(5, 'LEMONADE 12oz', 35),
(6, 'LEMONADE 16oz', 45),
(7, 'ICED TEA 12oz', 35),
(8, 'ICED TEA 16oz', 45),
(9, 'BOTTLED WATTER', 25),
(10, 'PORK BBQ MEAL', 109),
(11, 'RICE', 25);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `total` float NOT NULL,
  `cash_rendered` float NOT NULL,
  `cash_change` float NOT NULL,
  `void` int(1) NOT NULL DEFAULT 0,
  `table_number` int(11) NOT NULL,
  `complete` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `datetime`, `total`, `cash_rendered`, `cash_change`, `void`, `table_number`, `complete`) VALUES
(1, '0000-00-00 00:00:00', 4, 5, 0, 0, 12, 0),
(2, '2019-10-25 17:49:41', 4, 5, 0, 0, 12, 0),
(3, '2019-10-25 17:51:46', 2, 5, 3, 0, 12, 0),
(4, '2019-10-25 17:56:18', 2.4, 5, 2.6, 0, 12, 0),
(5, '2019-10-25 17:56:41', 2.4, 5, 2.6, 0, 12, 0),
(6, '2019-10-25 17:58:22', 449.2, 500, 50.8, 0, 16, 0),
(7, '2019-10-25 17:59:07', 235.2, 300, 64.8, 0, 12, 0),
(8, '2019-10-25 17:59:43', 225.2, 300, 74.8, 0, 15, 0),
(9, '2019-10-25 18:01:15', 490.2, 500, 9.8, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_detail`
--

CREATE TABLE `transaction_detail` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `serve` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_detail`
--

INSERT INTO `transaction_detail` (`id`, `transaction_id`, `product_id`, `quantity`, `serve`) VALUES
(1, 5, 3, 2, 0),
(2, 6, 6, 1, 0),
(3, 6, 9, 1, 0),
(4, 6, 3, 251, 0),
(5, 6, 2, 2, 0),
(6, 7, 8, 1, 0),
(7, 7, 2, 1, 0),
(8, 7, 3, 1, 0),
(9, 8, 5, 1, 0),
(10, 8, 3, 1, 0),
(11, 8, 2, 1, 1),
(12, 9, 3, 251, 0),
(13, 9, 2, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
