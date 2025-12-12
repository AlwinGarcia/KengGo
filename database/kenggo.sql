-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 10, 2025 at 03:04 PM
-- Server version: 11.5.2-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kenggo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','manager','staff') DEFAULT 'manager',
  `status` enum('active','pending','disabled') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `admin_code` (`admin_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_code`, `name`, `email`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'ADM-1001', 'Super Admin', 'admin@kenggo.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9iu1IJ5AZtQ.f1q5lIYFSi', 'superadmin', 'active', '2025-12-09 13:32:17', '2025-12-09 13:32:17', '2025-12-10 11:10:18'),
(2, 'ADM-2001', 'Ops Manager', 'manager@kenggo.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9iu1IJ5AZtQ.f1q5lIYFSi', 'manager', 'active', NULL, '2025-12-09 13:32:17', '2025-12-10 11:10:18'),
(3, 'ADM-3001', 'Support Staff', 'staff@kenggo.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9iu1IJ5AZtQ.f1q5lIYFSi', 'staff', 'pending', NULL, '2025-12-09 13:32:17', '2025-12-10 11:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','alert') DEFAULT 'info',
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `admin_id`, `title`, `message`, `type`, `status`, `created_at`, `read_at`) VALUES
(1, NULL, 'System Update', 'System maintenance scheduled for tonight at 2 AM', 'info', 'unread', '2025-12-10 01:00:00', NULL),
(2, NULL, 'New Trip Request', 'A new trip has been added to the system', 'success', 'unread', '2025-12-10 01:05:00', NULL),
(3, NULL, 'Driver Assignment', 'Shuttle 01 needs driver assignment for tomorrow', 'warning', 'unread', '2025-12-10 01:10:00', NULL),
(4, NULL, 'Payment Alert', 'Payment received for Shuttle 02', 'success', 'read', '2025-12-10 01:15:00', '2025-12-10 01:30:00'),
(5, NULL, 'Booking Update', '5 new bookings today', 'info', 'read', '2025-12-10 01:20:00', '2025-12-10 01:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_requests`
--

DROP TABLE IF EXISTS `admin_requests`;
CREATE TABLE IF NOT EXISTS `admin_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_code` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role_requested` enum('manager','staff') DEFAULT 'staff',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_code` (`request_code`),
  KEY `reviewed_by` (`reviewed_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_requests`
--

INSERT INTO `admin_requests` (`id`, `request_code`, `name`, `email`, `role_requested`, `status`, `reviewed_by`, `created_at`, `reviewed_at`) VALUES
(1, 'REQ-9001', 'Gideon Admin', 'gideon@example.com', 'manager', 'approved', 1, '2025-12-09 13:32:17', '2025-12-09 13:42:17'),
(2, 'REQ-9002', 'Backup Admin', 'backup@example.com', 'staff', 'pending', NULL, '2025-12-09 13:32:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `passenger_id` int(11) NOT NULL,
  `shuttle_id` int(11) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `status` enum('booked','cancelled','completed') DEFAULT 'booked',
  `booked_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_booking` (`passenger_id`,`shuttle_id`),
  KEY `passenger_id` (`passenger_id`),
  KEY `shuttle_id` (`shuttle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `passenger_id`, `shuttle_id`, `seat_number`, `status`, `booked_at`) VALUES
(1, 1, 1, 5, 'booked', '2025-12-09 13:32:17'),
(2, 2, 1, 6, 'booked', '2025-12-09 13:32:17'),
(3, 3, 2, 3, 'completed', '2025-12-09 13:32:17'),
(28, 1, 7, 6, 'booked', '2025-12-10 14:02:28'),
(29, 1, 9, 6, 'booked', '2025-12-10 14:34:00'),
(30, 1, 2, 6, 'booked', '2025-12-10 14:34:05'),
(31, 1, 12, 2, 'booked', '2025-12-10 14:34:09');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `license_expiry` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `experience_years` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_trips` int(11) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `driver_code` (`driver_code`),
  UNIQUE KEY `license_number` (`license_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `driver_code`, `name`, `email`, `password`, `license_number`, `license_expiry`, `phone`, `vehicle_number`, `plate_number`, `avatar_url`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `status`, `experience_years`, `rating`, `total_trips`, `last_login`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'DRV-1001', 'Driver A', 'driverA@example.com', '$2y$10$4.h1n0Gj60tOOpi52nU1d./4GXPqD/s0SSxFl3teRl3/ZIYsmtg62', 'DL-2025-001', '2026-06-15', '+63 912 345 6789', 'SLU-001', 'ABC-1234', 'https://cdn.kenggo.local/img/driver-a.jpg', 'Purok 3, Bakakeng Norte, Baguio City', 'Ana Santos', '+63 917 555 1111', 'active', 5, 4.80, 150, '2025-12-09 13:32:17', 'Prefers morning shifts; rated highly for punctuality.', '2025-12-09 13:32:17', '2025-12-10 11:10:18'),
(2, 'DRV-1002', 'Driver B', 'driverB@example.com', '$2y$10$4.h1n0Gj60tOOpi52nU1d./4GXPqD/s0SSxFl3teRl3/ZIYsmtg62', 'DL-2025-002', '2026-08-20', '+63 912 345 6790', 'SLU-002', 'XYZ-5678', 'https://cdn.kenggo.local/img/driver-b.jpg', 'Sto. Tomas Proper, Baguio City', 'Marco Reyes', '+63 917 555 2222', 'active', 3, 4.65, 98, '2025-12-08 16:04:00', 'Great with evening commuters; bilingual (Ilocano/English).', '2025-12-09 13:32:17', '2025-12-10 11:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

DROP TABLE IF EXISTS `passengers`;
CREATE TABLE IF NOT EXISTS `passengers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Juan Dela Cruz', 'juan@example.com', 'pass', '2025-12-09 13:32:17'),
(2, 'Maria Santos', 'maria@example.com', 'pass', '2025-12-09 13:32:17'),
(3, 'Alvin Tolentino', 'alvin@example.com', 'pass', '2025-12-09 13:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `shuttles`
--

DROP TABLE IF EXISTS `shuttles`;
CREATE TABLE IF NOT EXISTS `shuttles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shuttle_number` varchar(100) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `destination` varchar(100) DEFAULT NULL,
  `route` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `trip_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  CONSTRAINT `fk_shuttles_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shuttles`
--

INSERT INTO `shuttles` (`id`, `shuttle_number`, `plate_number`, `origin`, `destination`, `route`, `departure_time`, `arrival_time`, `capacity`, `price`, `status`, `trip_date`, `notes`, `driver_id`, `created_at`, `updated_at`) VALUES
(1, 'Shuttle 01', 'SLU-001', 'SLU Maryheights', 'Baguio Town', 'Bakakeng to SLU Maryheights', '07:30:00', '08:30:00', 18, 50.00, 'active', '2025-12-10', 'Morning route', 1, '2025-12-10 11:10:18', '2025-12-10 13:51:23'),
(2, 'Shuttle 02', 'SLU-002', 'Baguio Town', 'SLU Maryheights', 'SLU Maryheights to Bakakeng', '17:30:00', '18:30:00', 18, 50.00, 'active', '2025-12-10', 'Afternoon route', 2, '2025-12-10 11:10:18', '2025-12-10 13:51:23'),
(3, 'Shuttle 03', 'SLU-003', NULL, NULL, 'Quirino Hill to Burnham Park', '06:45:00', '07:25:00', 16, 60.00, 'active', '2025-12-11', 'Early office loop', 1, '2025-12-10 11:10:18', '2025-12-10 11:10:18'),
(4, 'Shuttle 07', 'SLU-007', NULL, NULL, 'Burnham Park to Quirino Hill', '19:15:00', '19:55:00', 16, 60.00, 'active', '2025-12-11', 'Evening return loop', 2, '2025-12-10 11:10:18', '2025-12-10 11:10:18'),
(5, 'Shuttle 12', 'SLU-012', NULL, NULL, 'Camp 7 to Session Road', '08:10:00', '08:55:00', 20, 70.00, 'pending', '2025-12-12', 'Needs dispatch approval', NULL, '2025-12-10 11:10:18', '2025-12-10 11:10:18'),
(6, 'Shuttle 15', 'SLU-015', NULL, NULL, 'Session Road to Camp 7', '21:00:00', '21:40:00', 20, 70.00, 'inactive', '2025-12-12', 'Temporarily offline for maintenance', NULL, '2025-12-10 11:10:18', '2025-12-10 11:10:18'),
(7, 'Shuttle 01', 'SLU-001', NULL, NULL, 'SLU Maryheights to Baguio Town', '06:30:00', '07:10:00', 18, 50.00, 'active', '2025-12-11', 'Early morning trip', 1, '2025-12-10 13:57:11', '2025-12-10 13:57:11'),
(8, 'Shuttle 02', 'SLU-002', NULL, NULL, 'SLU Maryheights to Baguio Town', '07:30:00', '08:10:00', 18, 50.00, 'active', '2025-12-11', 'Morning trip', 2, '2025-12-10 13:57:11', '2025-12-10 13:57:11'),
(9, 'Shuttle 03', 'SLU-003', NULL, NULL, 'SLU Maryheights to Baguio Town', '08:30:00', '09:10:00', 18, 50.00, 'active', '2025-12-11', 'Mid-morning trip', 1, '2025-12-10 13:57:11', '2025-12-10 13:57:11'),
(10, 'Shuttle 04', 'SLU-004', NULL, NULL, 'Baguio Town to SLU Maryheights', '17:00:00', '17:40:00', 18, 50.00, 'active', '2025-12-11', 'Afternoon return', 2, '2025-12-10 13:57:11', '2025-12-10 13:57:11'),
(11, 'Shuttle 05', 'SLU-005', NULL, NULL, 'Baguio Town to SLU Maryheights', '18:00:00', '18:40:00', 18, 50.00, 'active', '2025-12-11', 'Evening return', 1, '2025-12-10 13:57:11', '2025-12-10 13:57:11'),
(12, 'Shuttle 06', 'SLU-006', NULL, NULL, 'Baguio Town to SLU Maryheights', '19:00:00', '19:40:00', 18, 50.00, 'active', '2025-12-11', 'Late evening return', 2, '2025-12-10 13:57:11', '2025-12-10 13:57:11');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
CREATE TABLE IF NOT EXISTS `trips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shuttle_number` varchar(50) NOT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `seats_available` int(11) NOT NULL DEFAULT 20,
  `from_address` varchar(255) NOT NULL,
  `to_address` varchar(255) NOT NULL,
  `trip_date` date NOT NULL,
  `depart_time` time NOT NULL,
  `arrive_time` time DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive','pending','completed','cancelled') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `trip_date` (`trip_date`),
  KEY `status` (`status`),
  CONSTRAINT `fk_trips_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `shuttle_number`, `driver_name`, `driver_id`, `plate_number`, `seats_available`, `from_address`, `to_address`, `trip_date`, `depart_time`, `arrive_time`, `price`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Shuttle 01', 'Gideon Santos', 1, 'ABC-1234', 20, '72-74 Oxford St.', '20 Grosvenor Sq.', '2025-12-10', '07:30:00', '08:15:00', 100.00, 'active', 'Regular morning route', '2025-12-10 11:10:18', NULL),
(2, 'Shuttle 23', 'Maria Cruz', 2, 'XYZ-5678', 18, 'Great Portland St.', 'Baker Street', '2025-12-10', '08:00:00', '08:45:00', 100.00, 'active', NULL, '2025-12-10 11:10:18', NULL),
(3, 'Shuttle 44', 'Juan Dela Cruz', 1, 'LMN-9012', 20, '103 Seymour Pl.', 'London NW1 5BR', '2025-12-10', '09:00:00', '09:45:00', 100.00, 'active', NULL, '2025-12-10 11:10:18', NULL),
(4, 'Shuttle 67', 'Pedro Santos', 2, 'DEF-3456', 20, '377 Durnsford Rd.', '136 Buckhold Rd.', '2025-12-10', '10:00:00', '10:45:00', 100.00, 'active', NULL, '2025-12-10 11:10:18', NULL),
(5, 'Shuttle 01', 'Driver A', 1, 'SLU-001', 18, 'SLU Maryheights, Bakakeng', 'Baguio Town Proper', '2025-12-11', '06:30:00', '07:10:00', 50.00, 'active', 'Early morning trip', '2025-12-10 13:57:16', NULL),
(6, 'Shuttle 02', 'Driver B', 2, 'SLU-002', 18, 'SLU Maryheights, Bakakeng', 'Baguio Town Proper', '2025-12-11', '07:30:00', '08:10:00', 50.00, 'active', 'Morning trip', '2025-12-10 13:57:16', NULL),
(7, 'Shuttle 03', 'Driver A', 1, 'SLU-003', 18, 'SLU Maryheights, Bakakeng', 'Baguio Town Proper', '2025-12-11', '08:30:00', '09:10:00', 50.00, 'active', 'Mid-morning trip', '2025-12-10 13:57:16', NULL),
(8, 'Shuttle 04', 'Driver B', 2, 'SLU-004', 18, 'Baguio Town Proper', 'SLU Maryheights, Bakakeng', '2025-12-11', '17:00:00', '17:40:00', 50.00, 'active', 'Afternoon return', '2025-12-10 13:57:16', NULL),
(9, 'Shuttle 05', 'Driver A', 1, 'SLU-005', 18, 'Baguio Town Proper', 'SLU Maryheights, Bakakeng', '2025-12-11', '18:00:00', '18:40:00', 50.00, 'active', 'Evening return', '2025-12-10 13:57:16', NULL),
(10, 'Shuttle 06', 'Driver B', 2, 'SLU-006', 18, 'Baguio Town Proper', 'SLU Maryheights, Bakakeng', '2025-12-11', '19:00:00', '19:40:00', 50.00, 'active', 'Late evening return', '2025-12-10 13:57:16', NULL),
(11, 'Shuttle 07', 'Driver A', 1, 'SLU-007', 20, 'SLU Maryheights, Bakakeng', 'Session Road, Baguio', '2025-12-12', '07:00:00', '07:45:00', 55.00, 'active', 'Early shopping route', '2025-12-11 14:00:00', NULL),
(12, 'Shuttle 08', 'Driver B', 2, 'SLU-008', 20, 'SLU Maryheights, Bakakeng', 'SM Baguio', '2025-12-12', '08:00:00', '08:40:00', 55.00, 'active', 'Mall route morning', '2025-12-11 14:00:00', NULL),
(13, 'Shuttle 09', 'Driver A', 1, 'SLU-009', 18, 'Burnham Park', 'SLU Maryheights, Bakakeng', '2025-12-10', '16:00:00', '16:40:00', 50.00, 'completed', 'Completed yesterday', '2025-12-09 10:00:00', NULL),
(14, 'Shuttle 10', 'Driver B', 2, 'SLU-010', 18, 'Camp John Hay', 'SLU Maryheights, Bakakeng', '2025-12-09', '14:00:00', '14:45:00', 60.00, 'completed', 'Completed two days ago', '2025-12-08 10:00:00', NULL),
(15, 'Shuttle 11', 'Driver A', 1, 'SLU-011', 20, 'SLU Maryheights, Bakakeng', 'Wright Park', '2025-12-13', '09:00:00', '09:40:00', 50.00, 'pending', 'Scheduled for tomorrow', '2025-12-11 15:00:00', NULL),
(16, 'Shuttle 12', 'Driver B', 2, 'SLU-012', 20, 'SLU Maryheights, Bakakeng', 'Mines View Park', '2025-12-13', '10:00:00', '10:50:00', 65.00, 'pending', 'Tourist route tomorrow', '2025-12-11 15:00:00', NULL),
(17, 'Shuttle 13', 'Driver A', 1, 'SLU-013', 18, 'Baguio Town Proper', 'SLU Maryheights, Bakakeng', '2025-12-08', '17:00:00', '17:40:00', 50.00, 'cancelled', 'Cancelled due to weather', '2025-12-07 10:00:00', NULL),
(18, 'Shuttle 14', 'Driver B', 2, 'SLU-014', 18, 'Session Road, Baguio', 'SLU Maryheights, Bakakeng', '2025-12-07', '18:00:00', '18:45:00', 55.00, 'cancelled', 'Driver unavailable', '2025-12-06 10:00:00', NULL),
(19, 'Shuttle 15', 'Driver A', 1, 'SLU-015', 20, 'Lower Rock Quarry', 'SLU Maryheights, Bakakeng', '2025-12-09', '15:00:00', '15:45:00', 60.00, 'completed', 'Completed successfully', '2025-12-08 08:00:00', NULL),
(20, 'Shuttle 16', 'Driver B', 2, 'SLU-016', 20, 'Teacher Camp', 'SLU Maryheights, Bakakeng', '2025-12-08', '16:00:00', '16:50:00', 65.00, 'completed', 'Good trip', '2025-12-07 08:00:00', NULL),
(21, 'Shuttle 17', 'Driver A', 1, 'SLU-017', 18, 'SLU Maryheights, Bakakeng', 'Botanical Garden', '2025-12-14', '11:00:00', '11:45:00', 55.00, 'pending', 'Scheduled trip', '2025-12-11 16:00:00', NULL),
(22, 'Shuttle 18', 'Driver B', 2, 'SLU-018', 18, 'SLU Maryheights, Bakakeng', 'Bell Church', '2025-12-14', '12:00:00', '12:50:00', 60.00, 'pending', 'Scheduled trip', '2025-12-11 16:00:00', NULL),
(23, 'Shuttle 19', 'Driver A', 1, 'SLU-019', 20, 'Diplomat Hotel', 'SLU Maryheights, Bakakeng', '2025-12-06', '19:00:00', '19:50:00', 70.00, 'cancelled', 'Passenger no-show', '2025-12-05 10:00:00', NULL),
(24, 'Shuttle 20', 'Driver B', 2, 'SLU-020', 20, 'Tam-awan Village', 'SLU Maryheights, Bakakeng', '2025-12-05', '20:00:00', '20:50:00', 75.00, 'cancelled', 'Route not available', '2025-12-04 10:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `driver_notifications`
--

DROP TABLE IF EXISTS `driver_notifications`;
CREATE TABLE IF NOT EXISTS `driver_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','alert') DEFAULT 'info',
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `status` (`status`),
  KEY `driver_status_created` (`driver_id`,`status`,`created_at`),
  CONSTRAINT `fk_driver_notifications_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `driver_notifications` (`id`, `driver_id`, `title`, `message`, `type`, `status`, `created_at`, `read_at`) VALUES
(1, 1, 'New Assignment', 'You have been assigned Shuttle 01 for 06:30 today.', 'info', 'unread', '2025-12-10 14:00:00', NULL),
(2, 1, 'Document Expiring', 'Your driver license will expire on 2026-06-15. Please renew.', 'warning', 'unread', '2025-12-10 14:10:00', NULL),
(3, 2, 'Schedule Update', 'Shuttle 02 departure moved to 07:45. Please confirm.', 'alert', 'read', '2025-12-10 14:20:00', '2025-12-10 14:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `driver_documents`
--

DROP TABLE IF EXISTS `driver_documents`;
CREATE TABLE `driver_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(11) NOT NULL,
  `doc_type` enum('license','insurance','medical','certificate','id_card','other') NOT NULL,
  `file_name` varchar(150) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `issued_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `status` (`status`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `fk_driver_documents_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_driver_documents_admin` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `driver_documents` (`id`, `driver_id`, `doc_type`, `file_name`, `file_url`, `issued_at`, `expires_at`, `status`, `verified_by`, `verified_at`, `created_at`) VALUES
(1, 1, 'license', 'Driver License - DL-2025-001', 'https://cdn.kenggo.local/docs/driver-a-license.pdf', '2024-06-16', '2026-06-15', 'approved', 1, '2025-12-10 10:00:00', '2025-12-10 09:00:00'),
(2, 1, 'insurance', 'Comprehensive Insurance', 'https://cdn.kenggo.local/docs/driver-a-insurance.pdf', '2025-01-01', '2025-12-31', 'approved', 1, '2025-12-10 10:05:00', '2025-12-10 09:05:00'),
(3, 2, 'license', 'Driver License - DL-2025-002', 'https://cdn.kenggo.local/docs/driver-b-license.pdf', '2024-08-21', '2026-08-20', 'approved', 1, '2025-12-10 10:10:00', '2025-12-10 09:10:00'),
(4, 2, 'medical', 'Medical Certificate', 'https://cdn.kenggo.local/docs/driver-b-medical.pdf', '2025-07-01', '2026-07-01', 'pending', NULL, NULL, '2025-12-10 09:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `driver_trip_assignments`
--

DROP TABLE IF EXISTS `driver_trip_assignments`;
CREATE TABLE `driver_trip_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `driver_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `assigned_by_admin_id` int(11) DEFAULT NULL,
  `status` enum('assigned','accepted','en_route','completed','cancelled','declined') DEFAULT 'assigned',
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `trip_id` (`trip_id`),
  KEY `status` (`status`),
  KEY `driver_status_date` (`driver_id`,`status`,`assigned_at`),
  CONSTRAINT `fk_driver_trip_assignments_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_driver_trip_assignments_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_driver_trip_assignments_admin` FOREIGN KEY (`assigned_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `driver_trip_assignments` (`id`, `driver_id`, `trip_id`, `assigned_by_admin_id`, `status`, `assigned_at`, `accepted_at`, `started_at`, `completed_at`, `cancelled_at`, `notes`) VALUES
(1, 1, 5, 1, 'en_route', '2025-12-10 13:58:00', '2025-12-10 14:05:00', '2025-12-11 06:20:00', NULL, NULL, 'Depart 10 minutes early for traffic'),
(2, 1, 7, 1, 'assigned', '2025-12-10 14:00:00', NULL, NULL, NULL, NULL, 'Mid-morning backup if 5 finishes early'),
(3, 2, 6, 1, 'completed', '2025-12-10 14:02:00', '2025-12-10 14:10:00', '2025-12-11 07:25:00', '2025-12-11 08:20:00', NULL, 'Completed on time'),
(4, 2, 8, 1, 'assigned', '2025-12-10 14:05:00', NULL, NULL, NULL, NULL, 'Afternoon leg'),
(5, 1, 11, 1, 'en_route', '2025-12-11 16:00:00', '2025-12-11 16:30:00', '2025-12-12 06:50:00', NULL, NULL, 'En route to destination'),
(6, 2, 12, 1, 'en_route', '2025-12-11 16:05:00', '2025-12-11 16:35:00', '2025-12-12 07:50:00', NULL, NULL, 'En route to mall'),
(7, 1, 9, 1, 'assigned', '2025-12-11 14:30:00', NULL, NULL, NULL, NULL, 'Newly assigned'),
(8, 2, 10, 1, 'assigned', '2025-12-11 14:32:00', NULL, NULL, NULL, NULL, 'Pending acceptance'),
(9, 1, 13, 1, 'completed', '2025-12-09 12:00:00', '2025-12-09 12:30:00', '2025-12-10 15:50:00', '2025-12-10 16:35:00', NULL, 'Successful trip'),
(10, 2, 14, 1, 'completed', '2025-12-08 11:00:00', '2025-12-08 11:25:00', '2025-12-09 13:50:00', '2025-12-09 14:40:00', NULL, 'Good trip'),
(11, 1, 15, 1, 'accepted', '2025-12-11 15:10:00', '2025-12-11 16:00:00', NULL, NULL, NULL, 'Accepted for tomorrow'),
(12, 2, 16, 1, 'accepted', '2025-12-11 15:12:00', '2025-12-11 16:05:00', NULL, NULL, NULL, 'Ready for tomorrow'),
(13, 1, 17, 1, 'cancelled', '2025-12-07 10:15:00', '2025-12-07 10:30:00', NULL, NULL, '2025-12-08 08:00:00', 'Weather issue'),
(14, 2, 18, 1, 'cancelled', '2025-12-06 10:20:00', '2025-12-06 10:35:00', NULL, NULL, '2025-12-07 09:00:00', 'Driver not available'),
(15, 1, 19, 1, 'completed', '2025-12-08 13:00:00', '2025-12-08 13:30:00', '2025-12-09 14:50:00', '2025-12-09 15:40:00', NULL, 'Completed successfully'),
(16, 2, 20, 1, 'completed', '2025-12-07 12:00:00', '2025-12-07 12:30:00', '2025-12-08 15:50:00', '2025-12-08 16:45:00', NULL, 'Good trip completed'),
(17, 1, 21, 1, 'accepted', '2025-12-11 16:30:00', '2025-12-11 17:00:00', NULL, NULL, NULL, 'Accepted for day after tomorrow'),
(18, 2, 22, 1, 'accepted', '2025-12-11 16:35:00', '2025-12-11 17:05:00', NULL, NULL, NULL, 'Ready for future trip'),
(19, 1, 23, 1, 'cancelled', '2025-12-05 09:00:00', '2025-12-05 09:30:00', NULL, NULL, '2025-12-06 08:00:00', 'Passenger no-show'),
(20, 2, 24, 1, 'cancelled', '2025-12-04 09:00:00', '2025-12-04 09:30:00', NULL, NULL, '2025-12-05 08:00:00', 'Route not available'),
(21, 2, 10, 1, 'en_route', '2025-12-11 14:40:00', '2025-12-11 15:00:00', '2025-12-12 13:50:00', NULL, NULL, 'Second en_route for Driver B');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- TEST CREDENTIALS FOR DRIVER LOGIN
-- ============================================================================
-- Email:    driverA@example.com
-- Password: password
--
-- Email:    driverB@example.com
-- Password: password
--
-- Password Hash (bcrypt): $2y$10$4.h1n0Gj60tOOpi52nU1d./4GXPqD/s0SSxFl3teRl3/ZIYsmtg62
-- Generated with: password_hash('password', PASSWORD_BCRYPT)
-- ============================================================================
