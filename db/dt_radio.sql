-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 06, 2026 at 12:42 PM
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
-- Database: `cl_dt_radio`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `user_name`, `email`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$TiPWXGHgw0txVkj07fY5DOy1Dde1uTgA0W9OZhzKiIue.UNJXC6.q', 1, '2023-04-05 05:20:50', '2024-03-23 06:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_artist`
--

CREATE TABLE `tbl_artist` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Song, 2- Podcast, 3- Music	',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_banner`
--

CREATE TABLE `tbl_banner` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Song, 2- Podcast, 3- Music',
  `content_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_batch`
--

CREATE TABLE `tbl_batch` (
  `id` int(11) NOT NULL,
  `input_file_id` varchar(255) NOT NULL,
  `batch_id` varchar(255) NOT NULL,
  `output_file_id` varchar(255) NOT NULL,
  `error_file_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_city`
--

CREATE TABLE `tbl_city` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment`
--

CREATE TABLE `tbl_comment` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Song, 2- Podcast Episode, 3- Music',
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_episode`
--

CREATE TABLE `tbl_episode` (
  `id` int(10) UNSIGNED NOT NULL,
  `podcasts_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `portrait_img` varchar(255) NOT NULL,
  `landscape_img` varchar(255) NOT NULL,
  `episode_upload_type` varchar(255) NOT NULL COMMENT 'server_video, external_url, youtube',
  `episode_audio` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `total_play` int(11) NOT NULL DEFAULT 0,
  `sortable` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_event_join_user`
--

CREATE TABLE `tbl_event_join_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `live_event_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Paid,0- Free	',
  `transaction_id` varchar(255) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favorite`
--

CREATE TABLE `tbl_favorite` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Song, 2- Podcast, 3- Music',
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_follow`
--

CREATE TABLE `tbl_follow` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_general_setting`
--

CREATE TABLE `tbl_general_setting` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `tbl_general_setting`
--

INSERT INTO `tbl_general_setting` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'DTRadio', '2022-08-03 12:38:42', '2024-03-01 04:07:24'),
(2, 'host_email', 'support@divinetechs.com', '2022-08-03 12:38:42', '2024-03-01 04:07:24'),
(3, 'app_version', '2.0', '2022-08-03 12:38:42', '2026-03-06 11:17:46'),
(4, 'author', 'Divinetechs', '2022-08-03 12:38:42', '2024-03-01 04:07:24'),
(5, 'email', 'support@divinetechs.com', '2022-08-03 12:38:42', '2024-03-01 04:07:24'),
(6, 'contact', '+91 7984859403', '2022-08-03 12:38:42', '2026-03-06 11:18:04'),
(7, 'app_desripation', 'DivineTechs, a top web & mobile app development company offering innovative solutions for diverse industry verticals. We have creative and dedicated group of developers who are mastered in Apps Developments and Web Development with a nice in delivering quality solutions to customers across the globe.', '2022-08-03 12:38:42', '2024-03-01 04:07:24'),
(11, 'app_logo', '', '2022-08-03 12:38:42', '2026-03-06 10:57:50'),
(12, 'website', 'https://www.divinetechs.com', '2022-08-03 12:38:42', '2026-03-06 11:18:14'),
(13, 'currency', 'INR', '2022-08-03 12:38:42', '2024-03-01 04:07:54'),
(14, 'currency_code', '₹', '2022-08-03 12:38:42', '2024-03-01 04:07:54'),
(25, 'banner_ad', '0', '2022-08-03 12:38:42', '2025-08-13 11:06:14'),
(26, 'banner_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:26:54'),
(27, 'interstital_ad', '0', '2022-08-03 12:38:42', '2025-08-13 11:06:14'),
(28, 'interstital_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:26:54'),
(29, 'interstital_adclick', '', '2022-08-03 12:38:42', '2026-03-06 05:26:54'),
(30, 'reward_ad', '0', '2022-08-03 12:38:42', '2025-08-13 11:06:14'),
(31, 'reward_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:26:54'),
(32, 'reward_adclick', '', '2022-08-03 12:38:42', '2026-03-06 05:26:54'),
(33, 'ios_banner_ad', '0', '2022-08-03 12:38:42', '2026-02-26 03:40:06'),
(34, 'ios_banner_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:27:05'),
(35, 'ios_interstital_ad', '0', '2022-08-03 12:38:42', '2026-02-26 03:40:06'),
(36, 'ios_interstital_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:27:05'),
(37, 'ios_interstital_adclick', '', '2022-08-03 12:38:42', '2026-03-06 05:27:05'),
(38, 'ios_reward_ad', '0', '2022-08-03 12:38:42', '2026-02-26 03:40:06'),
(39, 'ios_reward_adid', '', '2022-08-03 12:38:42', '2026-03-06 05:27:05'),
(40, 'ios_reward_adclick', '', '2022-08-03 12:38:42', '2026-03-06 05:27:05'),
(41, 'fb_native_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(42, 'fb_native_id', '', '2022-08-03 12:38:42', '2023-04-15 11:23:54'),
(43, 'fb_banner_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(44, 'fb_banner_id', '', '2022-08-03 12:38:42', '2023-04-15 11:23:56'),
(45, 'fb_interstiatial_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(46, 'fb_interstiatial_id', '', '2022-08-03 12:38:42', '2024-03-01 05:00:28'),
(47, 'fb_rewardvideo_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(48, 'fb_rewardvideo_id', '', '2022-08-03 12:38:42', '2023-04-15 11:24:00'),
(49, 'fb_native_full_status', '0', '2022-08-03 12:38:42', '2026-03-06 10:58:01'),
(50, 'fb_native_full_id', '', '2022-08-03 12:38:42', '2024-03-01 04:53:21'),
(51, 'fb_ios_native_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(52, 'fb_ios_native_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(53, 'fb_ios_banner_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(54, 'fb_ios_banner_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(55, 'fb_ios_interstiatial_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(56, 'fb_ios_interstiatial_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(57, 'fb_ios_rewardvideo_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(58, 'fb_ios_rewardvideo_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(59, 'fb_ios_native_full_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(60, 'fb_ios_native_full_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(61, 'onesignal_apid', '', '2022-08-03 12:38:42', '2026-03-06 10:58:05'),
(62, 'onesignal_rest_key', '', '2022-08-03 12:38:42', '2026-03-02 01:22:07'),
(72, 'fb_interstital_adclick', '', '2024-02-29 05:09:15', '2024-03-01 10:33:31'),
(73, 'fb_reward_adclick', '', '2024-02-29 05:09:28', '2024-03-01 05:00:20'),
(74, 'fb_ios_interstital_adclick', '', '2024-02-29 05:09:38', '2024-03-01 05:15:19'),
(75, 'fb_ios_reward_adclick', '', '2024-02-29 05:09:48', '2024-03-01 05:15:19'),
(76, 'page_background_color', '#FFFFFF', '2024-08-23 11:17:09', '2024-08-23 11:17:09'),
(77, 'page_title_color', '#000000', '2024-08-23 11:17:09', '2024-08-23 11:17:09'),
(78, 'notification_configuration', '0', '2025-10-27 09:09:24', '2026-03-06 10:58:12'),
(79, 'dev_title', '', '2025-12-11 07:22:37', '2026-03-06 11:19:45'),
(80, 'dev_logo', '', '2025-12-11 07:22:37', '2026-03-06 10:58:19'),
(81, 'screenshot', '0', '2025-12-31 05:33:03', '2026-03-06 10:58:23'),
(82, 'login_page_image', '', '2026-01-05 12:04:06', '2026-01-05 12:04:06'),
(83, 'company_name', 'DivineTechs', '2026-02-24 11:52:19', '2026-03-06 11:19:47'),
(84, 'company_logo', '', '2026-02-24 11:52:19', '2026-02-24 11:52:19'),
(85, 'ai_api_key', '', '2026-02-24 11:52:35', '2026-03-06 10:58:33'),
(86, 'ai_section', '0', '2026-02-25 11:44:15', '2026-03-06 10:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_language`
--

CREATE TABLE `tbl_language` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_live_event`
--

CREATE TABLE `tbl_live_event` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `portrait_img` varchar(255) NOT NULL,
  `landscape_img` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `is_paid` int(11) NOT NULL COMMENT '1- Paid,0- Free',
  `price` int(11) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL COMMENT '1- Audio, 2- Video',
  `link` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_music`
--

CREATE TABLE `tbl_music` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist_id` varchar(255) NOT NULL,
  `album_name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `is_premium` int(11) NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `upload_type` int(11) NOT NULL COMMENT '	1- Server Content, 2- External URL, 3- Youtube	',
  `music` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `portrait_img` varchar(255) NOT NULL,
  `landscape_img` varchar(255) NOT NULL,
  `ogtag_img` varchar(255) NOT NULL,
  `total_play` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification`
--

CREATE TABLE `tbl_notification` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification_configuration`
--

CREATE TABLE `tbl_notification_configuration` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `send_mail` int(11) NOT NULL,
  `send_notification` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_notification_configuration`
--

INSERT INTO `tbl_notification_configuration` (`id`, `type`, `send_mail`, `send_notification`, `status`, `created_at`, `updated_at`) VALUES
(1, 'register', 0, 0, 0, '2025-09-29 09:17:02', '2026-03-06 11:23:36'),
(2, 'login', 0, 0, 0, '2025-09-29 09:17:12', '2026-03-06 11:23:38'),
(3, 'add-radio-station', 0, 0, 0, '2025-09-29 09:17:34', '2026-03-06 11:23:45'),
(4, 'add-podcast', 0, 0, 0, '2025-09-29 09:17:44', '2026-03-06 11:23:48'),
(5, 'add-live-event', 0, 0, 0, '2025-09-29 09:17:54', '2026-03-06 11:23:50'),
(6, 'package-buy', 0, 0, 0, '2025-09-29 09:18:05', '2026-03-06 11:23:51'),
(7, 'package-expired', 0, 0, 0, '2025-09-29 09:18:16', '2026-03-06 11:23:53'),
(8, 'add-music', 0, 0, 0, '2026-01-27 05:00:19', '2026-03-06 11:23:55');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_onboarding_screen`
--

CREATE TABLE `tbl_onboarding_screen` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_package`
--

CREATE TABLE `tbl_package` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `android_product_package` varchar(255) NOT NULL,
  `ios_product_package` varchar(255) NOT NULL,
  `web_product_package` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `device_limit` int(11) NOT NULL DEFAULT 1,
  `is_download` int(11) NOT NULL DEFAULT 0 COMMENT '	0- No, 1- Yes	',
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

CREATE TABLE `tbl_page` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`id`, `title`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'About Us', '', '', 1, '2022-01-24 17:28:26', '2026-03-06 11:00:32'),
(2, 'Privacy Policy', '', '', 1, '2022-01-24 17:28:26', '2026-03-06 11:00:42'),
(3, 'Terms & Conditions', '', '', 1, '2022-01-24 17:28:37', '2026-03-06 11:00:41'),
(4, 'Refund Policy', '', '', 1, '2023-04-15 11:01:19', '2026-03-06 11:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment_option`
--

CREATE TABLE `tbl_payment_option` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `visibility` varchar(255) NOT NULL,
  `is_live` varchar(255) NOT NULL,
  `key_1` varchar(255) NOT NULL,
  `key_2` varchar(255) NOT NULL,
  `key_3` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_payment_option`
--

INSERT INTO `tbl_payment_option` (`id`, `name`, `visibility`, `is_live`, `key_1`, `key_2`, `key_3`, `created_at`, `updated_at`) VALUES
(1, 'inapppurchage_android', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:58:58'),
(2, 'paypal', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:20'),
(3, 'razorpay', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:17'),
(4, 'flutterwave', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:22'),
(5, 'payumoney', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:28'),
(6, 'paytm', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:06'),
(7, 'stripe', '0', '0', '', '', '', '2023-06-17 08:32:13', '2026-03-06 10:59:25'),
(8, 'inapppurchage_ios', '0', '0', '', '', '', '2023-01-27 10:19:52', '2026-03-06 10:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_play`
--

CREATE TABLE `tbl_play` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1-Song, 2-Podcast',
  `content_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_podcast`
--

CREATE TABLE `tbl_podcast` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `portrait_img` varchar(255) NOT NULL,
  `landscape_img` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `trailer_upload_type` int(11) NOT NULL COMMENT '1- Server Content, 2- External URL',
  `trailer_audio` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `is_premium` int(11) NOT NULL DEFAULT 0,
  `total_play` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_section`
--

CREATE TABLE `tbl_section` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '0- All',
  `section_type` int(11) NOT NULL COMMENT '1- Home, 2- Music, 3- Radio, 4- Podcast',
  `title` varchar(255) NOT NULL,
  `sub_title` varchar(255) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1-Song, 2-Podcast, 3-Live event, 4-Artist, 5-Category, 6-\r\nLanguage, 7-City, 8-Music',
  `artist_id` int(11) NOT NULL DEFAULT 0 COMMENT '0-All',
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '0-All',
  `language_id` int(11) NOT NULL DEFAULT 0 COMMENT '0-All',
  `city_id` int(11) NOT NULL DEFAULT 0 COMMENT '0-All',
  `screen_layout` varchar(255) NOT NULL,
  `is_premium` int(11) NOT NULL DEFAULT 0 COMMENT '0- No, 1- Yes',
  `order_by_upload` int(11) NOT NULL DEFAULT 1 COMMENT '0- Asc, 1- Desc',
  `order_by_play` int(11) NOT NULL DEFAULT 1 COMMENT '0- Asc, 1- Desc',
  `is_paid` int(11) NOT NULL DEFAULT 0 COMMENT '0- No, 1- Yes',
  `is_title` int(11) NOT NULL DEFAULT 0 COMMENT '0- No, 1- Yes',
  `is_category` int(11) NOT NULL DEFAULT 0 COMMENT '0- No, 1- Yes',
  `is_artist_name` int(11) NOT NULL DEFAULT 0 COMMENT '0- No, 1- Yes',
  `no_of_content` int(11) NOT NULL,
  `view_all` int(11) NOT NULL DEFAULT 0 COMMENT '0-No, 1-Yes',
  `sortable` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp_setting`
--

CREATE TABLE `tbl_smtp_setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `protocol` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_smtp_setting`
--

INSERT INTO `tbl_smtp_setting` (`id`, `protocol`, `host`, `port`, `user`, `pass`, `from_name`, `from_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'smtp123', 'smtp.gmail.com', '587', 'admin@admin.com', 'admin', 'DTRadio-Divinetech', 'admin@admin.com', 0, '2022-08-03 10:14:04', '2026-03-06 11:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_social_link`
--

CREATE TABLE `tbl_social_link` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_song`
--

CREATE TABLE `tbl_song` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `upload_type` int(11) NOT NULL COMMENT '	1- Server Content, 2- External URL	',
  `song_url` text NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `is_premium` int(11) NOT NULL DEFAULT 0,
  `total_play` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `artist_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction`
--

CREATE TABLE `tbl_transaction` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `price` varchar(255) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `expiry_date` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` int(11) NOT NULL COMMENT '	1- Male, 2- Female',
  `image` varchar(255) NOT NULL,
  `type` int(11) NOT NULL COMMENT '	1- OTP, 2- Goggle, 3- Apple, 4- Normal	',
  `device_type` int(11) NOT NULL DEFAULT 0,
  `device_token` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_action`
--

CREATE TABLE `tbl_user_action` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_type` int(11) NOT NULL COMMENT '1- Radio, 2- Podcast, 3- Music',
  `content_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `artist_id` varchar(255) NOT NULL,
  `action` int(11) NOT NULL COMMENT '1- Play',
  `time_spend` int(11) NOT NULL DEFAULT 0,
  `content_duration` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_notification_tracking`
--

CREATE TABLE `tbl_user_notification_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_summary`
--

CREATE TABLE `tbl_user_summary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_artist`
--
ALTER TABLE `tbl_artist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_batch`
--
ALTER TABLE `tbl_batch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_city`
--
ALTER TABLE `tbl_city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_episode`
--
ALTER TABLE `tbl_episode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_event_join_user`
--
ALTER TABLE `tbl_event_join_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_favorite`
--
ALTER TABLE `tbl_favorite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_follow`
--
ALTER TABLE `tbl_follow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_general_setting`
--
ALTER TABLE `tbl_general_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_language`
--
ALTER TABLE `tbl_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_live_event`
--
ALTER TABLE `tbl_live_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_music`
--
ALTER TABLE `tbl_music`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_notification_configuration`
--
ALTER TABLE `tbl_notification_configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_onboarding_screen`
--
ALTER TABLE `tbl_onboarding_screen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_package`
--
ALTER TABLE `tbl_package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_page`
--
ALTER TABLE `tbl_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_payment_option`
--
ALTER TABLE `tbl_payment_option`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_play`
--
ALTER TABLE `tbl_play`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_podcast`
--
ALTER TABLE `tbl_podcast`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_section`
--
ALTER TABLE `tbl_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp_setting`
--
ALTER TABLE `tbl_smtp_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_social_link`
--
ALTER TABLE `tbl_social_link`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_song`
--
ALTER TABLE `tbl_song`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_transaction`
--
ALTER TABLE `tbl_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_action`
--
ALTER TABLE `tbl_user_action`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_notification_tracking`
--
ALTER TABLE `tbl_user_notification_tracking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_summary`
--
ALTER TABLE `tbl_user_summary`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_artist`
--
ALTER TABLE `tbl_artist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_banner`
--
ALTER TABLE `tbl_banner`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_batch`
--
ALTER TABLE `tbl_batch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_city`
--
ALTER TABLE `tbl_city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_episode`
--
ALTER TABLE `tbl_episode`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_event_join_user`
--
ALTER TABLE `tbl_event_join_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_favorite`
--
ALTER TABLE `tbl_favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_follow`
--
ALTER TABLE `tbl_follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_general_setting`
--
ALTER TABLE `tbl_general_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `tbl_language`
--
ALTER TABLE `tbl_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_live_event`
--
ALTER TABLE `tbl_live_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_music`
--
ALTER TABLE `tbl_music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notification_configuration`
--
ALTER TABLE `tbl_notification_configuration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_onboarding_screen`
--
ALTER TABLE `tbl_onboarding_screen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_package`
--
ALTER TABLE `tbl_package`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_page`
--
ALTER TABLE `tbl_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_payment_option`
--
ALTER TABLE `tbl_payment_option`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_play`
--
ALTER TABLE `tbl_play`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_podcast`
--
ALTER TABLE `tbl_podcast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_section`
--
ALTER TABLE `tbl_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_smtp_setting`
--
ALTER TABLE `tbl_smtp_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_social_link`
--
ALTER TABLE `tbl_social_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_song`
--
ALTER TABLE `tbl_song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transaction`
--
ALTER TABLE `tbl_transaction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_action`
--
ALTER TABLE `tbl_user_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_notification_tracking`
--
ALTER TABLE `tbl_user_notification_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_summary`
--
ALTER TABLE `tbl_user_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
