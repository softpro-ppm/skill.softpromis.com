-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 13, 2025 at 10:56 AM
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
(2, 1, 'practical', '2024-02-20', 90.00, 100.00, 'Excellent practical skills', 'completed', '2025-04-24 05:17:04', '2025-04-24 05:17:04');

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

--
-- Dumping data for table `assigned_courses`
--

INSERT INTO `assigned_courses` (`id`, `course_id`, `sector_id`, `scheme_id`, `center_id`, `assigned_at`) VALUES
(1, 4, 5, 1, 4, '2025-05-11 08:29:51');

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

--
-- Dumping data for table `assigned_schemes`
--

INSERT INTO `assigned_schemes` (`id`, `scheme_id`, `center_id`, `assigned_at`) VALUES
(1, 1, 4, '2025-05-11 08:14:31'),
(2, 1, 1, '2025-05-11 08:15:02');

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

--
-- Dumping data for table `assigned_sectors`
--

INSERT INTO `assigned_sectors` (`id`, `sector_id`, `scheme_id`, `center_id`, `assigned_at`) VALUES
(1, 5, 1, 4, '2025-05-11 08:23:33');

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
(1, 'Test', 1, 1, 'B001', '2024-01-01', '2024-06-30', 30, 'ongoing', '2025-04-24 05:17:04', '2025-05-11 08:44:09'),
(2, 'dfgh', 2, 2, 'B002', '2024-02-01', '2024-04-30', 25, 'ongoing', '2025-04-24 05:17:04', '2025-05-11 08:44:02'),
(3, 'dfgb', 3, 3, 'B003', '2024-03-01', '2024-08-31', 20, 'upcoming', '2025-04-24 05:17:04', '2025-05-11 08:48:56');

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
(1, 1, 'CERT001', NULL, '2024-06-30', '2026-06-30', 'issued', NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, 2, 'CERT002', NULL, '2024-06-30', '2026-06-30', 'issued', NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04');

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
(1, 1, 1, 'WD001', 'Web Development', 480, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL, NULL),
(2, 1, 1, 'DM001', 'Digital Marketing', 240, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL, NULL),
(3, 1, 3, 'DA001', 'Data Analytics', 360, NULL, 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL, NULL, NULL),
(4, 5, 1, '45554', 'Melodie Nichols', 5, 'Rerum culpa nulla qu', 'active', '2025-05-01 10:04:53', '2025-05-13 10:53:50', 16.00, 'Veniam consectetur', 'Ut adipisicing moles', NULL),
(5, 9, 2, 'Excepturi ea cumque', 'Wylie Dunlap', 25, 'Tempore facilis et', 'inactive', '2025-05-03 09:11:57', '2025-05-03 09:11:57', 59.00, 'Omnis voluptatem do', 'Fugiat excepteur no', NULL);

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

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`fee_id`, `student_id`, `enrollment_id`, `amount`, `payment_date`, `payment_mode`, `transaction_id`, `status`, `created_at`, `updated_at`, `notes`, `receipt_no`) VALUES
(2, NULL, 2, 25000.00, '2024-01-01', 'online', 'TXN123457', 'paid', '2025-04-24 05:17:04', '2025-04-24 05:17:04', NULL, NULL),
(3, NULL, 3, 15000.00, '2024-02-01', 'online', 'TXN123458', 'paid', '2025-04-24 05:17:04', '2025-05-04 10:01:44', 'sdf', 'sdf'),
(5, NULL, 2, 99.00, '1978-07-02', 'Et provident qui co', 'Saepe facere aut ill', 'failed', '2025-05-04 09:21:51', '2025-05-04 10:01:36', 'ttt', 'Voluptas explicabo'),
(6, NULL, 1, 50.00, '2007-12-20', 'Sit cum adipisicing', 'Veritatis sit possi', 'failed', '2025-05-04 10:02:04', '2025-05-04 21:36:18', 'Dolor commodi repell', 'Culpa earum at id mi'),
(7, NULL, 3, 7.00, '2004-07-02', 'Aut esse et veritat', 'Illo veritatis optio', 'pending', '2025-05-04 21:36:26', '2025-05-04 21:36:26', 'Voluptatem dignissim', 'At maiores a volupta'),
(8, NULL, 1, 100.00, '2025-05-05', 'Et provident qui co', 'Sit eos fugit earum', 'paid', '2025-05-04 21:37:02', '2025-05-04 21:37:16', 'cv', 'Culpa earum at id mi'),
(9, NULL, 3, 16.00, '1985-12-02', 'Nesciunt aut magnam', 'Consequatur Sed inc', 'paid', '2025-05-04 21:39:53', '2025-05-04 21:39:53', 'Qui nulla consequatu', 'Assumenda repellendu'),
(10, NULL, 3, 400.00, '2025-05-05', 'Et provident qui co', 'Et quasi nostrum qui', 'paid', '2025-05-04 21:40:20', '2025-05-04 21:40:41', 'xdfv', 'dfv');

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
(1, 2, 'PMKVY', 'Pradhan Mantri Kaushal Vikas Yojana', 'active', '2025-04-24 05:17:04', '2025-05-13 05:28:12'),
(2, 1, 'DDU-GKY', 'Deen Dayal Upadhyaya Grameen Kaushalya Yojana', 'active', '2025-04-24 05:17:04', '2025-05-13 05:37:24'),
(3, 2, 'Regular', 'Regular Training Programs', 'active', '2025-04-24 05:17:04', '2025-05-13 05:37:33'),
(11, 3, 'Debra Moody', 'Sunt eum porro veli', 'active', '2025-05-03 08:53:33', '2025-05-13 05:28:04'),
(14, 4, 'Wayne Mack', 'sdfg', 'active', '2025-05-13 04:50:58', '2025-05-13 05:41:37');

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
(1, NULL, NULL, 'IT-ITeS', 'Information Technology and IT Enabled Services', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(2, NULL, NULL, 'Healthcare', 'Healthcare and Medical Services', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, NULL, NULL, 'Retail', 'Retail and Sales Management', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(4, NULL, NULL, 'Petra Mathis', 'Dolore magni sunt es', 'active', '2025-04-30 06:12:13', '2025-04-30 06:12:13'),
(5, NULL, NULL, 'Petra Mathis yyy', 'Dolore magni sunt es', 'active', '2025-04-30 06:12:24', '2025-04-30 06:12:41'),
(6, NULL, NULL, 'asdf', 'asdfv', 'active', '2025-04-30 06:15:32', '2025-04-30 06:24:40'),
(7, NULL, NULL, 'asdf', 'wedf', 'active', '2025-04-30 06:24:58', '2025-04-30 06:24:58'),
(9, NULL, NULL, 'dfv', 'sdc', 'active', '2025-04-30 06:37:33', '2025-04-30 06:37:33'),
(10, NULL, NULL, 'dfv', 'sdc', 'active', '2025-04-30 06:37:47', '2025-04-30 06:37:47'),
(11, NULL, NULL, 'sdfv', 'xcv', 'active', '2025-04-30 06:39:35', '2025-04-30 06:39:35'),
(12, 4, 1, 'sdfv', 'xcv', 'active', '2025-04-30 06:39:43', '2025-05-13 05:59:24'),
(17, 4, 1, 'Gillian Villarreal', 'Perspiciatis at ill', 'active', '2025-05-13 05:50:08', '2025-05-13 05:56:16');

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `enrollment_no`, `first_name`, `last_name`, `email`, `mobile`, `date_of_birth`, `gender`, `address`, `course_id`, `batch_id`, `created_at`, `updated_at`) VALUES
(1, 'ENR001', 'Rahul', 'Sharma', 'rahul@gmail.com', '9876543218', '2000-01-15', 'male', 'yyyy', NULL, NULL, '2025-04-24 05:17:04', '2025-05-03 10:03:07'),
(2, 'ENR002', 'Priya', 'Patel', 'priya@gmail.com', '9876543219', '2001-03-20', 'female', NULL, NULL, NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(3, 'ENR003', 'Amit', 'Kumar', 'amit@gmail.com', '9876543220', '1999-07-10', 'male', NULL, NULL, NULL, '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(11, 'ENR004', 'weq', 'qw', 'xavutuq@mailinator.com', '2425342424', '2010-07-30', 'male', 'qw\r\nq', NULL, NULL, '2025-05-03 11:21:00', '2025-05-03 11:21:00'),
(14, 'ENR006', 'Umakant', 'Yadav', 'uky171991@gmail.com', '9453619260', '2025-05-05', 'male', 'Jaunpur Rd', 3, 2, '2025-05-04 23:46:24', '2025-05-05 00:02:57'),
(15, 'ENR007', 'Erica', 'Steele', 'gabowyg@mailinator.com', '5656565656', '1982-04-17', 'female', 'Esse consequatur o', 3, 3, '2025-05-04 23:54:58', '2025-05-05 00:15:17');

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
(3, 3, 2, '2024-02-01', 'active', '2025-04-24 05:17:04', '2025-04-24 05:17:04'),
(4, 1, 3, '2025-05-11', 'active', '2025-05-11 08:50:45', '2025-05-11 08:50:45'),
(5, 3, 3, '2025-05-12', 'active', '2025-05-12 10:04:07', '2025-05-12 10:04:07');

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
(4, 1, 'Kameko Patel', 'Irure est accusamus ', 'vubi@mailinator.com', '+1 (326) 925-23', 'Reprehenderit non t', 'Irure molestiae cons', 'Quia repudiandae mol', 'Aliquip li', 'active', '2025-04-26 02:01:19', '2025-04-26 02:04:38');

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
(7, 'tytytyty', 'Sunt eum eu qui ut e', 'xunecejybe@mailinator.com', '+1 (227) 851-33', 'Voluptatem et sit ', 'https://www.dybubyboruhiz.ws', 'active', '2025-05-01 09:43:19', '2025-05-01 09:43:30', NULL, NULL),
(8, 'Indigo Joyce', 'Ipsum nisi id et do', 'ditopyw@mailinator.com', '+1 (616) 157-57', 'Quia ipsum amet ei', 'https://www.nuw.com', 'active', '2025-05-03 09:00:50', '2025-05-03 09:00:50', NULL, NULL),
(9, 'Justina Foley', 'Tempora in hic aut t', 'cepyraw@mailinato', '+1 (839) 493-99', 'Nisi aliquam velit u', 'https://www.hecolomuh.org.au', 'active', '2025-05-03 09:01:00', '2025-05-13 04:21:38', NULL, NULL);

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
(7, 1, 'admin', '$2y$10$zX0FKvO3WAdGwlYasWjkIOTBGEfWB9KAVshBsNXOA3KU2lkbYN93C', 'admin@softpro.com', 'Administrator', '', 'active', '2025-04-24 07:16:39', '2025-05-13 07:25:02', '3567554cad69e154b6e556d447ef4c19eb7b373cf81d2c8e6642259fc864a991', '2025-05-13 07:25:02');

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
  ADD KEY `center_id` (`center_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `certificate_no` (`certificate_number`),
  ADD KEY `enrollment_id` (`enrollment_id`);

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
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `scheme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sectors`
--
ALTER TABLE `sectors`
  MODIFY `sector_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_batch_enrollment`
--
ALTER TABLE `student_batch_enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_centers`
--
ALTER TABLE `training_centers`
  MODIFY `center_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `training_partners`
--
ALTER TABLE `training_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `student_batch_enrollment` (`enrollment_id`);

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
