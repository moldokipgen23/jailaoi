-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 09, 2025 at 08:47 AM
-- Server version: 8.0.43-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jailaoi_tube`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int UNSIGNED NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `user_name`, `email`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@admin.com', '$2y$10$knld0iB52/DjPK6fbP2jRu6N.LhJAQ.zki1rYEcOzDnZcwc/NYD9y', 1, '2023-11-06 16:27:31', '2025-08-28 12:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ads`
--

CREATE TABLE `tbl_ads` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `redirect_uri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` int NOT NULL COMMENT '1- Banner Ads, 2- Interstital Ads, 3- Reward Ads',
  `image_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '\r\n',
  `video_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `budget` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Inactive, 1- Active',
  `is_hide` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ads_view_click_count`
--

CREATE TABLE `tbl_ads_view_click_count` (
  `id` int UNSIGNED NOT NULL,
  `ads_type` int NOT NULL COMMENT '1- Banner Ads, 2- Interstital Ads, 3- Reward Ads',
  `ads_id` int NOT NULL,
  `device_type` int NOT NULL DEFAULT '0' COMMENT '1- Android, 2- IOS, 3- Web',
  `device_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_id` int NOT NULL DEFAULT '0',
  `type` int NOT NULL DEFAULT '0' COMMENT '1- CPV, 2- CPC',
  `total_coin` int NOT NULL DEFAULT '0',
  `admin_commission` int NOT NULL DEFAULT '0',
  `user_wallet_earning` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_badges_bonus`
--

CREATE TABLE `tbl_badges_bonus` (
  `id` int NOT NULL,
  `type` int NOT NULL COMMENT '0- Both, 1- Badges, 2- Bonus',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bonus_coin` int NOT NULL,
  `condition_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `x_number` int NOT NULL DEFAULT '0',
  `x_content` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_block_channel`
--

CREATE TABLE `tbl_block_channel` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `block_user_id` int NOT NULL,
  `block_channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_coin_package`
--

CREATE TABLE `tbl_coin_package` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `coin` int NOT NULL,
  `android_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ios_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `web_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_coin_transaction`
--

CREATE TABLE `tbl_coin_transaction` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `package_id` int NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `coin` int NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment`
--

CREATE TABLE `tbl_comment` (
  `id` int UNSIGNED NOT NULL,
  `comment_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '	1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content`
--

CREATE TABLE `tbl_content` (
  `id` int UNSIGNED NOT NULL,
  `content_type` int NOT NULL COMMENT '1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio\r\n',
  `channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  `language_id` int NOT NULL,
  `hashtag_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `portrait_img_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `portrait_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `landscape_img_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `landscape_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `content_upload_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'server_video, external_url, youtube',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_duration` int NOT NULL DEFAULT '0',
  `is_rent` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes',
  `rent_price` int NOT NULL DEFAULT '0',
  `rent_day` int NOT NULL DEFAULT '0',
  `is_comment` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `is_download` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `is_like` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `total_view` int NOT NULL DEFAULT '0',
  `total_like` int NOT NULL DEFAULT '0',
  `total_dislike` int NOT NULL DEFAULT '0',
  `playlist_type` int NOT NULL DEFAULT '0' COMMENT '1- Public, 2- Private',
  `total_watch_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT 'In Second',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content_like`
--

CREATE TABLE `tbl_content_like` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '	1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `status` int NOT NULL COMMENT '0- Remove, 1- Like, 2- Dislike',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content_report`
--

CREATE TABLE `tbl_content_report` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `report_user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '	1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content_view`
--

CREATE TABLE `tbl_content_view` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_episode`
--

CREATE TABLE `tbl_episode` (
  `id` int UNSIGNED NOT NULL,
  `podcasts_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `portrait_img_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `portrait_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `landscape_img_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `landscape_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `episode_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `episode_upload_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'server_audio, external_url',
  `episode_audio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_comment` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `is_download` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `is_like` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `total_view` int NOT NULL DEFAULT '0',
  `total_like` int NOT NULL DEFAULT '0',
  `total_dislike` int NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feed`
--

CREATE TABLE `tbl_feed` (
  `id` int UNSIGNED NOT NULL,
  `channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtag_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_like` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes	',
  `is_comment` int NOT NULL DEFAULT '0' COMMENT '	0- No, 1- Yes',
  `total_like` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feed_comment`
--

CREATE TABLE `tbl_feed_comment` (
  `id` int UNSIGNED NOT NULL,
  `comment_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL,
  `feed_id` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feed_content`
--

CREATE TABLE `tbl_feed_content` (
  `id` int UNSIGNED NOT NULL,
  `feed_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '1-Image, 2-Video',
  `image_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `video_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feed_like`
--

CREATE TABLE `tbl_feed_like` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `feed_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feed_report`
--

CREATE TABLE `tbl_feed_report` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `report_user_id` int NOT NULL,
  `feed_id` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_general_setting`
--

CREATE TABLE `tbl_general_setting` (
  `id` int UNSIGNED NOT NULL,
  `key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_general_setting`
--

INSERT INTO `tbl_general_setting` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'JailaOi', '2023-04-21 05:09:12', '2025-06-03 13:37:02'),
(2, 'app_version', '1.4', '2023-04-21 05:09:12', '2025-10-09 08:41:13'),
(3, 'app_logo', '', '2023-04-21 05:09:12', '2025-10-09 08:41:16'),
(4, 'app_description', 'DivineTechs, a top web & mobile app development company offering innovative solutions for diverse industry verticals. \r\nWe have creative and dedicated group of developers who are mastered in Apps Developments and Web Development with a nice in delivering quality solutions to customers across the globe.', '2023-04-21 05:09:12', '2025-10-06 10:27:37'),
(5, 'author', 'DivineTechs', '2023-04-21 05:09:12', '2025-08-29 11:22:29'),
(6, 'email', 'support@divinetechs.com', '2023-04-21 05:09:12', '2025-08-29 11:22:32'),
(7, 'contact', '917984798190', '2023-04-21 05:09:12', '2025-10-09 08:41:37'),
(8, 'website', 'https://www.divinetechs.com/', '2023-04-21 05:09:12', '2025-10-09 08:41:46'),
(9, 'currency', 'USD', '2023-04-21 05:09:12', '2025-06-03 13:54:38'),
(10, 'currency_code', '$', '2023-04-21 05:09:12', '2025-06-03 13:54:38'),
(11, 'admob_status', '0', '2023-04-21 05:09:12', '2025-06-04 13:39:21'),
(12, 'banner_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:39:15'),
(13, 'banner_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:39:05'),
(14, 'interstital_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:39:15'),
(15, 'interstital_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:39:05'),
(16, 'interstital_adclick', '', '2023-04-21 05:09:12', '2025-06-04 13:39:05'),
(17, 'reward_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:39:15'),
(18, 'reward_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:39:05'),
(19, 'reward_adclick', '', '2023-04-21 05:09:12', '2025-06-04 13:39:05'),
(20, 'ios_banner_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:44:24'),
(21, 'ios_banner_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:44:33'),
(22, 'ios_interstital_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:44:24'),
(23, 'ios_interstital_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:44:33'),
(24, 'ios_interstital_adclick', '', '2023-04-21 05:09:12', '2025-06-04 13:44:33'),
(25, 'ios_reward_ad', '0', '2023-04-21 05:09:12', '2025-06-04 13:44:24'),
(26, 'ios_reward_adid', '', '2023-04-21 05:09:12', '2025-06-04 13:44:33'),
(27, 'ios_reward_adclick', '', '2023-04-21 05:09:12', '2025-06-04 13:44:33'),
(28, 'facebook_ads_status', '0', '2023-04-21 05:09:12', '2025-10-09 08:41:52'),
(29, 'fb_native_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(30, 'fb_native_id', '', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(31, 'fb_banner_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(32, 'fb_banner_id', '', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(33, 'fb_interstiatial_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(34, 'fb_interstiatial_id', '', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(35, 'fb_rewardvideo_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(36, 'fb_rewardvideo_id', '', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(37, 'fb_native_full_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(38, 'fb_native_full_id', '', '2023-04-21 05:09:12', '2025-06-05 04:40:13'),
(39, 'fb_ios_native_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(40, 'fb_ios_native_id', '', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(41, 'fb_ios_banner_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(42, 'fb_ios_banner_id', '', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(43, 'fb_ios_interstiatial_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(44, 'fb_ios_interstiatial_id', '', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(45, 'fb_ios_rewardvideo_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(46, 'fb_ios_rewardvideo_id', '', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(47, 'fb_ios_native_full_status', '0', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(48, 'fb_ios_native_full_id', '', '2023-04-21 05:09:12', '2025-06-05 04:39:57'),
(49, 'onesignal_app_id', '', '2023-04-21 05:09:12', '2025-06-09 10:29:41'),
(50, 'onesignal_rest_key', '', '2023-04-21 05:09:12', '2025-06-09 10:29:41'),
(51, 'page_background_color', '#2923230', '2024-10-28 11:30:11', '2025-10-09 08:47:03'),
(52, 'page_title_color', '#ffffff', '2024-10-28 11:30:11', '2025-05-23 12:17:29'),
(53, 'panel_login_page_view', '0', '2025-03-04 10:01:53', '2025-10-09 08:44:33'),
(54, 'panel_login_page_bg_image', '', '2025-03-04 10:02:37', '2025-10-09 08:44:31'),
(55, 'panel_login_page_bg_color', '#000000', '2025-03-04 10:02:37', '2025-06-11 13:19:53'),
(56, 'panel_login_page_image', '', '2025-03-04 10:02:55', '2025-06-11 13:19:53'),
(57, 'is_live_streaming_fake', '0', '2024-11-27 11:21:19', '2025-06-04 06:41:17'),
(58, 'live_appid', '', '2023-10-17 10:35:28', '2025-06-04 06:41:08'),
(59, 'live_appsign', '', '2023-10-17 10:35:28', '2025-06-04 06:41:08'),
(60, 'live_serversecret', '', '2023-10-17 10:35:32', '2025-06-04 06:41:08'),
(61, 'deepar_android_key', '', '2024-11-27 11:21:12', '2025-06-04 07:23:43'),
(62, 'deepar_ios_key', '', '2024-11-27 11:21:12', '2025-06-04 07:23:43'),
(63, 'vap_id_key', '', '2024-07-26 10:18:23', '2025-06-04 05:12:24'),
(64, 'banner_ads_status', '0', '2024-03-27 05:37:49', '2025-06-04 12:40:54'),
(65, 'banner_ads_cpv', '0', '2024-03-27 05:37:49', '2025-10-09 08:42:42'),
(66, 'banner_ads_cpc', '0', '2024-03-27 05:38:05', '2025-10-09 08:42:42'),
(67, 'interstital_ads_status', '0', '2024-03-27 05:38:05', '2025-06-04 12:40:51'),
(68, 'interstital_ads_cpv', '0', '2024-03-27 05:38:26', '2025-10-09 08:42:44'),
(69, 'interstital_ads_cpc', '0', '2024-03-27 05:38:26', '2025-10-09 08:42:44'),
(70, 'reward_ads_status', '0', '2024-03-27 05:38:44', '2025-06-04 12:40:57'),
(71, 'reward_ads_cpv', '0', '2024-03-27 05:38:44', '2025-10-09 08:42:46'),
(72, 'reward_ads_cpc', '0', '2024-03-27 05:39:01', '2025-10-09 08:42:46'),
(73, 'ads_commission', '0', '2024-03-27 05:39:01', '2025-10-09 08:43:48'),
(74, 'rent_commission', '0', '2023-12-11 13:37:36', '2025-10-09 08:43:48'),
(75, 'min_withdrawal_amount', '0', '2024-03-27 05:39:09', '2025-10-09 08:44:20'),
(76, 'refer_and_earn_status', '0', '2025-05-21 09:29:04', '2025-10-09 08:43:57'),
(77, 'parent_user_earn', '0', '2025-05-21 09:29:04', '2025-06-04 11:21:07'),
(78, 'child_user_earn', '0', '2025-05-21 09:29:18', '2025-06-04 11:21:07'),
(79, 'app_logo_storage_type', '1', '2025-06-11 12:25:34', '2025-10-06 10:27:37'),
(80, 'panel_login_page_bg_image_storage_type', '1', '2025-06-11 12:25:34', '2025-08-01 05:45:06'),
(81, 'panel_login_page_image_storage_type', '1', '2025-06-11 12:25:34', '2025-07-15 13:34:54'),
(82, 'playstore_id', '', '2025-08-27 10:22:09', '2025-08-28 06:01:53'),
(83, 'appstore_id', '', '2025-08-27 10:22:09', '2025-08-28 06:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gift`
--

CREATE TABLE `tbl_gift` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gift_transaction`
--

CREATE TABLE `tbl_gift_transaction` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `gift_id` int NOT NULL DEFAULT '0',
  `coin` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hashtag`
--

CREATE TABLE `tbl_hashtag` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_used` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_history`
--

CREATE TABLE `tbl_history` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `stop_time` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_interests`
--

CREATE TABLE `tbl_interests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `category_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hashtag_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_language`
--

CREATE TABLE `tbl_language` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_live_history`
--

CREATE TABLE `tbl_live_history` (
  `id` int UNSIGNED NOT NULL,
  `room_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `total_gift` int NOT NULL DEFAULT '0',
  `total_join_user` int NOT NULL DEFAULT '0',
  `total_live_chat` int NOT NULL DEFAULT '0',
  `start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `end_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `duration` int NOT NULL DEFAULT '0' COMMENT 'In Second',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_live_user`
--

CREATE TABLE `tbl_live_user` (
  `id` int UNSIGNED NOT NULL,
  `room_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `total_view` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0' COMMENT '0- Not Live, 1- Live	',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notification`
--

CREATE TABLE `tbl_notification` (
  `id` int UNSIGNED NOT NULL,
  `type` int NOT NULL COMMENT '1- Admin, 2- Like, 3- Comment, 4- Subscribe',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `from_user_id` int NOT NULL,
  `content_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_onboarding_screen`
--

CREATE TABLE `tbl_onboarding_screen` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_package`
--

CREATE TABLE `tbl_package` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ads_free` int NOT NULL,
  `download_content` int NOT NULL,
  `background_play` int NOT NULL,
  `android_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ios_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `web_product_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_package_detail`
--

CREATE TABLE `tbl_package_detail` (
  `id` int UNSIGNED NOT NULL,
  `package_id` int NOT NULL,
  `package_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `package_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

CREATE TABLE `tbl_page` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`id`, `title`, `description`, `storage_type`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'About Us', '', 1, '', 1, '2023-04-21 05:09:12', '2025-10-09 08:45:07'),
(2, 'Privacy Policy', '', 1, '', 1, '2023-04-21 05:09:12', '2025-10-09 08:45:08'),
(3, 'Terms & Conditions', '', 1, '', 1, '2023-04-21 05:09:12', '2025-10-09 08:45:09'),
(4, 'Refund Policy', '', 1, '', 1, '2023-04-21 05:09:12', '2025-10-09 08:45:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment_option`
--

CREATE TABLE `tbl_payment_option` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_live` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_payment_option`
--

INSERT INTO `tbl_payment_option` (`id`, `name`, `visibility`, `is_live`, `key_1`, `key_2`, `key_3`, `key_4`, `created_at`, `updated_at`) VALUES
(1, 'inapppurchage', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:21'),
(2, 'paypal', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:31'),
(3, 'razorpay', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:30'),
(4, 'flutterwave', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:29'),
(5, 'payumoney', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:29'),
(6, 'paytm', '0', '0', '', '', '', '', '2023-04-21 05:09:13', '2025-06-03 10:32:28'),
(7, 'stripe', '0', '0', '', '', '', '', '2023-07-14 13:04:49', '2025-06-03 10:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_playlist_content`
--

CREATE TABLE `tbl_playlist_content` (
  `id` int NOT NULL,
  `channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `playlist_id` int NOT NULL COMMENT 'FK tbl_content-id',
  `content_type` int NOT NULL COMMENT '	1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_read_notification`
--

CREATE TABLE `tbl_read_notification` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `notification_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_refer_earn`
--

CREATE TABLE `tbl_refer_earn` (
  `id` int UNSIGNED NOT NULL,
  `parent_user_id` int NOT NULL,
  `reference_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `child_user_id` int NOT NULL,
  `parent_earn` int NOT NULL DEFAULT '0',
  `child_earn` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rent_section`
--

CREATE TABLE `tbl_rent_section` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL,
  `no_of_content` int NOT NULL DEFAULT '1',
  `view_all` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide,1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rent_transaction`
--

CREATE TABLE `tbl_rent_transaction` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `content_id` int NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_commission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `user_wallet_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiry_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Expiry, 1- Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report_reason`
--

CREATE TABLE `tbl_report_reason` (
  `id` int UNSIGNED NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_section`
--

CREATE TABLE `tbl_section` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `short_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_home_screen` int NOT NULL DEFAULT '1' COMMENT '1- home screen, 2- other screen',
  `content_type` int NOT NULL COMMENT '	1- Music, 2- Podcasts, 3- Radio, 4- Playlist, 5- Category, 6- Language	',
  `category_id` int NOT NULL,
  `language_id` int NOT NULL,
  `order_by_view` int NOT NULL DEFAULT '0' COMMENT '1- ASC, 2- DESC',
  `order_by_like` int NOT NULL DEFAULT '0' COMMENT '1- ASC, 2- DESC',
  `order_by_upload` int NOT NULL DEFAULT '0' COMMENT '1- ASC, 2- DESC',
  `screen_layout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_of_content` int NOT NULL DEFAULT '0' COMMENT '0- All',
  `view_all` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_send_gift`
--

CREATE TABLE `tbl_send_gift` (
  `id` int UNSIGNED NOT NULL,
  `gift_id` int NOT NULL,
  `user_id` int NOT NULL,
  `channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content_id` int NOT NULL,
  `price` int NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp_setting`
--

CREATE TABLE `tbl_smtp_setting` (
  `id` int UNSIGNED NOT NULL,
  `protocol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0- OFF, 1- ON',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_smtp_setting`
--

INSERT INTO `tbl_smtp_setting` (`id`, `protocol`, `host`, `port`, `user`, `pass`, `from_name`, `from_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'smtp123', 'smtp.gmail.com', '587', 'admin@admin.com', 'admin', 'JailaOi-DivineTechs', 'admin@admin.com', 0, '2023-08-26 06:19:33', '2025-10-09 08:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_social_link`
--

CREATE TABLE `tbl_social_link` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_storage_setting`
--

CREATE TABLE `tbl_storage_setting` (
  `id` int UNSIGNED NOT NULL,
  `storage_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '1-Local, 2- AWS S3',
  `s3_access_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `s3_secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `s3_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `s3_bucket_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `s3_endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_storage_setting`
--

INSERT INTO `tbl_storage_setting` (`id`, `storage_type`, `s3_access_key`, `s3_secret_key`, `s3_region`, `s3_bucket_name`, `s3_endpoint`, `status`, `created_at`, `updated_at`) VALUES
(1, '1', '', '', '', '', '', 1, '2025-04-16 13:10:50', '2025-09-24 09:23:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subscriber`
--

CREATE TABLE `tbl_subscriber` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction`
--

CREATE TABLE `tbl_transaction` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `package_id` int NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '	0- Expiry, 1- Active	',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int UNSIGNED NOT NULL,
  `channel_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `channel_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` int NOT NULL DEFAULT '0' COMMENT '1- OTP, 2- Google, 3- Apple, 4- Normal',
  `image_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cover_img_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `cover_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `device_type` int NOT NULL DEFAULT '0' COMMENT '1- Android, 2- IOS, 3- Web',
  `device_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `facebook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `instagram_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `twitter_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `wallet_balance` int NOT NULL DEFAULT '0' COMMENT 'Coin Balance',
  `wallet_earning` int NOT NULL DEFAULT '0' COMMENT 'Coin Earning',
  `is_account_verify` int NOT NULL DEFAULT '0' COMMENT '0- No, 1- Yes',
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bank_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bank_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ifsc_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `front_id_proof_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `front_id_proof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `back_id_proof_storage_type` int NOT NULL DEFAULT '0' COMMENT '1- Local Storage, 2- AWS S3 Storage	',
  `back_id_proof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pincode` int NOT NULL,
  `user_penal_status` int NOT NULL DEFAULT '0' COMMENT '0- OFF, 1- ON',
  `reference_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `push_notification_status` int NOT NULL DEFAULT '1' COMMENT '0- OFF, 1- ON',
  `send_mail_status` int NOT NULL DEFAULT '1' COMMENT '0- OFF, 1- ON',
  `status` int NOT NULL DEFAULT '1' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_badges_bonus`
--

CREATE TABLE `tbl_user_badges_bonus` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `badges_bonus_id` int NOT NULL,
  `reward_coin` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0' COMMENT '0- Hide, 1- Show',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_watch_later`
--

CREATE TABLE `tbl_watch_later` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `content_type` int NOT NULL COMMENT '	1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	',
  `content_id` int NOT NULL,
  `episode_id` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_withdrawal_request`
--

CREATE TABLE `tbl_withdrawal_request` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `amount` int NOT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0 - Pending, 1- Completed',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_ads`
--
ALTER TABLE `tbl_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_ads_view_click_count`
--
ALTER TABLE `tbl_ads_view_click_count`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_badges_bonus`
--
ALTER TABLE `tbl_badges_bonus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_block_channel`
--
ALTER TABLE `tbl_block_channel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_coin_package`
--
ALTER TABLE `tbl_coin_package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_coin_transaction`
--
ALTER TABLE `tbl_coin_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_content`
--
ALTER TABLE `tbl_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_content_like`
--
ALTER TABLE `tbl_content_like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_content_report`
--
ALTER TABLE `tbl_content_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_content_view`
--
ALTER TABLE `tbl_content_view`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_episode`
--
ALTER TABLE `tbl_episode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feed`
--
ALTER TABLE `tbl_feed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feed_comment`
--
ALTER TABLE `tbl_feed_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feed_content`
--
ALTER TABLE `tbl_feed_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feed_like`
--
ALTER TABLE `tbl_feed_like`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_feed_report`
--
ALTER TABLE `tbl_feed_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_general_setting`
--
ALTER TABLE `tbl_general_setting`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `tbl_gift`
--
ALTER TABLE `tbl_gift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gift_transaction`
--
ALTER TABLE `tbl_gift_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_hashtag`
--
ALTER TABLE `tbl_hashtag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_history`
--
ALTER TABLE `tbl_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_interests`
--
ALTER TABLE `tbl_interests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_language`
--
ALTER TABLE `tbl_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_live_history`
--
ALTER TABLE `tbl_live_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_live_user`
--
ALTER TABLE `tbl_live_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
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
-- Indexes for table `tbl_package_detail`
--
ALTER TABLE `tbl_package_detail`
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
-- Indexes for table `tbl_playlist_content`
--
ALTER TABLE `tbl_playlist_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_read_notification`
--
ALTER TABLE `tbl_read_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_refer_earn`
--
ALTER TABLE `tbl_refer_earn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rent_section`
--
ALTER TABLE `tbl_rent_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rent_transaction`
--
ALTER TABLE `tbl_rent_transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report_reason`
--
ALTER TABLE `tbl_report_reason`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_section`
--
ALTER TABLE `tbl_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_send_gift`
--
ALTER TABLE `tbl_send_gift`
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
-- Indexes for table `tbl_storage_setting`
--
ALTER TABLE `tbl_storage_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subscriber`
--
ALTER TABLE `tbl_subscriber`
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
-- Indexes for table `tbl_user_badges_bonus`
--
ALTER TABLE `tbl_user_badges_bonus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_watch_later`
--
ALTER TABLE `tbl_watch_later`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_withdrawal_request`
--
ALTER TABLE `tbl_withdrawal_request`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_ads`
--
ALTER TABLE `tbl_ads`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_ads_view_click_count`
--
ALTER TABLE `tbl_ads_view_click_count`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_badges_bonus`
--
ALTER TABLE `tbl_badges_bonus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_block_channel`
--
ALTER TABLE `tbl_block_channel`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_coin_package`
--
ALTER TABLE `tbl_coin_package`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_coin_transaction`
--
ALTER TABLE `tbl_coin_transaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_content`
--
ALTER TABLE `tbl_content`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_content_like`
--
ALTER TABLE `tbl_content_like`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_content_report`
--
ALTER TABLE `tbl_content_report`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_content_view`
--
ALTER TABLE `tbl_content_view`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_episode`
--
ALTER TABLE `tbl_episode`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_feed`
--
ALTER TABLE `tbl_feed`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_feed_comment`
--
ALTER TABLE `tbl_feed_comment`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_feed_content`
--
ALTER TABLE `tbl_feed_content`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_feed_like`
--
ALTER TABLE `tbl_feed_like`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_feed_report`
--
ALTER TABLE `tbl_feed_report`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_general_setting`
--
ALTER TABLE `tbl_general_setting`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `tbl_gift`
--
ALTER TABLE `tbl_gift`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_gift_transaction`
--
ALTER TABLE `tbl_gift_transaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_hashtag`
--
ALTER TABLE `tbl_hashtag`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_history`
--
ALTER TABLE `tbl_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_interests`
--
ALTER TABLE `tbl_interests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_language`
--
ALTER TABLE `tbl_language`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_live_history`
--
ALTER TABLE `tbl_live_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_live_user`
--
ALTER TABLE `tbl_live_user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notification`
--
ALTER TABLE `tbl_notification`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_onboarding_screen`
--
ALTER TABLE `tbl_onboarding_screen`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_package`
--
ALTER TABLE `tbl_package`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_package_detail`
--
ALTER TABLE `tbl_package_detail`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_page`
--
ALTER TABLE `tbl_page`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_payment_option`
--
ALTER TABLE `tbl_payment_option`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_playlist_content`
--
ALTER TABLE `tbl_playlist_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_read_notification`
--
ALTER TABLE `tbl_read_notification`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_refer_earn`
--
ALTER TABLE `tbl_refer_earn`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rent_section`
--
ALTER TABLE `tbl_rent_section`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rent_transaction`
--
ALTER TABLE `tbl_rent_transaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report_reason`
--
ALTER TABLE `tbl_report_reason`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_section`
--
ALTER TABLE `tbl_section`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_send_gift`
--
ALTER TABLE `tbl_send_gift`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_smtp_setting`
--
ALTER TABLE `tbl_smtp_setting`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_social_link`
--
ALTER TABLE `tbl_social_link`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_storage_setting`
--
ALTER TABLE `tbl_storage_setting`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_subscriber`
--
ALTER TABLE `tbl_subscriber`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transaction`
--
ALTER TABLE `tbl_transaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_badges_bonus`
--
ALTER TABLE `tbl_user_badges_bonus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_watch_later`
--
ALTER TABLE `tbl_watch_later`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_withdrawal_request`
--
ALTER TABLE `tbl_withdrawal_request`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
