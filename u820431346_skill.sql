-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 01, 2025 at 10:01 AM
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

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessment_id`, `enrollment_id`, `assessment_type`, `assessment_date`, `score`, `max_score`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'theory', '2024-02-15', 85.00, 100.00, 'Good performance', 'completed', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 1, 'practical', '2024-02-20', 90.00, 100.00, 'Excellent practical skills', 'completed', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 2, 'theory', '2024-02-15', 75.00, 100.00, 'Need improvement in theory', 'completed', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `batch_id` int(11) NOT NULL,
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

INSERT INTO `batches` (`batch_id`, `center_id`, `course_id`, `batch_code`, `start_date`, `end_date`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'B001', '2024-01-01', '2024-06-30', 30, 'ongoing', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 2, 2, 'B002', '2024-02-01', '2024-04-30', 25, 'ongoing', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 3, 3, 'B003', '2024-03-01', '2024-08-31', 20, 'upcoming', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `certificate_type` enum('completion','achievement','specialization') DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `status` enum('issued','revoked') DEFAULT 'issued',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`certificate_id`, `enrollment_id`, `certificate_number`, `certificate_type`, `issue_date`, `valid_until`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'CERT001', 'completion', '2024-06-30', '2026-06-30', 'issued', 'Good performance', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 2, 'CERT002', 'achievement', '2024-06-30', '2026-06-30', 'issued', 'Excellent practical skills', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

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
  `syllabus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `sector_id`, `scheme_id`, `course_code`, `course_name`, `duration_hours`, `description`, `status`, `created_at`, `updated_at`, `fee`, `prerequisites`, `syllabus`) VALUES
(1, 1, 1, 'WD001', 'Web Development', 480, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL),
(2, 1, 1, 'DM001', 'Digital Marketing', 240, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL),
(3, 1, 3, 'DA001', 'Data Analytics', 360, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` TEXT DEFAULT NULL,
  `receipt_no` VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `enrollment_id`, `amount`, `payment_date`, `payment_mode`, `transaction_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 25000.00, '2024-01-01', 'online', 'TXN123456', 'paid', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 2, 25000.00, '2024-01-01', 'online', 'TXN123457', 'paid', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 3, 15000.00, '2024-02-01', 'online', 'TXN123458', 'paid', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

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
  `scheme_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schemes`
--

INSERT INTO `schemes` (`scheme_id`, `scheme_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PMKVY', 'Pradhan Mantri Kaushal Vikas Yojana', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 'DDU-GKY', 'Deen Dayal Upadhyaya Grameen Kaushalya Yojana', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 'Regular', 'Regular Training Programs', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(4, 'Abel Noel', 'Excepturi esse dolo', 'inactive', '2025-04-30 01:11:27', '2025-04-30 01:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `sectors`
--

CREATE TABLE `sectors` (
  `sector_id` int(11) NOT NULL,
  `sector_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sectors`
--

INSERT INTO `sectors` (`sector_id`, `sector_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'IT-ITeS', 'Information Technology and IT Enabled Services', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 'Healthcare', 'Healthcare and Medical Services', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 'Retail', 'Retail and Sales Management', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(4, 'Petra Mathis', 'Dolore magni sunt es', 'active', '2025-04-30 06:12:13', '2025-04-30 06:12:13'),
(5, 'Petra Mathis yyy', 'Dolore magni sunt es', 'active', '2025-04-30 06:12:24', '2025-04-30 06:12:41'),
(6, 'asdf', 'asdfv', 'active', '2025-04-30 06:15:32', '2025-04-30 06:24:40'),
(7, 'asdf', 'wedf', 'active', '2025-04-30 06:24:58', '2025-04-30 06:24:58'),
(9, 'dfv', 'sdc', 'active', '2025-04-30 06:37:33', '2025-04-30 06:37:33'),
(10, 'dfv', 'sdc', 'active', '2025-04-30 06:37:47', '2025-04-30 06:37:47'),
(11, 'sdfv', 'xcv', 'active', '2025-04-30 06:39:35', '2025-04-30 06:39:35'),
(12, 'sdfv', 'xcv', 'active', '2025-04-30 06:39:43', '2025-04-30 06:39:43'),
(13, 'testing', 'test', 'active', '2025-04-30 06:42:19', '2025-04-30 06:42:19');

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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `enrollment_no`, `first_name`, `last_name`, `email`, `mobile`, `date_of_birth`, `gender`, `address`, `created_at`, `updated_at`) VALUES
(1, 'ENR001', 'Rahul', 'Sharma', 'rahul@gmail.com', '9876543218', '2000-01-15', 'male', 'yyyy', '2025-04-24 05:17:04', '2025-04-28 00:54:54'),
(2, 'ENR002', 'Priya', 'Patel', 'priya@gmail.com', '9876543219', '2001-03-20', 'female', NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 'ENR003', 'Amit', 'Kumar', 'amit@gmail.com', '9876543220', '1999-07-10', 'male', NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(5, 'ENR004', 'Malcolm', 'Bradford', 'zavi@mailinator.com', '4554545454', '1971-12-08', 'female', 'Modi qui quia ad off', '2025-04-28 00:53:23', '2025-04-28 00:53:23');

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
(1, 1, 1, '2024-01-01', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 2, 1, '2024-01-01', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 3, 2, '2024-02-01', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

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
(1, 1, 'TechSkill Mumbai Center', 'Amit Shah', 'mumbai@techskill.com', '9876543215', 'Andheri, Mumbai', NULL, NULL, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 1, 'TechSkill Pune Center', 'Sneha Patil', 'pune@techskill.com', '9876543216', 'Hinjewadi, Pune', NULL, NULL, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 2, 'DTI Delhi Center', 'Vikram Singh', 'delhi@dti.com', '9876543217', 'Connaught Place, Delhi', NULL, NULL, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(4, 1, 'Kameko Patel', 'Irure est accusamus ', 'vubi@mailinator.com', '+1 (326) 925-23', 'Reprehenderit non t', 'Irure molestiae cons', 'Quia repudiandae mol', 'Aliquip li', 'active', '2025-04-26 02:01:19', '2025-04-26 02:04:38'),
(5, 2, 'Kessie Santana', 'Aspernatur amet vol', 'sibyt@mailinator.com', '+1 (464) 557-50', 'Vitae accusamus corr', 'Autem vero iste itaq', 'Tempor doloribus qua', 'Dolorum it', 'inactive', '2025-04-26 02:04:54', '2025-05-01 09:16:33');

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
(1, 'TechSkill Solutions', 'Rajesh Kumar', 'contact@techskill.com', '9876543213', 'Mumbai, India', '', 'active', '2025-04-24 05:17:04', '2025-04-26 01:52:08', NULL, NULL),
(2, 'Digital Training Institute', 'Priya Singh', 'info@dti.com', '9876543214', 'Delhi, India', '', 'active', '2025-04-24 05:17:04', '2025-04-26 01:51:57', NULL, NULL),
(4, 'Cassidy Carr', 'Et aspernatur do dol', 'gekysopodu@mailinator.com', '+1 (338) 718-60', 'Harum facere asperio', 'https://www.lozupofetok.co', 'active', '2025-04-26 02:22:34', '2025-04-30 00:29:01', NULL, NULL),
(5, 'bdvs djkbjhs', 'jnjkbjbh', 'hhjhjkhj@gmail', '6756567', 'hjhghgh', '', 'active', '2025-04-30 00:30:31', '2025-05-01 09:16:22', '68117106bd505_reg.jpg', '68117106bd791_agr.jpg'),
(7, 'tytytyty', 'Sunt eum eu qui ut e', 'xunecejybe@mailinator.com', '+1 (227) 851-33', 'Voluptatem et sit ', 'https://www.dybubyboruhiz.ws', 'active', '2025-05-01 09:43:19', '2025-05-01 09:43:30', NULL, NULL);

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
(7, 1, 'admin', '$2y$10$zX0FKvO3WAdGwlYasWjkIOTBGEfWB9KAVshBsNXOA3KU2lkbYN93C', 'admin@softpro.com', 'Administrator', '', 'active', '2025-04-24 07:16:39', '2025-05-01 08:53:43', '4a9964f3c0f81d851b0e0a8738291090c2dc934025791e052332c036a1e08c28', '2025-05-01 08:53:43');

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
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD UNIQUE KEY `batch_code` (`batch_code`),
  ADD KEY `center_id` (`center_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `sector_id` (`sector_id`),
  ADD KEY `scheme_id` (`scheme_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

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
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`);

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
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schemes`
--
ALTER TABLE `schemes`
  MODIFY `scheme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sectors`
--
ALTER TABLE `sectors`
  MODIFY `sector_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_batch_enrollment`
--
ALTER TABLE `student_batch_enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_centers`
--
ALTER TABLE `training_centers`
  MODIFY `center_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `training_partners`
--
ALTER TABLE `training_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`center_id`) REFERENCES `training_centers` (`center_id`),
  ADD CONSTRAINT `batches_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`sector_id`),
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`scheme_id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`);

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
