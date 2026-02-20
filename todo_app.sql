-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2025 at 03:49 PM
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
-- Database: `todo_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `status` enum('pending','finished') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task`, `status`, `created_at`) VALUES
(19, 1, 'Test Task', 'pending', '2025-02-21 15:55:54'),
(46, 34, 'mobdev', '', '2025-02-21 17:45:49'),
(47, 34, 'DSA study', '', '2025-02-21 17:52:30'),
(62, 33, 'DSA study', 'pending', '2025-02-22 14:41:11'),
(63, 33, 'DSA study', 'pending', '2025-02-22 14:41:13'),
(64, 33, 'DSA study', 'pending', '2025-02-22 14:41:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'aller-franz', '$2y$10$Au0vnqcFmamSYh0OziyySepTh2FnKzlSkYo1.7gwYdqhWeYR25oZ2', '2025-02-21 15:35:39'),
(10, 'allerfranz', '$2y$10$I.3za4DtK1KuGpQAnLmhhOLze4Y../HRrfNgocQTnSBcibR1R2yHS', '2025-02-21 15:36:19'),
(15, 'kyle123', '$2y$10$XZQkPs7cp2C4qigFUPWo3.cczJNRBeLwIqghl3bali.Mw16RrVA3.', '2025-02-21 15:37:05'),
(24, 'franzgheric', '$2y$10$kL0rnYsJUAAlsTsyykb1HeFSO.3I5M1pyYMmfCsecOFh.JDxmUkmK', '2025-02-21 15:43:39'),
(33, 'they123', '$2y$10$qLXr0yX5s26VToU6JU6k3uRdtOCglByHZyzmXSpFHx5OirLNeo7We', '2025-02-21 16:38:36'),
(34, 'aller123', '$2y$10$STlvnHWBtPZka7JW8N312ugCtoZjCutGTsUD00rlJpxkd3t0CbXHe', '2025-02-21 16:46:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
