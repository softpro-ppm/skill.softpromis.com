-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 17, 2025 at 01:42 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u820431346_skill`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessment_id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `assessment_type` enum('theory','practical','project') DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `max_score` decimal(5,2) DEFAULT 100.00,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_courses`
--

CREATE TABLE `assigned_courses` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_schemes`
--

CREATE TABLE `assigned_schemes` (
  `id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_sectors`
--

CREATE TABLE `assigned_sectors` (
  `id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `batch_id` int(11) NOT NULL,
  `batch_name` varchar(100) NOT NULL,
  `center_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `batch_code` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batch_id`, `batch_name`, `center_id`, `course_id`, `batch_code`, `start_date`, `end_date`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(11, 'ELE-B1', NULL, 7, 'B001', '2025-05-01', '2025-05-31', 60, 'ongoing', '2025-05-15 00:46:12', '2025-05-15 06:34:38'),
(12, 'Skyler Drake', NULL, 7, 'B012', '1972-05-23', '2010-12-22', 44, 'completed', '2025-05-15 06:14:45', '2025-05-15 06:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `certificate_type` enum('completion','achievement','specialization') DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `status` enum('issued','revoked') DEFAULT 'issued',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `sector_id` int(11) DEFAULT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fee` decimal(10,2) DEFAULT NULL,
  `prerequisites` text DEFAULT NULL,
  `syllabus` text DEFAULT NULL,
  `center_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `sector_id`, `scheme_id`, `course_code`, `course_name`, `duration_hours`, `description`, `status`, `created_at`, `updated_at`, `fee`, `prerequisites`, `syllabus`, `center_id`) VALUES
(7, 18, 15, 'ELE1', 'Asst. Electrician', 360, '', 'active', '2025-05-15 00:43:12', '2025-05-15 00:43:12', 3000.00, '', '', 11);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL,
  `receipt_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `created_at`, `updated_at`, `permissions`) VALUES
(1, 'admin', 'System Administrator', '2025-04-24 05:17:04', '2025-04-28 00:10:59', '[\"view_dashboard\",\"view_users\",\"create_users\",\"edit_users\",\"delete_users\",\"view_training\",\"create_training\",\"edit_training\",\"delete_training\",\"view_reports\",\"generate_reports\",\"export_reports\"]'),
(2, 'trainer', 'Training Staff', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL),
(3, 'assessor', 'Assessment Staff', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL),
(4, 'student', 'Student User', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schemes`
--

CREATE TABLE `schemes` (
  `scheme_id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `scheme_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schemes`
--

INSERT INTO `schemes` (`scheme_id`, `center_id`, `scheme_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(15, 11, 'PMKVY', 'PMKVY', 'active', '2025-05-15 00:41:48', '2025-05-15 00:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `sectors`
--

CREATE TABLE `sectors` (
  `sector_id` int(11) NOT NULL,
  `center_id` int(11) DEFAULT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `sector_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sectors`
--

INSERT INTO `sectors` (`sector_id`, `center_id`, `scheme_id`, `sector_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(18, 11, 15, 'Electronics', 'Electronics', 'active', '2025-05-15 00:42:16', '2025-05-15 00:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `enrollment_no` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(255) DEFAULT NULL,
  `aadhaar` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `enrollment_no`, `first_name`, `last_name`, `email`, `mobile`, `date_of_birth`, `gender`, `address`, `course_id`, `batch_id`, `created_at`, `updated_at`, `photo`, `aadhaar`, `qualification`) VALUES
(16, 'ENR001', 'Rajesh', 'G', 'pusyriqani@mailinator.com', '9550755039', '2007-07-11', 'male', 'Illum ut incidunt', 7, 11, '2025-05-15 00:47:07', '2025-05-15 08:14:27', NULL, NULL, NULL),
(18, 'ENR002', 'aaa', '', 'abc@mailinator.com', '6776676776', '1985-08-17', 'other', 'Lorem saepe minus co', 7, 11, '2025-05-17 01:26:48', '2025-05-17 01:38:04', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_batch_enrollment`
--

CREATE TABLE `student_batch_enrollment` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `status` enum('active','completed','dropped') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_batch_enrollment`
--

INSERT INTO `student_batch_enrollment` (`enrollment_id`, `student_id`, `batch_id`, `enrollment_date`, `status`, `created_at`, `updated_at`) VALUES
(7, 16, 11, '2025-05-15', 'active', '2025-05-15 07:54:47', '2025-05-15 08:14:27'),
(9, 18, 11, '2025-05-17', 'active', '2025-05-17 01:26:48', '2025-05-17 01:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `training_centers`
--

CREATE TABLE `training_centers` (
  `center_id` int(11) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `center_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_centers`
--

INSERT INTO `training_centers` (`center_id`, `partner_id`, `center_name`, `contact_person`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `status`, `created_at`, `updated_at`) VALUES
(11, 20, 'Softpro Branch', 'Aperiam Et Minim Vol', 'duqel@mailinator.com', '5456456456', 'Libero provident en', 'Vel Laboris Non Aliq', 'Non Officiis Quisqua', '564564', 'active', '2025-05-15 00:34:51', '2025-05-15 00:41:15'),
(12, 21, 'Imani Wagner', 'Proident Voluptatem', 'qyquxevu@mailinator.com', '1617382164', 'Sit est laboris eni', 'Quos Repellendus Pr', 'Aliquid Amet Enim V', '564656', 'active', '2025-05-15 01:22:47', '2025-05-15 01:22:47');

-- --------------------------------------------------------

--
-- Table structure for table `training_partners`
--

CREATE TABLE `training_partners` (
  `partner_id` int(11) NOT NULL,
  `partner_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `registration_doc` varchar(255) DEFAULT NULL,
  `agreement_doc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_partners`
--

INSERT INTO `training_partners` (`partner_id`, `partner_name`, `contact_person`, `email`, `phone`, `address`, `website`, `status`, `created_at`, `updated_at`, `registration_doc`, `agreement_doc`) VALUES
(20, 'Softpro', 'Rerum vel voluptas p', 'mabixetaz@mailinator.com', '5454555554', 'Ad quis culpa aliqui', 'https://www.rulakifiniqetu.me.uk', 'active', '2025-05-15 00:33:36', '2025-05-15 00:34:24', '68253660cb1c8_reg.pdf', '68253660ce96e_agr.pdf'),
(21, 'Metx', 'Error nulla distinct', 'nylyqy@mailinator.com', '5415341564', 'Debitis aut dolore i', 'https://www.myjilygufipe.us', 'active', '2025-05-15 01:21:44', '2025-05-15 01:22:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token` text DEFAULT NULL,
  `last_login` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `username`, `password`, `email`, `full_name`, `mobile`, `status`, `created_at`, `updated_at`, `token`, `last_login`) VALUES
(5, 2, 'trainer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer@softpro.com', 'John Trainer', '9876543211', 'active', '2025-04-24 07:12:13', '2025-04-24 07:12:13', NULL, NULL),
(6, 3, 'assessor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assessor@softpro.com', 'Jane Assessor', '9876543212', 'active', '2025-04-24 07:12:13', '2025-04-24 07:12:13', NULL, NULL),
(7, 1, 'admin', '$2y$10$zX0FKvO3WAdGwlYasWjkIOTBGEfWB9KAVshBsNXOA3KU2lkbYN93C', 'admin@softpro.com', 'Administrator', '', 'active', '2025-04-24 07:16:39', '2025-05-17 01:08:57', '29be2a27b2844b6b005a1e64c7a79b482ceaf497c4a32b76719d5ece9363eea2', '2025-05-17 01:08:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`assessment_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `assigned_courses`
--
ALTER TABLE `assigned_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`course_id`,`sector_id`,`scheme_id`,`center_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `sector_id` (`sector_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `assigned_schemes`
--
ALTER TABLE `assigned_schemes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`scheme_id`,`center_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `assigned_sectors`
--
ALTER TABLE `assigned_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`sector_id`,`scheme_id`,`center_id`),
  ADD KEY `sector_id` (`sector_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD UNIQUE KEY `batch_code` (`batch_code`),
  ADD UNIQUE KEY `center_id_2` (`center_id`),
  ADD KEY `center_id` (`center_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `certificate_no` (`certificate_number`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `sector_id` (`sector_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `fk_courses_center` (`center_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `schemes`
--
ALTER TABLE `schemes`
  ADD PRIMARY KEY (`scheme_id`);

--
-- Indexes for table `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`sector_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`),
  ADD KEY `fk_students_course` (`course_id`),
  ADD KEY `fk_students_batch` (`batch_id`);

--
-- Indexes for table `student_batch_enrollment`
--
ALTER TABLE `student_batch_enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `training_centers`
--
ALTER TABLE `training_centers`
  ADD PRIMARY KEY (`center_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `training_partners`
--
ALTER TABLE `training_partners`
  ADD PRIMARY KEY (`partner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assigned_courses`
--
ALTER TABLE `assigned_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assigned_schemes`
--
ALTER TABLE `assigned_schemes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assigned_sectors`
--
ALTER TABLE `assigned_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schemes`
--
ALTER TABLE `schemes`
  MODIFY `scheme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sectors`
--
ALTER TABLE `sectors`
  MODIFY `sector_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `student_batch_enrollment`
--
ALTER TABLE `student_batch_enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `training_centers`
--
ALTER TABLE `training_centers`
  MODIFY `center_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `training_partners`
--
ALTER TABLE `training_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`);

--
-- Constraints for table `assigned_courses`
--
ALTER TABLE `assigned_courses`
  ADD CONSTRAINT `assigned_courses_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_courses_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`sector_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_courses_ibfk_3` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`scheme_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_courses_ibfk_4` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`) ON DELETE CASCADE;

--
-- Constraints for table `assigned_schemes`
--
ALTER TABLE `assigned_schemes`
  ADD CONSTRAINT `assigned_schemes_ibfk_1` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`scheme_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_schemes_ibfk_2` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`) ON DELETE CASCADE;

--
-- Constraints for table `assigned_sectors`
--
ALTER TABLE `assigned_sectors`
  ADD CONSTRAINT `assigned_sectors_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`sector_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_sectors_ibfk_2` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`scheme_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assigned_sectors_ibfk_3` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`) ON DELETE CASCADE;

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`),
  ADD CONSTRAINT `batches_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`),
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`sector_id`),
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`scheme_id`),
  ADD CONSTRAINT `fk_courses_center` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`),
  ADD CONSTRAINT `fees_ibfk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`),
  ADD CONSTRAINT `fk_students_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `student_batch_enrollment`
--
ALTER TABLE `student_batch_enrollment`
  ADD CONSTRAINT `student_batch_enrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_batch_enrollment_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`);

--
-- Constraints for table `training_centers`
--
ALTER TABLE `training_centers`
  ADD CONSTRAINT `training_centers_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `training_partners` (`partner_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
