-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 10, 2025 at 02:35 PM
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
CREATE TABLE IF NOT EXISTS `admin_notifications` (
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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `passenger_id` int(11) NOT NULL,
  `details` text NOT NULL,
  `status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `passenger_id` (`passenger_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `passenger_id`, `details`, `status`, `created_at`) VALUES
(1, 1, 'Driver was late for the 7:30 trip.', 'pending', '2025-12-09 13:32:17'),
(2, 2, 'Shuttle was overcrowded.', 'resolved', '2025-12-09 13:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `email`, `password`, `vehicle_number`, `created_at`) VALUES
(1, 'Driver A', 'driverA@example.com', 'pass', 'SLU-001', '2025-12-09 13:32:17'),
(2, 'Driver B', 'driverB@example.com', 'pass', 'SLU-002', '2025-12-09 13:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `passenger_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `passenger_id` (`passenger_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `passenger_id`, `message`, `created_at`) VALUES
(1, 1, 'Your shuttle booking for 07:30 is confirmed.', '2025-12-09 13:32:17'),
(2, 2, 'Your complaint has been resolved.', '2025-12-09 13:32:17');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  KEY `driver_id` (`driver_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 'Shuttle 06', 'Driver B', 2, 'SLU-006', 18, 'Baguio Town Proper', 'SLU Maryheights, Bakakeng', '2025-12-11', '19:00:00', '19:40:00', 50.00, 'active', 'Late evening return', '2025-12-10 13:57:16', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
