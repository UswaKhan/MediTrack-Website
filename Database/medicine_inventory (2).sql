-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 05:22 PM
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
-- Database: `medicine_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `page_accessed` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_logs`
--

INSERT INTO `access_logs` (`id`, `username`, `role`, `ip_address`, `page_accessed`, `timestamp`) VALUES
(1, 'Not logged in', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:18:12'),
(2, 'Not logged in', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:19:01'),
(3, 'Not logged in', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:22:07'),
(4, 'Admin2', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:27:12'),
(5, 'Admin2', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:28:35'),
(6, 'Admin2', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:28:44'),
(7, 'Admin2', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:29:37'),
(8, 'Admin2', 'cashier', '::1', '/IS/test_access_control.php', '2025-05-11 13:33:07'),
(9, 'Admin2', 'cashier', '::1', '/IS/add_user.php', '2025-05-11 14:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') DEFAULT 'admin',
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `role`, `profile_picture`, `email`, `phone`) VALUES
(17, 'Admin2', '$2y$10$8z4780TPNGu99DVskGH0OufuX4nZx4BBCLPBoKgTOYVchXIDLKlbe', 'admin', 'uploads/68207fcb7f27d_person2.avif', 'admin2@gmail.com', '12345678901'),
(18, 'Admin3', '$2y$10$UWLILOUU2BH83bDkSIYknuM7NI0A4rjhbki93Z32yQA7lvjIYdaFy', 'admin', 'uploads/68208012630ee_person3.avif', 'admin3@gmail.com', '12345678901'),
(23, 'Admin1', '$2y$10$xvNDF8Iy/BhPMAWaK/kjhOdU6luKiBcga6yiSqnMGh0G/.TH1Z1Uq', 'admin', 'uploads/68209cf5262b9_person1.jpg', 'admin1@gmail.com', '11112222333'),
(24, 'Cashier1', '$2y$10$wwM0FIVxqfflBi5mnCu0PuXZnmIHKMexiZisVJMOMJaI7lxTJ46Wi', 'cashier', 'uploads/68209e1cd3c02_person1.jpg', 'cashier1@gmail.com', '11112222333'),
(25, 'Cashier2', '$2y$10$to0DSTbGTQ0ZmC4nqOMVIes0Lofil8vG/qQoBVuU8bkz/0X6lbmRe', 'cashier', 'uploads/68209e72c15d6_person2.avif', 'cashier2@gmail.com', '11112222333'),
(27, 'Cashier3', '$2y$10$yjN4ac3mezPWMs6k2EeND.dsKdjEy4xOzRd8vOibt7vaUJcAO8Lpi', 'cashier', NULL, 'cashier3@gmail.com', '12345678900'),
(42, 'bcdbdbhbhc', '$2y$10$seiiGtUmey.SOhWuDt4dlejlaVKrNz95ceEp.VNVP3nzydfAXhGyO', 'admin', NULL, 'cndnc@dnh.com', '12345667889');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL,
  `date_of_registration` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `created_at`, `email`, `date_of_registration`) VALUES
(2, 'Summera Faraz', '03334088896', '2025-04-26 11:16:24', 'uswalohani386@gmail.com', '2025-04-26'),
(5, 'Ezza', '12345678901', '2025-04-27 10:57:24', 'ezza@gmail.com', '2025-04-27'),
(6, 'Mamoor', '0331497718', '2025-04-29 04:11:52', 'mamoor@gmail.com', '2025-04-29'),
(7, 'Adnan', '12345432123', '2025-04-30 11:40:32', 'adnan@gmail.com', '2025-04-30'),
(8, 'Ezza', '12345654321', '2025-04-30 11:41:29', 'ezza@gmail.com', '2025-04-30'),
(9, 'Amna', '03334088898', '2025-05-11 13:10:37', 'amna@gmail.com', '2025-05-11'),
(14, 'Uswa Khan', '15473783899', '2025-05-15 14:12:21', 'uswa@gmail.com', '2025-05-15');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `time`, `success`) VALUES
(165, 'Cashier1', '2025-05-15 20:07:03', 1),
(166, 'Cashier1', '2025-05-15 20:12:07', 1),
(167, 'Admin1', '2025-05-15 20:13:18', 1),
(168, 'Admin1', '2025-05-15 20:19:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `quantity`, `expiry_date`, `price`) VALUES
(16, 'Rigix', 'Anti Allergy', 18, '2025-04-29', 300.00),
(21, 'Kestine', 'Anti Allergy', 5, '2025-05-01', 260.00),
(22, 'Brufin', 'Antibiotic', 15, '2025-04-26', 200.00),
(24, 'Sunny D', 'Vitamin', 14, '2025-05-31', 500.00),
(36, 'Panadol', 'Pain killer', 20, '2025-05-31', 200.00),
(37, 'Paracetamol ', 'Pain Killer', 20, '2025-05-30', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `quantity_sold` int(11) DEFAULT NULL,
  `sold_by` varchar(50) DEFAULT NULL,
  `sold_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `medicine_id`, `quantity_sold`, `sold_by`, `sold_at`, `customer_id`, `total_price`, `sale_date`) VALUES
(1, 1, 1, 'Faiza', '2025-04-23 16:04:26', NULL, NULL, '2025-04-23 21:30:58'),
(2, 1, 5, 'Faiza', '2025-04-23 16:21:55', 1, NULL, '2025-04-23 21:30:58'),
(3, 1, 1, 'Faiza', '2025-04-23 16:28:07', 1, 0.00, '2025-04-23 21:30:58'),
(4, 1, 1, 'Faiza', '2025-04-23 16:29:49', 1, 0.00, '2025-04-23 21:30:58'),
(5, 1, 1, 'Faiza', '2025-04-23 16:30:06', 1, 0.00, '2025-04-23 21:30:58'),
(6, 2, 1, 'Faiza', '2025-04-23 16:35:02', 1, 0.00, '2025-04-23 21:35:02'),
(7, 9, 1, 'Amna', '2025-04-26 11:16:24', 2, 700.00, '2025-04-26 16:16:24'),
(8, 9, 1, 'Amna', '2025-04-26 11:26:04', 2, 700.00, '2025-04-26 16:26:04'),
(9, 9, 1, 'Amna', '2025-04-26 11:27:19', 2, 700.00, '2025-04-26 16:27:19'),
(10, 9, 1, 'Amna', '2025-04-26 12:08:19', 2, 700.00, '2025-04-26 17:08:19'),
(11, 4, 2, 'Amna', '2025-04-26 12:41:09', 2, 0.00, '2025-04-26 17:41:09'),
(12, 10, 2, 'Amna', '2025-04-26 12:41:09', 2, 246.00, '2025-04-26 17:41:09'),
(13, 9, 2, 'Amna', '2025-04-26 12:41:09', 2, 1400.00, '2025-04-26 17:41:09'),
(14, 3, 2, 'Amna', '2025-04-26 12:44:47', 2, 0.00, '2025-04-26 17:44:47'),
(15, 9, 2, 'Amna', '2025-04-26 12:44:47', 2, 1400.00, '2025-04-26 17:44:47'),
(16, 3, 2, 'Amna', '2025-04-26 12:59:59', 2, 0.00, '2025-04-26 17:59:59'),
(17, 9, 1, 'Amna', '2025-04-26 12:59:59', 2, 700.00, '2025-04-26 17:59:59'),
(18, 3, 2, 'Amna', '2025-04-26 13:00:23', 2, 0.00, '2025-04-26 18:00:23'),
(19, 9, 23, 'Amna', '2025-04-26 13:00:23', 2, 16100.00, '2025-04-26 18:00:23'),
(20, 9, 2, 'Amna', '2025-04-26 13:03:36', 2, 1400.00, '2025-04-26 18:03:36'),
(21, 10, 1, 'Amna', '2025-04-26 13:03:36', 2, 123.00, '2025-04-26 18:03:36'),
(22, 9, 2, 'Amna', '2025-04-26 13:08:30', 2, 1400.00, '2025-04-26 18:08:30'),
(23, 10, 1, 'Amna', '2025-04-26 13:08:30', 2, 123.00, '2025-04-26 18:08:30'),
(24, 9, 1, 'Amna', '2025-04-26 13:09:26', 1, 700.00, '2025-04-26 18:09:26'),
(25, 10, 1, 'Amna', '2025-04-26 13:09:26', 1, 123.00, '2025-04-26 18:09:26'),
(26, 3, 2, 'Amna', '2025-04-26 13:37:34', 3, 0.00, '2025-04-26 18:37:34'),
(27, 11, 1, 'Amna', '2025-04-26 15:13:07', 4, 250.00, '2025-04-26 20:13:07'),
(28, 14, 1, 'Amna', '2025-04-26 15:13:07', 4, 250.00, '2025-04-26 20:13:07'),
(29, 14, 2, 'Cashier2', '2025-04-27 07:31:49', 4, 500.00, '2025-04-27 12:31:49'),
(30, 13, 1, 'Cashier2', '2025-04-27 07:31:49', 4, 350.00, '2025-04-27 12:31:49'),
(31, 12, 1, 'Cashier2', '2025-04-27 07:31:49', 4, 350.00, '2025-04-27 12:31:49'),
(32, 13, 1, 'Cashier2', '2025-04-27 07:45:04', 1, 350.00, '2025-04-27 12:45:04'),
(33, 13, 1, 'Cashier2', '2025-04-27 10:57:24', 5, 350.00, '2025-04-27 15:57:24'),
(34, 13, 1, 'Cashier2', '2025-04-27 10:59:56', 4, 350.00, '2025-04-27 15:59:56'),
(35, 14, 1, 'Cashier2', '2025-04-27 10:59:56', 4, 250.00, '2025-04-27 15:59:56'),
(36, 17, 2, 'Cashier2', '2025-04-27 10:59:56', 4, 460.00, '2025-04-27 15:59:56'),
(37, 12, 1, 'Cashier2', '2025-04-27 10:59:56', 4, 350.00, '2025-04-27 15:59:56'),
(38, 13, 1, 'Cashier2', '2025-04-27 11:00:57', 5, 350.00, '2025-04-27 16:00:57'),
(39, 13, 1, 'Cashier2', '2025-04-27 11:13:14', 5, 350.00, '2025-04-27 16:13:14'),
(40, 14, 1, 'Cashier1', '2025-04-29 04:11:52', 6, 250.00, '2025-04-29 09:11:52'),
(41, 21, 1, 'Cashier2', '2025-04-30 11:40:32', 7, 260.00, '2025-04-30 16:40:32'),
(42, 20, 1, 'Cashier2', '2025-04-30 11:41:29', 8, 200.00, '2025-04-30 16:41:29'),
(43, 24, 1, 'Cashier2', '2025-05-11 13:01:47', 1, 500.00, '2025-05-11 18:01:47'),
(44, 21, 1, 'Cashier2', '2025-05-11 13:01:47', 1, 260.00, '2025-05-11 18:01:47'),
(45, 24, 1, 'Cashier2', '2025-05-11 13:04:28', 1, 500.00, '2025-05-11 18:04:28'),
(46, 21, 1, 'Cashier2', '2025-05-11 13:04:28', 1, 260.00, '2025-05-11 18:04:28'),
(47, 22, 1, 'Cashier2', '2025-05-11 13:06:35', 1, 200.00, '2025-05-11 18:06:35'),
(48, 21, 2, 'Cashier2', '2025-05-11 13:06:35', 1, 520.00, '2025-05-11 18:06:35'),
(49, 21, 1, 'Cashier2', '2025-05-11 13:08:21', 1, 260.00, '2025-05-11 18:08:21'),
(50, 22, 2, 'Cashier2', '2025-05-11 13:08:21', 1, 400.00, '2025-05-11 18:08:21'),
(51, 21, 1, 'Cashier2', '2025-05-11 13:10:37', 9, 260.00, '2025-05-11 18:10:37'),
(52, 22, 2, 'Cashier2', '2025-05-11 13:10:37', 9, 400.00, '2025-05-11 18:10:37'),
(53, 21, 2, 'Cashier2', '2025-05-11 13:15:27', 2, 520.00, '2025-05-11 18:15:27'),
(54, 24, 1, 'Cashier2', '2025-05-11 13:15:27', 2, 500.00, '2025-05-11 18:15:27'),
(55, 21, 1, 'Cashier1', '2025-05-11 13:26:39', 2, 260.00, '2025-05-11 18:26:39'),
(56, 24, 2, 'Cashier1', '2025-05-11 13:26:39', 2, 1000.00, '2025-05-11 18:26:39'),
(57, 21, 1, 'Cashier1', '2025-05-11 13:28:16', 2, 260.00, '2025-05-11 18:28:16'),
(58, 21, 1, 'Cashier1', '2025-05-11 13:46:09', 10, 260.00, '2025-05-11 18:46:09'),
(59, 24, 1, 'Cashier1', '2025-05-11 13:46:09', 10, 500.00, '2025-05-11 18:46:09'),
(60, 21, 1, 'Cashier1', '2025-05-15 12:24:18', 11, 260.00, '2025-05-15 17:24:18'),
(61, 21, 1, 'Cashier1', '2025-05-15 12:32:23', 13, 260.00, '2025-05-15 17:32:23'),
(62, 21, 1, 'Cashier1', '2025-05-15 14:12:21', 14, 260.00, '2025-05-15 19:12:21'),
(63, 16, 2, 'Cashier1', '2025-05-15 14:12:21', 14, 600.00, '2025-05-15 19:12:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`timestamp`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`time`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
