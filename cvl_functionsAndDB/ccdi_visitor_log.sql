-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 03:53 PM
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
-- Database: `ccdi_visitor_log`
--

-- --------------------------------------------------------

--
-- Table structure for table `cvl_login_info`
--

CREATE TABLE `cvl_login_info` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cvl_login_info`
--

INSERT INTO `cvl_login_info` (`id`, `username`, `password`) VALUES
(3, 'admin', '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

-- --------------------------------------------------------

--
-- Table structure for table `cvl_visitor_info`
--

CREATE TABLE `cvl_visitor_info` (
  `id` int(11) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `purpose_of_visit` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cvl_visitor_info`
--

INSERT INTO `cvl_visitor_info` (`id`, `full_name`, `address`, `contact`, `school`, `purpose_of_visit`, `created_at`) VALUES
(8, 'John Villaluna', 'Sorsogon City, Sorsogon', '0956 677 7789', 'CCDI', 'exam', '2025-12-07 12:31:37'),
(18, 'Kyle Desamparo', 'Sorsogon City, Sorsogon', '0976 655 4432', 'SSU', 'inquiry', '2025-12-07 12:35:31'),
(19, 'Joe Guab', 'Sorsogon City, Sorsogon', '0946 677 7413', 'CCDI', 'exam', '2025-12-07 13:06:59'),
(21, 'Dylan Ray Gonzalgo', 'Sorsogon City, Sorsogon', '0945 577 7487', 'SSU', 'visit', '2025-12-07 14:21:40'),
(25, 'MJ Advincula', 'Sorsogon City, Sorsogon', '0948 877 7654', 'CCDI', 'exam', '2025-12-07 14:27:23'),
(26, 'Paolo Funelas', 'Sorsogon City, Sorsogon', '0956 874 4785', 'CCDI', 'exam', '2025-12-07 14:53:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cvl_login_info`
--
ALTER TABLE `cvl_login_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cvl_visitor_info`
--
ALTER TABLE `cvl_visitor_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cvl_login_info`
--
ALTER TABLE `cvl_login_info`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cvl_visitor_info`
--
ALTER TABLE `cvl_visitor_info`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
