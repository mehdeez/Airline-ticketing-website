-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 09:38 AM
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
-- Database: `skyvoyager_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `booking_reference` char(8) NOT NULL,
  `seat_numbers` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flight_schedule`
--

CREATE TABLE `flight_schedule` (
  `flight_id` int(11) NOT NULL,
  `airline_code` char(2) NOT NULL,
  `flight_number` varchar(6) NOT NULL,
  `origin_code` char(3) NOT NULL,
  `destination_code` char(3) NOT NULL,
  `departure_utc` datetime NOT NULL,
  `arrival_utc` datetime NOT NULL,
  `duration_minutes` smallint(6) DEFAULT NULL,
  `aircraft_type` varchar(20) DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `seats_total` int(11) NOT NULL,
  `seats_remaining` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight_schedule`
--

INSERT INTO `flight_schedule` (`flight_id`, `airline_code`, `flight_number`, `origin_code`, `destination_code`, `departure_utc`, `arrival_utc`, `duration_minutes`, `aircraft_type`, `base_price`, `seats_total`, `seats_remaining`, `is_active`) VALUES
(1, 'SV', 'SVO101', 'JFK', 'LAX', '2023-12-15 08:00:00', '2023-12-15 11:30:00', 210, 'Boeing 737', 299.99, 180, 150, 1),
(2, 'SV', 'SVO202', 'LAX', 'ORD', '2023-12-16 14:00:00', '2023-12-16 19:45:00', 345, 'Airbus A320', 349.99, 160, 120, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `account_status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `created_at`, `last_login`, `account_status`) VALUES
(1, 'zubair', 'asdf@asdf.com', '$2y$10$5uLOl/LNDCH4K1nh9t.Mhe//TaCZk1CtRJInQMKP52xEYfp/NpHnq', 'zub m', '01234567891', '2025-04-20 06:24:53', NULL, 'active'),
(2, 'admin', 'aasdf@asdf.com', '$2y$10$ojiviAPw9CDhchle4rGU/uqX59gjJjgS.xDFbtcCwCn/Jw2mNFMgW', 'zub mq', '01234567891', '2025-04-20 07:05:16', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indexes for table `flight_schedule`
--
ALTER TABLE `flight_schedule`
  ADD PRIMARY KEY (`flight_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flight_schedule`
--
ALTER TABLE `flight_schedule`
  MODIFY `flight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`flight_id`) REFERENCES `flight_schedule` (`flight_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
