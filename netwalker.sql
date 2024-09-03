-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 03, 2024 at 09:46 PM
-- Server version: 8.0.39-0ubuntu0.20.04.1
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `netwalker`
--

-- --------------------------------------------------------

--
-- Table structure for table `dogs`
--

CREATE TABLE `dogs` (
  `dog_id` int NOT NULL,
  `owner_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `breed` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dogs`
--

INSERT INTO `dogs` (`dog_id`, `owner_id`, `name`, `breed`, `gender`, `age`, `description`) VALUES
(4, 27, 'Tacsko', 'Vadállat', 'male', 500, 'Nagyon veszélyes😂');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiry_id` int NOT NULL,
  `owner_id` int DEFAULT NULL,
  `walker_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`inquiry_id`, `owner_id`, `walker_id`, `message`, `created_at`, `is_read`) VALUES
(20, 27, 24, 'Asd', '2024-09-03 19:07:39', 1),
(21, 27, 24, 'Asd', '2024-09-03 19:08:22', 1),
(22, 27, 24, '123', '2024-09-03 19:10:24', 1),
(23, 27, 24, '123', '2024-09-03 19:10:30', 1),
(24, 27, 24, '123', '2024-09-03 19:10:36', 1),
(25, 27, 24, '123', '2024-09-03 19:17:28', 1),
(26, 27, 24, '123', '2024-09-03 19:17:48', 1),
(27, 27, 24, 'Kutya', '2024-09-03 19:20:07', 1),
(28, 27, 24, 'Dzsonikám ', '2024-09-03 19:25:04', 1),
(29, 27, 24, 'Dzsonikám ', '2024-09-03 19:26:08', 1),
(30, 31, 24, 'Udv!', '2024-09-03 20:57:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int NOT NULL,
  `walker_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `walker_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(6, 24, 27, 5, 'A braate', '2024-09-03 19:26:03');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int NOT NULL,
  `reported_user_id` int DEFAULT NULL,
  `reported_by_user_id` int DEFAULT NULL,
  `report_reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `report_description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `report_content` text COLLATE utf8mb4_general_ci,
  `is_resolved` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `reported_user_id`, `reported_by_user_id`, `report_reason`, `report_description`, `created_at`, `report_content`, `is_resolved`) VALUES
(6, 24, 24, 'Problema', 'Nem tudok uzenetet kuldeni', '2024-09-03 20:36:57', NULL, 0),
(7, 31, 31, 'problema', 'nem tudok uzenetet kuldeni', '2024-09-03 21:01:23', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `token_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_type` enum('activation','password_reset') COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`token_id`, `user_id`, `token`, `token_type`, `expires_at`, `used`, `created_at`) VALUES
(9, 24, 'dd24d75dbed47993aba870b6ec151f5a', 'activation', '2024-09-04 18:42:21', 0, '2024-09-03 18:42:21'),
(10, 25, 'a205686f8f489338fbb1946f8d9c4419', 'activation', '2024-09-04 18:45:49', 0, '2024-09-03 18:45:49'),
(12, 27, '9373354daa38269f35d689af8fa13e46', 'activation', '2024-09-04 19:05:33', 0, '2024-09-03 19:05:33'),
(13, 28, '5a4add67955480f546fdc240d91b28da', 'activation', '2024-09-04 19:16:43', 0, '2024-09-03 19:16:43'),
(14, 29, '90032d00858a611679608871abce5cd4', 'activation', '2024-09-04 19:23:31', 0, '2024-09-03 19:23:31'),
(15, 24, '88f02595a40703a22f76a131c085fb49', 'password_reset', '2024-09-03 20:35:46', 0, '2024-09-03 19:35:46'),
(16, 24, 'd9c30a792dc427d89730db3d4c3fce62', 'password_reset', '2024-09-03 20:45:10', 0, '2024-09-03 19:45:10'),
(17, 24, 'e8fdcd7e7bbf3277a2b9294c8c6acab0', 'password_reset', '2024-09-03 20:51:36', 0, '2024-09-03 19:51:36'),
(18, 24, 'b66561661dd154a9c2231d40ec167a10', 'password_reset', '2024-09-03 20:55:56', 0, '2024-09-03 19:55:56'),
(19, 24, 'f01c6d8e90bf2a667cd3a630a6947bda', 'password_reset', '2024-09-03 21:01:18', 0, '2024-09-03 20:01:18'),
(20, 24, '2a1e486bdcc75fc0ca9ec3b041e94a8f', 'password_reset', '2024-09-03 20:04:31', 1, '2024-09-03 20:04:12'),
(22, 31, '6d7fd6291ec66587376121282a2481e4', 'activation', '2024-09-03 20:56:54', 1, '2024-09-03 20:56:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_walker` tinyint(1) DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT '0',
  `is_approved` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `first_name`, `last_name`, `phone_number`, `address`, `email`, `is_walker`, `is_admin`, `is_approved`, `is_active`, `created_at`) VALUES
(24, 'Jonathan', '$2y$10$cwnkcDfP0JjhTh2h7SAhLeFPnbsQxXk0xiBHVfzPMvherQeJ77lOC', 'Borics', 'Jonathan', '0629618409', 'Mala 24', 'jonathanborics97@gmail.com', 1, 0, 1, 1, '2024-09-03 18:42:21'),
(25, 'Boris', '$2y$10$DDsnXaCLDJ0BWbtr7j76buLM.8OtH1WkQ2PtG8oECegf273ZAmSGW', 'Olah', 'Boris', '0611525372', 'Mala 12', 'joniboricsem32@gmail.com', 0, 1, 0, 1, '2024-09-03 18:45:49'),
(27, 'Roberto', '$2y$10$yzEHz11Qe8ohtoejg2u0xOxySm3e66TUzlWPCzciJE.04xD7eGPnW', 'Robert ', 'O', '0620_123-321', 'Zsákutca 92', 'olahrobert028@gmail.com', 0, 0, 0, 1, '2024-09-03 19:05:33'),
(28, 'Nastavnica', '$2y$10$CvDL.fFL9Jy4g59Dwb4I/e1aUvUXfAjgKwwGwLnMGxe/YtV4Qpyg6', 'Ivana', '', '', '', 'ivanaknapik@gmail.com', 0, 0, 0, 1, '2024-09-03 19:16:43'),
(29, 'Lidia', '$2y$10$XAwn1e7/A8NIDA7W25YCYOr7IBF4ja21kmidNSr7eFTGjmV95JYXG', 'Lidia', 'Veréb ', '', '', 'lidiavereb@gmail.com', 1, 0, 1, 1, '2024-09-03 19:23:31'),
(31, 'Peter', '$2y$10$RoQkn9fLmC/eDk4tE5E1e.bLYWHrbqKk8zvsexsrB0JIt56b6fXNe', 'Kovacs', 'Peter', '2132312321', 'Subotica, Sutjeska 21', 'boricsfamily@gmail.com', 0, 0, 0, 1, '2024-09-03 20:56:39');

-- --------------------------------------------------------

--
-- Table structure for table `walker_profiles`
--

CREATE TABLE `walker_profiles` (
  `walker_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `bio` text COLLATE utf8mb4_general_ci,
  `photo_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `favorite_breed` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `walker_profiles`
--

INSERT INTO `walker_profiles` (`walker_id`, `user_id`, `bio`, `photo_url`, `favorite_breed`) VALUES
(11, 24, 'Sziasztok, Borics Jonathán vagyok.\r\nnagyon szeretem a kutyusokat.', 'uploads/barni.jpg', 'Rotweiler'),
(12, 25, '', '', ''),
(13, 29, 'Szeretek kutyákat sétáltatni és úgy gondolom hogy a kutyák az ember legjobb barátai.', 'uploads/2d3e581681e5caed9446036c3b929dfb.jpg', 'Golden retriever ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dogs`
--
ALTER TABLE `dogs`
  ADD PRIMARY KEY (`dog_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiry_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `walker_id` (`walker_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ratings_ibfk_1` (`walker_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reported_by_user_id` (`reported_by_user_id`),
  ADD KEY `fk_reported_user_id` (`reported_user_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  ADD PRIMARY KEY (`walker_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dogs`
--
ALTER TABLE `dogs`
  MODIFY `dog_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiry_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `token_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  MODIFY `walker_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dogs`
--
ALTER TABLE `dogs`
  ADD CONSTRAINT `dogs_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`walker_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`walker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reported_user_id` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_by_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  ADD CONSTRAINT `walker_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
