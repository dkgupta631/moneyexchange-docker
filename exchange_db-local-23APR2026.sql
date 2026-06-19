-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 06:38 AM
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
-- Database: `exchange_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1776852369),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1776852369;', 1776852369),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1776852779),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1776852779;', 1776852779);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `from_currency` varchar(255) DEFAULT NULL,
  `to_currency` varchar(255) DEFAULT NULL,
  `buy_or_sell` varchar(255) DEFAULT NULL,
  `normal_rate` varchar(255) DEFAULT NULL,
  `standard_rate` varchar(255) DEFAULT NULL,
  `rate_date` varchar(255) DEFAULT NULL,
  `ordering` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `section`, `from_currency`, `to_currency`, `buy_or_sell`, `normal_rate`, `standard_rate`, `rate_date`, `ordering`, `created_at`, `updated_at`) VALUES
(1, 'left', 'Dollar', 'Riel', 'sell', '4012', NULL, '2026-03-10', '1', '2026-03-09 23:55:37', '2026-03-09 23:55:37'),
(2, 'left', 'Dollar', 'Baht', 'sell', '31.93', '31.93', '2026-03-18', '3', '2026-03-10 00:01:06', '2026-03-17 23:23:26'),
(3, 'left', 'Baht', 'Riel', 'sell', '125.1', '123.8', '2026-03-10', '5', '2026-03-10 00:01:46', '2026-03-17 21:10:49'),
(4, 'left', 'Riel', 'Dollar', 'buy', '4018', NULL, '2026-03-10', '2', '2026-03-10 00:02:33', '2026-03-17 21:07:37'),
(5, 'left', 'Baht', 'Dollar', 'buy', '32.08', '32.43', '2026-03-10', '4', '2026-03-10 00:03:13', '2026-03-17 21:10:04'),
(6, 'left', 'Riel', 'Baht', 'buy', '125.8', '125.8', '2026-03-10', '6', '2026-03-10 00:04:04', '2026-03-17 21:11:24');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"ef96c59f-e5ce-4b70-9d53-31f44dfd92d9\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:29:\\\"Your account is not verified.\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:29:\\\"heroicon-o-exclamation-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"warning\\\";s:6:\\\"status\\\";s:7:\\\"warning\\\";s:5:\\\"title\\\";s:14:\\\"Access Denied!\\\";s:4:\\\"view\\\";s:36:\\\"filament-notifications::notification\\\";s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"b490e328-2125-4dc2-ba6e-0a180a174787\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1773913406,\"delay\":null}', 0, NULL, 1773913406, 1773913406),
(2, 'default', '{\"uuid\":\"e6e256d4-4132-45b8-9d7d-9330c8f71cf5\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:9;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:29:\\\"Your account is not verified.\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:29:\\\"heroicon-o-exclamation-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"warning\\\";s:6:\\\"status\\\";s:7:\\\"warning\\\";s:5:\\\"title\\\";s:14:\\\"Access Denied!\\\";s:4:\\\"view\\\";s:36:\\\"filament-notifications::notification\\\";s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"3813b5ca-027b-4201-96c7-ab29811813cb\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1773913439,\"delay\":null}', 0, NULL, 1773913439, 1773913439);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `order`, `status`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 0, 1, 'images/flages/01KK3QC91YEFATVJM65G935YRD.png', NULL, '2026-03-07 01:42:14'),
(2, 'Thai', 'th-TH', 1, 1, 'images/flages/01KK3QHGKMSWKK47G2TWNCCV3D.png', NULL, '2026-03-07 01:45:06'),
(3, 'Khmer', 'km', 2, 1, 'images/flages/01KK3QJ8JT1HAB5VPDR3V7ZEWG.png', NULL, '2026-03-07 01:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_02_032809_create_exchange_rates_table', 2),
(5, '2026_03_05_080802_create_invoices_table', 3),
(6, '2026_03_07_083037_create_languages_table', 4),
(7, '2026_03_10_063022_create_exchange_rates_table', 5),
(8, '2026_03_11_090307_create_money_exchange_invoices_table', 6),
(9, '2026_03_13_041337_create_money_transfer_invoices_table', 7),
(10, '2026_03_13_042931_create_money_transfer_charges_table', 8),
(11, '2026_03_19_092226_create_notifications_table', 9),
(12, '2026_03_20_042625_create_money_transfer_invoices_table', 10),
(13, '2026_03_20_062710_create_money_transfer_invoices_table', 11),
(14, '2026_03_24_092803_create_money_transfer_invoices_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `money_exchange_invoices`
--

CREATE TABLE `money_exchange_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `exchange_rate_id` varchar(255) DEFAULT NULL,
  `from_currency` varchar(255) DEFAULT NULL,
  `to_currency` varchar(255) DEFAULT NULL,
  `exchange_type` varchar(255) DEFAULT NULL,
  `exchange_rate` varchar(255) DEFAULT NULL,
  `where_to_send` varchar(255) DEFAULT NULL,
  `entered_amount` varchar(255) DEFAULT NULL,
  `subtotal` varchar(255) DEFAULT NULL,
  `service_fee` varchar(255) DEFAULT NULL,
  `final_amount` varchar(255) DEFAULT NULL,
  `receive_type` varchar(255) DEFAULT NULL,
  `invoice_slip` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `money_exchange_invoices`
--

INSERT INTO `money_exchange_invoices` (`id`, `invoice_number`, `customer_name`, `phone`, `exchange_rate_id`, `from_currency`, `to_currency`, `exchange_type`, `exchange_rate`, `where_to_send`, `entered_amount`, `subtotal`, `service_fee`, `final_amount`, `receive_type`, `invoice_slip`, `created_at`, `updated_at`) VALUES
(6, '#E11032026164344', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Bank to Bank', NULL, '2026-03-11 02:43:44', '2026-03-11 02:43:44'),
(7, '#E11032026182711', 'ghfh', 'fghfh', '4', 'Riel', 'Dollar', 'Normal', '4020', 'TRF-IN', '10000', '2.49', '0.1', '2.49', 'Cash to Cash', NULL, '2026-03-11 04:27:11', '2026-03-11 04:27:11'),
(8, '#E12032026141616', 'DK', '69861400', '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '1', '31.06', '0.1', '31.06', 'Cash to Cash', NULL, '2026-03-12 00:16:16', '2026-03-12 00:16:16'),
(9, '#E12032026150033', 'dfd', '435464', '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 01:00:33', '2026-03-12 01:00:33'),
(10, '#E12032026150118', 'dfd', '435464', '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 01:01:18', '2026-03-12 01:01:18'),
(11, '#E12032026150141', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 01:01:41', '2026-03-12 01:01:41'),
(12, '#E12032026150200', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 01:02:00', '2026-03-12 01:02:00'),
(13, '#E12032026150311', 'dilip kumar', '69861400', '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '120', '3.85', '0.1', '3.85', 'Cash to Cash', NULL, '2026-03-12 01:03:11', '2026-03-12 01:03:11'),
(14, '#E12032026151059', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '1', '31.06', '0.1', '31.06', 'Cash to Cash', NULL, '2026-03-12 01:10:59', '2026-03-12 01:10:59'),
(15, '#E12032026163110', 'dk gupta', '69861400', '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '100', '3106.00', '0.1', '3106.00', 'Cash to Cash', NULL, '2026-03-12 02:31:10', '2026-03-12 02:31:10'),
(16, '#E12032026164412', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '200', '6.42', '0.1', '6.42', 'Cash to Cash', NULL, '2026-03-12 02:44:12', '2026-03-12 02:44:12'),
(17, '#E12032026183126', NULL, NULL, '4', 'Riel', 'Dollar', 'Standard', '4020', 'TRF-OUT', '100000', '24.88', '0.1', '24.88', 'Cash to Bank', NULL, '2026-03-12 04:31:26', '2026-03-12 04:31:26'),
(18, '#E12032026184810', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 04:48:10', '2026-03-12 04:48:10'),
(19, '#E12032026185951', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 04:59:51', '2026-03-12 04:59:51'),
(20, '#E12032026190207', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 05:02:07', '2026-03-12 05:02:07'),
(21, '#E12032026190224', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 05:02:24', '2026-03-12 05:02:24'),
(22, '#E12032026190343', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '100', '3106.00', '0.1', '3106.00', 'Cash to Cash', NULL, '2026-03-12 05:03:43', '2026-03-12 05:03:43'),
(23, '#E12032026190502', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '100', '3106.00', '0.1', '3106.00', 'Cash to Cash', NULL, '2026-03-12 05:05:02', '2026-03-12 05:05:02'),
(24, '#E13032026102450', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 20:24:50', '2026-03-12 20:24:50'),
(25, '#E13032026102920', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '12', '372.72', '0.1', '372.72', 'Cash to Cash', NULL, '2026-03-12 20:29:20', '2026-03-12 20:29:20'),
(26, '#E13032026102943', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 20:29:43', '2026-03-12 20:29:43'),
(27, '#E13032026105017', NULL, NULL, '6', 'Riel', 'Baht', 'Normal', '129.3', 'TRF-IN', '10000', '77.34', '0.1', '77.34', 'Cash to Cash', NULL, '2026-03-12 20:50:17', '2026-03-12 20:50:17'),
(28, '#E13032026133635', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-12 23:36:35', '2026-03-12 23:36:35'),
(29, '#E13032026140849', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '100', '3106.00', '0.1', '3106.00', 'Cash to Cash', NULL, '2026-03-13 00:08:49', '2026-03-13 00:08:49'),
(30, '#E13032026143305', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.1', '3.21', 'Cash to Cash', NULL, '2026-03-13 00:33:05', '2026-03-13 00:33:05'),
(31, '#E13032026151213', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '133', '4.27', '0.1', '4.27', 'Cash to Cash', NULL, '2026-03-13 01:12:13', '2026-03-13 01:12:13'),
(32, '#E130326151308', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '1000', '32.11', '0.1', '32.11', 'Cash to Cash', NULL, '2026-03-13 01:13:08', '2026-03-13 01:13:08'),
(33, '#E130326152153', NULL, NULL, '6', 'Riel', 'Baht', 'Normal', '129.3', 'TRF-IN', '10000', '77.34', '0.00', '77.34', 'Cash to Cash', NULL, '2026-03-13 01:21:53', '2026-03-13 01:21:53'),
(34, '#E130326153358', NULL, NULL, '5', 'Baht', 'Dollar', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.00', '3.21', 'Cash to Cash', NULL, '2026-03-13 01:33:58', '2026-03-13 01:33:58'),
(35, '#E130326154219', NULL, NULL, '2', 'Dollar', 'Baht', 'Normal', '31.06', 'TRF-IN', '123', '3820.38', '0.00', '3820.38', 'Cash to Cash', NULL, '2026-03-13 01:42:19', '2026-03-13 01:42:19'),
(36, '#E130326154552', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '31.14', 'TRF-IN', '100', '3.21', '0.00', '3.21', 'Cash to Cash', NULL, '2026-03-13 01:45:52', '2026-03-13 01:45:52'),
(37, '#E130326163718', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '31.14', 'TRF-IN', '3114.00', '100', '0.00', '100', 'Cash to Cash', NULL, '2026-03-13 02:37:18', '2026-03-13 02:37:18'),
(38, '#E130326163935', NULL, NULL, '6', 'Riel', 'Baht', 'Normal', '129.3', 'TRF-IN', '38789.76', '300', '0.00', '300', 'Cash to Cash', NULL, '2026-03-13 02:39:35', '2026-03-13 02:39:35'),
(39, '#E130326175256', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '100.00', '3106', '0.00', '3106', 'Cash to Cash', NULL, '2026-03-13 03:52:56', '2026-03-13 03:52:56'),
(40, '#E130326184935', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '31.14', 'TRF-IN', '31060.00', '997.43', '0.00', '997.43', 'Cash to Cash', NULL, '2026-03-13 04:49:35', '2026-03-13 04:49:35'),
(41, '#E140326111639', 'Dk', 'gupta', '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '100.00', '3106', '0.00', '3106', 'Cash to Cash', NULL, '2026-03-13 21:16:39', '2026-03-13 21:16:39'),
(42, '#E140326182740', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '1233', '38296.98', '0.00', '38296.98', 'Cash to Cash', NULL, '2026-03-14 04:27:40', '2026-03-14 04:27:40'),
(43, '#E140326184419', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '343', '10653.58', '0.00', '10653.58', 'Cash to Cash', NULL, '2026-03-14 04:44:19', '2026-03-14 04:44:19'),
(44, '#E150326161016', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '31.14', 'TRF-IN', '2500', '80.28', '0.00', '80.28', 'Cash to Cash', NULL, '2026-03-15 02:10:16', '2026-03-15 02:10:16'),
(45, '#E160326112311', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '31.14', 'TRF-IN', '31060.00', '997.43', '0.00', '997.43', 'Cash to Cash', NULL, '2026-03-15 21:23:11', '2026-03-15 21:23:11'),
(46, '#E160326175954', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '199', '6180.94', '0.00', '6180.94', 'Cash to Cash', NULL, '2026-03-16 03:59:54', '2026-03-16 03:59:54'),
(47, '#E160326180027', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '166', '5155.96', '0.00', '5155.96', 'Cash to Cash', NULL, '2026-03-16 04:00:27', '2026-03-16 04:00:27'),
(48, '#E160326180229', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '435345', '13521815.70', '0.00', '13521815.70', 'Cash to Cash', NULL, '2026-03-16 04:02:29', '2026-03-16 04:02:29'),
(49, '#E180326110530', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.06', 'TRF-IN', '1234', '38328.04', '0.00', '38328.04', 'Cash to Cash', NULL, '2026-03-17 21:05:30', '2026-03-17 21:05:30'),
(50, '#E180326132304', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '32.08', 'TRF-IN', '101475.78', '3163.21', '0.00', '3163.21', 'Cash to Cash', NULL, '2026-03-17 23:23:04', '2026-03-17 23:23:04'),
(51, '#E180326145802', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '125.27', '4000', '0.00', '4000', 'Cash to Cash', NULL, '2026-03-18 00:58:02', '2026-03-18 00:58:02'),
(52, '#E180326164430', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '233', '7439.69', '0.00', '7439.69', 'Cash to Cash', NULL, '2026-03-18 02:44:30', '2026-03-18 02:44:30'),
(53, '#E180326164526', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '333', '10632.69', '0.00', '10632.69', 'Cash to Cash', NULL, '2026-03-18 02:45:26', '2026-03-18 02:45:26'),
(54, '#E180326171611', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '100', '3193.00', '0.00', '3193.00', 'Cash to Cash', NULL, '2026-03-18 03:16:11', '2026-03-18 03:16:11'),
(55, '#E180326173605', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '100', '3193.00', '0.00', '3193.00', 'Cash to Cash', NULL, '2026-03-18 03:36:05', '2026-03-18 03:36:05'),
(56, '#E220326192148', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '199', '6354.07', '0.00', '6354.07', 'Cash to Cash', NULL, '2026-03-22 05:21:48', '2026-03-22 05:21:48'),
(57, '#E230326135021', NULL, NULL, '1', 'US $ ', 'Riel', 'Normal', '4012', 'TRF-IN', '100', '401200.00', '0.00', '401200.00', 'Cash to Cash', NULL, '2026-03-22 23:50:21', '2026-03-22 23:50:21'),
(58, '#E230326153254', NULL, NULL, '1', 'US $ ', 'Riel', 'Normal', '4012', 'TRF-IN', '100', '401200.00', '0.00', '401200.00', 'Cash to Cash', NULL, '2026-03-23 01:32:54', '2026-03-23 01:32:54'),
(59, '#E010426142854', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '100', '3193.00', '0.00', '3193.00', 'Cash to Cash', NULL, '2026-04-01 07:28:54', '2026-04-01 07:28:54'),
(60, '#E090426134803', NULL, NULL, '2', 'US $ ', 'Baht', 'Normal', '31.93', 'TRF-IN', '25', '798.25', '0.00', '798.25', 'Cash to Cash', NULL, '2026-04-09 06:48:03', '2026-04-09 06:48:03'),
(61, '#E130426104540', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '32.08', 'TRF-IN', '4000', '124.69', '0.00', '124.69', 'Cash to Cash', NULL, '2026-04-13 03:45:40', '2026-04-13 03:45:40'),
(62, '#E130426140521', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '32.08', 'TRF-IN', '1000', '31.17', '0.00', '31.17', 'Cash to Cash', NULL, '2026-04-13 07:05:21', '2026-04-13 07:05:21'),
(63, '#E180426142202', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '32.08', 'TRF-IN', '3193.00', '99.53', '0.00', '99.53', 'Cash to Cash', NULL, '2026-04-18 07:22:02', '2026-04-18 07:22:02'),
(64, '#E220426093428', NULL, NULL, '5', 'Baht', 'US $ ', 'Normal', '32.08', 'TRF-IN', '3193.00', '99.53', '0.00', '99.53', 'Cash to Cash', NULL, '2026-04-22 02:34:28', '2026-04-22 02:34:28'),
(65, '#E220426165116', NULL, NULL, '3', 'Baht', 'Riel', 'Normal', '125.1', 'TRF-IN', '5000', '625500.00', '0.00', '625500.00', 'Cash to Cash', NULL, '2026-04-22 09:51:16', '2026-04-22 09:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `money_transfer_charges`
--

CREATE TABLE `money_transfer_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transfer_type` varchar(255) DEFAULT NULL,
  `trf_fee_in_persentage` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `money_transfer_charges`
--

INSERT INTO `money_transfer_charges` (`id`, `transfer_type`, `trf_fee_in_persentage`, `created_at`, `updated_at`) VALUES
(1, 'little_amount', '2', '2026-03-13 04:32:28', '2026-03-13 04:32:28'),
(2, 'big_amount', '1', '2026-03-13 04:32:28', '2026-03-13 04:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `money_transfer_invoices`
--

CREATE TABLE `money_transfer_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `transfer_type` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `acc_name` varchar(255) DEFAULT NULL,
  `acc_number` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `entered_amount` varchar(255) DEFAULT NULL,
  `trf_fee_in_persentage` varchar(255) DEFAULT NULL,
  `trf_fee` varchar(255) DEFAULT NULL,
  `net_amount` varchar(255) DEFAULT NULL,
  `status` enum('pending_bkk_approval','accepted_bkk','completed','Rejected','cancelled') NOT NULL DEFAULT 'pending_bkk_approval',
  `reject_reason` varchar(255) DEFAULT NULL,
  `invoice_url` varchar(255) DEFAULT NULL,
  `invoice_slip` varchar(255) DEFAULT NULL,
  `transaction_slip` varchar(255) DEFAULT NULL,
  `createdBy` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `money_transfer_invoices`
--

INSERT INTO `money_transfer_invoices` (`id`, `invoice_number`, `customer_name`, `phone`, `transfer_type`, `bank_name`, `acc_name`, `acc_number`, `currency`, `entered_amount`, `trf_fee_in_persentage`, `trf_fee`, `net_amount`, `status`, `reject_reason`, `invoice_url`, `invoice_slip`, `transaction_slip`, `createdBy`, `created_at`, `updated_at`) VALUES
(49, '#TO290326153534', 'rterte', NULL, 'Transfer-OUT', 'Kasikorn Bank', 'rtertert', '2162049926', '฿', '2000', '2', '40.00', '2000.00', 'accepted_bkk', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjkwMzI2MTUzNTM0', NULL, 'transaction-slips/01KMWBS1FQ9STZPNKSEKYW8P01.jpg', '6', '2026-03-29 08:35:34', '2026-03-29 08:57:32'),
(50, '#TI290326033657', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'hfhfghfg', '2162049926', '฿', '100000', '1', '1000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjkwMzI2MDMzNjU3', NULL, 'transaction-slips-in/01KMWQ6V16P3T0TBXN282WN1N7.jpg', '6', '2026-03-29 08:36:57', '2026-03-29 11:55:58'),
(51, '#TI290326065711', 'fvbhfdgh', '56456456546', 'Transfer-IN', 'Kasikorn Bank', 'dfgdfgdg', '2162049926', '฿', '5000', '1', '50.00', '5000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjkwMzI2MDY1NzEx', NULL, NULL, '6', '2026-03-29 11:57:11', '2026-03-29 11:57:53'),
(52, '#TI290326070703', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ghfgh', '2162049926', '฿', '5000', '1', '50.00', '5000.00', 'Rejected', 'kdjfgkdfjg4mvd', 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjkwMzI2MDcwNzAz', NULL, NULL, '6', '2026-03-29 12:07:03', '2026-03-29 12:08:26'),
(53, '#TI300326021753', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'hgjfghgfhj', '0972970649', '฿', '5000', '1', '50.00', '5000.00', 'Rejected', 'ghjfgjhgfjhgfj', 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMzAwMzI2MDIxNzUz', NULL, NULL, '6', '2026-03-30 07:17:53', '2026-03-30 07:37:04'),
(54, '#TI300326023848', NULL, NULL, 'Transfer-IN', 'Krungthai Bank', 'gjhgjghjg', '0972970649', '฿', '5000', '1', '50.00', '5000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMzAwMzI2MDIzODQ4', NULL, 'transaction-slips-in/01KMYTZWD4S8SD9MHYEQJVBCPC.jpg', '6', '2026-03-30 07:38:48', '2026-03-30 07:40:34'),
(55, '#TO300326160548', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'bmhbhmjgjg', '2162049926', '฿', '2000', '2', '40.00', '1960.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMzAwMzI2MTYwNTQ4', NULL, 'transaction-slips/01KMZ04HDV0TA3TRTQYDMJTRVF.png', '6', '2026-03-30 09:05:48', '2026-03-30 09:10:29'),
(56, '#TI300326063651', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'nvbvnvgnvg', '9793037999', '฿', '20000', '1', '200.00', '19800.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMzAwMzI2MDYzNjUx', NULL, NULL, '5', '2026-03-30 11:36:51', '2026-03-30 11:36:51'),
(57, '#TO310326172736', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'dfgdfg', '2162049926', '฿', '5000', '2', '100.00', '4900.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMzEwMzI2MTcyNzM2', NULL, NULL, '5', '2026-03-31 10:27:36', '2026-03-31 10:27:36'),
(58, '#TI310326062121', 'Dk gupta', '6554564565654', 'Transfer-IN', 'Kasikorn Bank', 'fgdfdfgfd', '2162049926', '฿', '20000', '1', '200.00', '19800.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMzEwMzI2MDYyMTIx', NULL, NULL, '5', '2026-03-31 11:21:21', '2026-03-31 11:21:21'),
(59, '#TO010426105342', 'ghfgh', '6545654654', 'Transfer-OUT', 'Krungthai Bank', 'ryryry', '38146463767', '฿', '2000', '2', '40.00', '2000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDEwNDI2MTA1MzQy', NULL, NULL, '5', '2026-04-01 03:53:42', '2026-04-01 03:58:32'),
(60, '#TI010426105908', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'tfhfthyytf', '38146463767', '฿', '100000', '1', '1000.00', '100000.00', 'Rejected', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDEwNDI2MTA1OTA4', NULL, NULL, '5', '2026-04-01 03:59:08', '2026-04-01 04:00:26'),
(61, '#TI010426020847', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'regrgegr', '4361644339', '฿', '100000', '1', '1000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDEwNDI2MDIwODQ3', NULL, 'transaction-slips-in/01KN3Y708PS7V929ZFHGR5G7BJ.jpg', '5', '2026-04-01 07:08:47', '2026-04-01 07:13:05'),
(62, '#TO010426144028', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'pariya', '4361644339', '฿', '9425', '2', '188.50', '9425.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDEwNDI2MTQ0MDI4', NULL, 'transaction-slips/01KN3ZY04CPNXMV00AGQ3JZH03.jpg', '5', '2026-04-01 07:40:28', '2026-04-01 07:43:07'),
(63, '#TI010426045018', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'cfghfdgh', '4361644339', '฿', '5000', '1', '50.00', '4950.00', 'accepted_bkk', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDEwNDI2MDQ1MDE4', NULL, NULL, '5', '2026-04-01 09:50:18', '2026-04-01 09:50:56'),
(64, '#TO010426165814', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'fhfghfh', '4361644339', '฿', '5000', '2', '100.00', '4900.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDEwNDI2MTY1ODE0', NULL, 'transaction-slips/01KN47RND7GET58FVEXYXQ10BN.png', '5', '2026-04-01 09:58:14', '2026-04-01 10:00:01'),
(65, '#TI020426012513', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'gjfghfgh', '4361644339', '฿', '5000', '1', '50.00', '4950.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDEyNTEz', NULL, 'transaction-slips-in/01KN6DZAWV9431HGJJ6XWKWB6Q.jpg', '5', '2026-04-02 06:25:13', '2026-04-02 06:27:00'),
(66, '#TO020426133055', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'fgfdgd', '4361644339', '฿', '5000', '2', '100.00', '4900.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDIwNDI2MTMzMDU1', NULL, 'transaction-slips/01KN6MP6EMYVDZR1WG24PKKRRW.jpg', '5', '2026-04-02 06:30:55', '2026-04-02 08:24:20'),
(67, '#TI020426015411', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'gdfgfdgfdg', '4361644339', '฿', '5000', '1', '50.00', '4950.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDE1NDEx', NULL, 'transaction-slips-in/01KN6MTX42Q4XBH90BYF1AGP3K.jpg', '5', '2026-04-02 06:54:11', '2026-04-02 08:26:54'),
(68, '#TI020426034021', NULL, NULL, 'Transfer-IN', 'Bangkok Bank', 'fhftyhtf', '4361644339', '฿', '10000', '1', '100.00', '10000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDM0MDIx', NULL, 'transaction-slips-in/01KN6R4434RG17FSQK7TTYC0FA.jpg', '5', '2026-04-02 08:40:21', '2026-04-02 09:24:22'),
(69, '#TI020426042211', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'cbcbgfghhf', '4361644339', '฿', '5000', '1', '50.00', '5000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDQyMjEx', NULL, 'transaction-slips-in/01KN6R2DCPYTX2Z4K9AHH7RSYY.jpg', '5', '2026-04-02 09:22:11', '2026-04-02 09:23:26'),
(70, '#TI020426042606', NULL, NULL, 'Transfer-IN', 'Bangkok Bank', 'fdtgdrg', '4361644339', '฿', '5000', '1', '50.00', '5000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDQyNjA2', NULL, 'transaction-slips-in/01KN6RB1P08HTASGV9FSMVZMHV.jpg', '5', '2026-04-02 09:26:06', '2026-04-02 09:28:09'),
(71, '#TI020426044342', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ftjfgjgfj', '1172276070', '฿', '5000', '1', '50.00', '4950.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDIwNDI2MDQ0MzQy', NULL, 'transaction-slips-in/01KN6SAJ9TSCXJRZQY2Z32V2DQ.jpg', '5', '2026-04-02 09:43:42', '2026-04-02 09:45:22'),
(72, '#TI030426105406', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'gfdgdfgdfg', '4361644339', '฿', '2000', '1', '20.00', '1980.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDMwNDI2MTA1NDA2', NULL, 'transaction-slips-in/01KN8QR073VMX2Z6WEZ11133DF.jpg', '5', '2026-04-03 03:54:06', '2026-04-03 03:56:14'),
(73, '#TO030426105718', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'vgfhgf', '4361644339', '฿', '5000', '2', '100.00', '4900.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDMwNDI2MTA1NzE4', NULL, 'transaction-slips/01KN8QVS7T0Q5KZRH6QF5DQKSF.jpg', '5', '2026-04-03 03:57:18', '2026-04-03 03:58:18'),
(74, '#TI030426063801', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ghfhfh', '6394032155', '฿', '20000', '1', '200.00', '19800.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDMwNDI2MDYzODAx', NULL, 'transaction-slips-in/01KN9J8RD3P8FH5QRFS3GNBAHY.jpg', '5', '2026-04-03 11:38:01', '2026-04-03 11:39:46'),
(75, '#TI040426113747', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'dfgdhd', '6394032155', '฿', '20000', '1', '200.00', '19800.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDQwNDI2MTEzNzQ3', NULL, 'transaction-slips-in/01KNBCJPHXBEYBQ04R29H2REEE.jpg', '5', '2026-04-04 04:37:47', '2026-04-04 04:38:49'),
(76, '#TO040426114018', 'gxghfd', '43656345665', 'Transfer-OUT', 'Krungthai Bank', 'cbcbf', '6394032155', '฿', '10000', '2', '200.00', '10000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDQwNDI2MTE0MDE4', NULL, 'transaction-slips/01KNBCSGZJ4J70AWP8ZDZKGVKG.jpg', '5', '2026-04-04 04:40:18', '2026-04-04 04:42:33'),
(77, '#TO040426114321', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'ghhgh', '6394032155', '฿', '20000', '2', '400.00', '19600.00', 'Rejected', 'Wrong  account name', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDQwNDI2MTE0MzIx', NULL, NULL, '5', '2026-04-04 04:43:21', '2026-04-04 04:52:00'),
(78, '#TI040426065224', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'drgertg', '9793037999', '฿', '5000', '1', '50.00', '4950.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDQwNDI2MDY1MjI0', NULL, NULL, '5', '2026-04-04 11:52:24', '2026-04-04 11:52:24'),
(79, '#TI060426111101', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'hggfhfgh', '9157154437', '฿', '10000', '1', '100.00', '9900.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDYwNDI2MTExMTAx', NULL, 'transaction-slips-in/01KNGFWDFTT0TFXBRM355S0F26.jpg', '5', '2026-04-06 04:11:01', '2026-04-06 04:12:45'),
(80, '#TO060426111318', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'dgfdgd', '9157154437', '฿', '10000', '2', '200.00', '9800.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDYwNDI2MTExMzE4', NULL, 'transaction-slips/01KNGFYKXHQJ5XZYZD1749YE0E.jpg', '5', '2026-04-06 04:13:18', '2026-04-06 04:22:47'),
(81, '#TI060426112222', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'dgffdg', '9157154437', '฿', '5000', '1', '50.00', '4950.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDYwNDI2MTEyMjIy', NULL, 'transaction-slips-in/01KNGGFC6K7CBFTZKNKQD88DZW.jpg', '5', '2026-04-06 04:22:22', '2026-04-06 04:23:07'),
(82, '#TO060426151219', 'dk gupta', '7584375943857', 'Transfer-OUT', 'Kasikorn Bank', 'ddddddd', '4361644339', '฿', '10000', '2', '200.00', '9800.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDYwNDI2MTUxMjE5', NULL, NULL, '5', '2026-04-06 08:12:19', '2026-04-06 08:12:19'),
(83, '#TI080426113423', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'gfhjfghfg', '082600170008', '฿', '20000', '1', '200.00', '19800.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDgwNDI2MTEzNDIz', NULL, 'transaction-slips-in/01KNNP0WDVC54A2E3VHKZY527Z.jpg', '5', '2026-04-08 04:34:23', '2026-04-08 04:36:15'),
(84, '#TO080426113700', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'fdgdfg', '082600170008', '฿', '100000', '2', '2000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDgwNDI2MTEzNzAw', NULL, 'transaction-slips/01KNNP3HFTZY3N9BC04CRV06CN.jpg', '5', '2026-04-08 04:37:00', '2026-04-08 04:37:43'),
(85, '#TI080426113927', 'dk gupta', '456456546', 'Transfer-IN', 'Bank for Agriculture (BAAC)', 'djhgfssdf', '082600170008', '฿', '100000', '1', '1000.00', '100000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDgwNDI2MTEzOTI3', NULL, NULL, '5', '2026-04-08 04:39:27', '2026-04-08 04:40:35'),
(86, '#TO090426134936', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'fhgfdhgf', '4361644339', '฿', '100000', '2', '2000.00', '100000.00', 'Rejected', 'Change mind', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMDkwNDI2MTM0OTM2', NULL, NULL, '5', '2026-04-09 06:49:36', '2026-04-09 06:51:18'),
(87, '#TI090426015151', NULL, NULL, 'Transfer-IN', 'Krungthai Bank', 'dgdfgd', '4361644339', '฿', '100000', '1', '1000.00', '99000.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMDkwNDI2MDE1MTUx', NULL, NULL, '5', '2026-04-09 06:51:51', '2026-04-09 06:51:51'),
(88, '#TO100426104631', NULL, NULL, 'Transfer-OUT', 'Bank of Ayudhya', 'v bcxbcxbv', '2133898681', '฿', '100000', '2', '2000.00', '100000.00', 'Rejected', 'wrong account name', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTAwNDI2MTA0NjMx', NULL, NULL, '5', '2026-04-10 03:46:31', '2026-04-10 03:47:15'),
(89, '#TO110426181743', NULL, NULL, 'Transfer-OUT', 'CIMB Thai Bank', 'ghghgfh', '9157154437', '฿', '100000', '2', '2000.00', '100000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTEwNDI2MTgxNzQz', NULL, NULL, '5', '2026-04-11 11:17:43', '2026-04-11 11:18:01'),
(90, '#TI130426104159', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'dfsf', '2133898681', '฿', '15000', '1', '150.00', '15000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMTMwNDI2MTA0MTU5', NULL, 'transaction-slips-in/01KP2EYF45C2C3CF77DRCMTEEV.jpg', '11', '2026-04-13 03:41:59', '2026-04-13 03:42:44'),
(91, '#TO130426104334', NULL, NULL, 'Transfer-OUT', 'Bank of Ayudhya', 'dsfgdsgd', '2133898681', '฿', '100000', '2', '2000.00', '100000.00', 'Rejected', 'wrong acc', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTMwNDI2MTA0MzM0', NULL, NULL, '11', '2026-04-13 03:43:34', '2026-04-13 03:44:37'),
(92, '#TI130426110513', NULL, NULL, 'Transfer-IN', 'Government Housing Bank', 'gffhf', '2133898681', '฿', '100000', '1', '1000.00', '100000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMTMwNDI2MTEwNTEz', NULL, NULL, '11', '2026-04-13 04:05:13', '2026-04-13 04:05:31'),
(93, '#TO130426110614', NULL, NULL, 'Transfer-OUT', 'Bank of Ayudhya', 'dgfdgd', '2133898681', '฿', '10000', '1', '100.00', '9900.00', 'Rejected', 'Change mind', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTMwNDI2MTEwNjE0', NULL, NULL, '11', '2026-04-13 04:06:14', '2026-04-13 04:06:28'),
(94, '#TI130426021043', NULL, NULL, 'Transfer-IN', 'Siam Commercial Bank', 'jack', '2162049926', '฿', '100000', '1', '1000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMTMwNDI2MDIxMDQz', NULL, 'transaction-slips-in/01KP2V2F8SASC0025NFPDJ4C18.jpg', '5', '2026-04-13 07:10:43', '2026-04-13 07:14:38'),
(95, '#TO130426141839', NULL, NULL, 'Transfer-OUT', 'Bangkok Bank', 'egfjhdg', '2162049926', '฿', '20000', '1', '200.00', '19800.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTMwNDI2MTQxODM5', NULL, 'transaction-slips/01KP2VSSXWRP8RKR04X6FZQV79.jpg', '5', '2026-04-13 07:18:39', '2026-04-13 07:27:23'),
(96, '#TI180426022841', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'dfgdfgf', '7052627207', '฿', '5000', '1', '50.00', '5000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMTgwNDI2MDIyODQx', NULL, 'transaction-slips-in/01KPFQZ59KTCRZZZWN2JWDKWHB.png', '5', '2026-04-18 07:28:41', '2026-04-18 07:30:31'),
(97, '#TO180426143145', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'fghfgh', '7052627207', '฿', '100000', '1', '1000.00', '100000.00', 'Rejected', 'wrong   account number', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMTgwNDI2MTQzMTQ1', NULL, NULL, '5', '2026-04-18 07:31:45', '2026-04-18 07:32:54'),
(98, '#TI210426115451', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ghjhnvfghj', '657', '฿', '10000', '2', '200', '9800', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjEwNDI2MTE1NDUx', NULL, NULL, '5', '2026-04-21 04:54:51', '2026-04-21 04:54:51'),
(99, '#TI210426115645', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'cfchfh', '4564', '฿', '100000', '1', '1000', '99000', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjEwNDI2MTE1NjQ1', NULL, NULL, '5', '2026-04-21 04:56:45', '2026-04-21 04:56:45'),
(100, '#TI210426014516', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ghfh', '56765', '฿', '2000', '2', '40.00', '2000.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjEwNDI2MDE0NTE2', NULL, NULL, '5', '2026-04-21 06:45:16', '2026-04-21 06:45:16'),
(101, '#TI210426014602', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'ghjghj', '6464', '฿', '100000', '1', '1000.00', '99000.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjEwNDI2MDE0NjAy', NULL, NULL, '5', '2026-04-21 06:46:02', '2026-04-21 06:46:02'),
(102, '#TI210426014638', NULL, NULL, 'Transfer-IN', 'Kasikorn Bank', 'cgcbhgfch', '456', '฿', '100000', '1', '1000.00', '100000.00', 'pending_bkk_approval', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjEwNDI2MDE0NjM4', NULL, NULL, '5', '2026-04-21 06:46:38', '2026-04-21 06:46:38'),
(103, '#TI220426093514', 'fdg', '456546546', 'Transfer-IN', 'Bank of Ayudhya', 'ryrtyrt', '54645', '฿', '100000', '1', '1000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjIwNDI2MDkzNTE0', NULL, 'transaction-slips-in/01KPSGPV06XRNE427PK4RX764N.jpg', '5', '2026-04-22 02:35:14', '2026-04-22 02:36:03'),
(104, '#TO220426093722', NULL, NULL, 'Transfer-OUT', 'Islamic Bank of Thailand', 'hghg', '45648', '฿', '20000', '2', '400', '19600', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MDkzNzIy', NULL, 'transaction-slips/01KPSGTE87YEBD0N1VHPFTABYF.jpg', '5', '2026-04-22 02:37:22', '2026-04-22 02:38:01'),
(105, '#TO220426133444', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'hfghfh', '54645', '฿', '100000', '1', '1000', '99000', 'Rejected', 'Wrong Account details', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MTMzNDQ0', NULL, NULL, '5', '2026-04-22 06:34:44', '2026-04-22 06:40:44'),
(106, '#TO220426133523', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'fhfh', '54645', '฿', '20000', '2', '400', '19600', 'Rejected', 'Change mind', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MTMzNTIz', NULL, NULL, '5', '2026-04-22 06:35:23', '2026-04-22 06:40:10'),
(107, '#TO220426133809', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'yry', '6657', '฿', '100000', '1', '1000.00', '100000.00', 'Rejected', 'Wrong amount', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MTMzODA5', NULL, NULL, '5', '2026-04-22 06:38:09', '2026-04-22 06:40:00'),
(108, '#TO220426133839', NULL, NULL, 'Transfer-OUT', 'Krungthai Bank', 'yutyu', '56767', '฿', '20000', '2', '400.00', '20000.00', 'Rejected', 'ghjfgh', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MTMzODM5', NULL, NULL, '5', '2026-04-22 06:38:39', '2026-04-22 06:39:06'),
(109, '#TI220426050046', NULL, NULL, 'Transfer-IN', 'Bangkok Bank', 'fdfhg', '4545', '฿', '100000', '1', '1000.00', '100000.00', 'completed', NULL, 'http://127.0.0.1:8000/money-transfer/invoice/I1RJMjIwNDI2MDUwMDQ2', NULL, 'transaction-slips-in/01KPTAFGNDXNHKMS6FM4DBHQK0.jpg', '5', '2026-04-22 10:00:46', '2026-04-22 10:06:26'),
(110, '#TO220426171009', NULL, NULL, 'Transfer-OUT', 'Kasikorn Bank', 'gfgfg', '3435', '฿', '20000', '2', '400.00', '19600.00', 'Rejected', 'wur', 'http://127.0.0.1:8000/money-transfer/invoice/I1RPMjIwNDI2MTcxMDA5', NULL, NULL, '5', '2026-04-22 10:10:09', '2026-04-22 10:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('iO9NHqLZsx6Dmm5rOjpWk0wt90zvgo04F05k81GB', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZjV6bW9ma1hVR2xid0lud2dPZkphRFI0cHJ1N1FOZG1Wb1BYT2ZZRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ia2tvZmZpY2UiO3M6NToicm91dGUiO3M6MzQ6ImZpbGFtZW50LmJra29mZmljZS5wYWdlcy5kYXNoYm9hcmQiO31zOjU2OiJsb2dpbl9ia2tvZmZpY2VfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo5O3M6MjM6InBhc3N3b3JkX2hhc2hfYmtrb2ZmaWNlIjtzOjY0OiJlYjhmZWVkZjU4NWYwNTY1MzE4NzRjZTYzNjI3OGEwOWMzNWNkYjQyMzY4Nzc0NmM1OGU2ZTk3YjdlYWFjNDFkIjt9', 1776919030),
('peY8NbzeFwHjAbaF0DquIts7UBLk86dCiIlwjTkq', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoid3NYYjdqYURTMTFiTHJMS3VpQ0JwVHNsZmVjOW44S3Uxb04xRjZRZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90ZWxsZXIiO3M6NToicm91dGUiO3M6Mzg6ImZpbGFtZW50LnRlbGxlci5wYWdlcy50ZWxsZXItZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MzoibG9naW5fdGVsbGVyXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7czoyMDoicGFzc3dvcmRfaGFzaF90ZWxsZXIiO3M6NjQ6Ijc1YWZkMjljNjFmNzkwZDliYmMxOGI3M2RkOWEyNmZjZGY1YmIyMzlhMmQzOTk4NmMwNjZkNjA3ZDI5ZDdkYTQiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1776918948),
('r2nivV0WN5DrTBD50SLZ2SaJckceuIrTOunIBTUd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHZBQmpKTndEWjJPYjI2ck9kZ3RHbzMyYmJSeGlSZ1NmUDJtTWR4diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776916145);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phoneNumber` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` varchar(255) DEFAULT '0',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phoneNumber`, `role`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, 'admin', 'exchangeadmin@gmail.com', '0', '$2y$12$HVpob/gBzZs9ydJExqo9dOnoEMBzAQ1zz8ieffiw.q6foo2n8GApO', 'jgQ1Uj6EIYS9gufYeF398VrFYXNFmqdQ1RVCNWiYMPg3eg7s1NvFxBIPNBNR', '2026-02-28 06:42:32', '2026-02-28 06:42:32'),
(5, 'dilip', '69861400', 'teller', 'dilipkumargupta31@gmail.com', '1', '$2y$12$X4DsROLrt73SmwYK0TJUs.EHb7rVQx29twfjqCTTymvTUGBnHCqHi', 'LPvIsopyb0nUnEQDt14BMNxwInGEryyttgZv7JjNMC5ucNQA0Ye2ok3AojOK', '2026-03-16 02:12:33', '2026-03-16 02:12:33'),
(6, 'jack', '7234567890', 'teller', 'jack@gmail.com', '1', '$2y$12$rfd5PwOIW..6gXiVpHOAiOZ3uyq/S/n0s9GGq/C.iCfw3nDSh7Q8S', NULL, '2026-03-17 02:41:04', '2026-03-17 02:41:04'),
(8, 'sk yadav', '7234567888', 'teller', 'sk@gmail.com', '0', '$2y$12$Z7mmGbXGUZULtJNU2VAideRPomOUaA57L3WW2RV.agO119mPnobpe', NULL, '2026-03-17 03:56:38', '2026-03-17 03:56:38'),
(9, 'sandeep', '8888888888', 'bkkoffice', 'sandeep@gmail.com', '1', '$2y$12$QgBJvyoVKiWRtT85fmTQHO24Ke/183B4fPNQ1URmKAyLCjCRFs9iK', 'ZR7pzd60amBOOrklsfm8H1EoEAX9jp4w04r65AlV1NlkTdUW0I5uAoKQohrA', '2026-03-18 21:09:49', '2026-03-18 21:09:49'),
(10, 'rocky', '7234567855', 'teller', 'roky@gmail.com', '1', '$2y$12$mfyvpq2da6fVQd.rYyE3WeQ4L.X3Ahg49D1K2whk3PYzRA6WoLJva', NULL, '2026-04-13 03:17:31', '2026-04-13 03:17:31'),
(11, 'suman', '7234567899', 'teller', 'suman@gmail.com', '1', '$2y$12$ZDyRCKYUM7SnY7wNozBD3eOC2DEK5/X.yg1MCIoRlfFMMTr19Gv0u', NULL, '2026-04-13 03:37:52', '2026-04-13 03:37:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `money_exchange_invoices`
--
ALTER TABLE `money_exchange_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `money_transfer_charges`
--
ALTER TABLE `money_transfer_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `money_transfer_invoices`
--
ALTER TABLE `money_transfer_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `money_exchange_invoices`
--
ALTER TABLE `money_exchange_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `money_transfer_charges`
--
ALTER TABLE `money_transfer_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `money_transfer_invoices`
--
ALTER TABLE `money_transfer_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
