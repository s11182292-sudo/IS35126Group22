-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 10:19 AM
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
-- Database: `tourism_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 2, 'Login successful', '2026-06-03 03:35:25'),
(2, 1, 'Login successful', '2026-06-03 03:36:23'),
(3, 3, 'Login successful', '2026-06-03 03:37:31'),
(4, 3, 'Login successful', '2026-06-03 03:38:09'),
(5, 3, 'Admin Margaret Vodo added package \'Sunset Island Escape\' (Mamanuca Islands, Fiji)', '2026-06-03 03:45:14'),
(6, 1, 'Login successful', '2026-06-03 03:46:29'),
(7, 2, 'Login successful', '2026-06-03 03:46:49'),
(8, 2, 'Booked package ID 1', '2026-06-03 03:47:20'),
(9, 2, 'User logged out', '2026-06-03 04:10:44'),
(10, 4, 'Successful login', '2026-06-03 04:24:58'),
(11, 4, 'Booked package ID 1', '2026-06-03 04:25:50'),
(12, 4, 'User logged out', '2026-06-03 04:25:54'),
(13, 1, 'Successful login', '2026-06-03 04:26:06'),
(14, 1, 'User logged out', '2026-06-03 04:28:32'),
(15, 1, 'User logged out', '2026-06-03 05:45:01'),
(16, 1, 'OTP login initiated', '2026-06-03 05:54:56'),
(17, 1, 'OTP login initiated', '2026-06-03 06:01:44'),
(18, 1, 'OTP login initiated', '2026-06-03 06:04:24'),
(19, 1, 'User logged out', '2026-06-03 06:07:21'),
(20, 1, 'OTP login initiated', '2026-06-03 06:12:12'),
(21, 1, 'User logged out', '2026-06-03 06:45:32'),
(22, 1, 'OTP login initiated', '2026-06-03 06:46:21');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `persons` int(11) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `travel_date`, `persons`, `status`) VALUES
(1, 2, 1, '2026-06-06', 2, 'Pending'),
(2, 4, 1, '2026-06-19', 5, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `user_id`, `otp_code`, `expires_at`, `verified`) VALUES
(1, 1, '455144', '2026-06-03 08:04:49', 0),
(2, 1, '146798', '2026-06-03 08:11:38', 0),
(3, 1, '955984', '2026-06-03 18:14:18', 0);

-- --------------------------------------------------------

--
-- Table structure for table `travel_packages`
--

CREATE TABLE `travel_packages` (
  `id` int(11) NOT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_packages`
--

INSERT INTO `travel_packages` (`id`, `package_name`, `description`, `destination`, `price`) VALUES
(1, 'Sunset Island Escape', 'Enjoy a 3-day luxury island getaway in the beautiful Mamanuca Islands. This package includes beachfront accommodation, daily breakfast, snorkeling tours, sunset cruise, and guided island exploration. Perfect for couples or small groups seeking relaxation and adventure.', 'Mamanuca Islands, Fiji', 899.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','agent','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`, `failed_attempts`, `lock_until`) VALUES
(1, 'Maria Yalayalatabua', 'm.yalayalatabua@gmail.com', '$2y$10$N8icZcBudcl73JEqEkI30.6VatIE78UYYJmprUPsOoZbF6WeR2.YK', 'admin', '2026-06-03 03:28:33', 0, NULL),
(2, 'John Smith', 'John@gmail.com', '$2y$10$ZXCfMzqTgFDVTEdPVMvAtOjnMuyJ0sMg5619DzpvbmBeMYlRiw2a6', 'customer', '2026-06-03 03:33:34', 0, '2026-06-03 06:38:12'),
(3, 'Margaret Vodo', 'Margie@gmail.com', '$2y$10$y.FrJzwj5Ix6eVRfqPCuDuPy2hIuDDmb3Y1t8jQTiTrZM7VMmWlVa', 'admin', '2026-06-03 03:37:20', 0, NULL),
(4, 'Salaseini Waqa', 'Sala@gmail.com', '$2y$10$xExldkL3J.Ug0Hu7k2AAYuEH3.fne.jNo.hbyTN05wmZPHwjL/dqe', 'customer', '2026-06-03 04:24:44', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `travel_packages`
--
ALTER TABLE `travel_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `travel_packages`
--
ALTER TABLE `travel_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
