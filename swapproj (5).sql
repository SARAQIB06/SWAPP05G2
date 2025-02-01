-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 04:29 PM
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
-- Database: `swapproj`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('create','update','delete','view') DEFAULT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `created_time` date NOT NULL,
  `location` varchar(30) NOT NULL,
  `image` blob NOT NULL,
  `school` enum('','IIT','ENG','DES','BUS','ASC','HSS') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `name`, `description`, `model_number`, `purchase_date`, `created_time`, `location`, `image`, `school`) VALUES
(1, 'Calculator', 'Calculator', 'X-1234', '2024-05-08', '2025-01-09', '17-5-10', '', 'ENG');

-- --------------------------------------------------------

--
-- Table structure for table `student_inventory`
--

CREATE TABLE `student_inventory` (
  `student_inventory_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `borrowed_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` timestamp NULL DEFAULT NULL,
  `status` enum('assigned','in-use','returned') DEFAULT 'assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_inventory`
--

INSERT INTO `student_inventory` (`student_inventory_id`, `user_id`, `inventory_id`, `borrowed_date`, `return_date`, `status`) VALUES
(1, 4, 1, '2025-01-08 16:00:00', '2025-11-17 16:00:00', 'assigned'),
(3, 8, 1, '2025-01-29 12:11:28', '2025-01-09 16:00:00', 'in-use');

-- --------------------------------------------------------

--
-- Table structure for table `usage_logs`
--

CREATE TABLE `usage_logs` (
  `usage_log_id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `usage_description` text DEFAULT NULL,
  `maintenance_description` text DEFAULT NULL,
  `usage_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  `mobile_number` int(15) NOT NULL,
  `role` enum('student','facility manager','admin') NOT NULL DEFAULT 'student',
  `username` varchar(30) NOT NULL,
  `school` enum('','IIT','ENG','DES','BUS','ASC','HSS') DEFAULT NULL,
  `reset_password` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `mobile_number`, `role`, `username`, `school`, `reset_password`) VALUES
(4, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'student', 'stu1', 'ENG', 'no'),
(5, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'admin', 'ad1', '', 'yes'),
(6, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'facility manager', 'fm1', '', 'yes'),
(7, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'student', 'stu2', 'IIT', 'yes'),
(8, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'student', 'stu3', 'ENG', 'no'),
(11, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'student', 'stu112343', 'BUS', 'yes'),
(12, 'testemail@gmail.com', '489cd5dbc708c7e541de4d7cd91ce6d0f1613573b7fc5b40d3942ccb9555cf35', 96362476, 'student', 'stu112343', 'BUS', 'yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `id` (`inventory_id`),
  ADD KEY `course_id` (`school`);

--
-- Indexes for table `student_inventory`
--
ALTER TABLE `student_inventory`
  ADD PRIMARY KEY (`student_inventory_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `usage_logs`
--
ALTER TABLE `usage_logs`
  ADD PRIMARY KEY (`usage_log_id`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_inventory`
--
ALTER TABLE `student_inventory`
  MODIFY `student_inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `usage_logs`
--
ALTER TABLE `usage_logs`
  MODIFY `usage_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student_inventory`
--
ALTER TABLE `student_inventory`
  ADD CONSTRAINT `student_inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `student_inventory_ibfk_2` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`);

--
-- Constraints for table `usage_logs`
--
ALTER TABLE `usage_logs`
  ADD CONSTRAINT `usage_logs_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
