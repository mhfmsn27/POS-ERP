-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.24-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table poshub.provinces
CREATE TABLE IF NOT EXISTS `provinces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table poshub.provinces: ~34 rows (approximately)
DELETE FROM `provinces`;
/*!40000 ALTER TABLE `provinces` DISABLE KEYS */;
INSERT INTO `provinces` (`id`, `status`, `name`, `code`, `province_id`, `created_at`, `updated_at`) VALUES
	(1, 'yes', 'Bali', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(2, 'yes', 'Bangka Belitung', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(3, 'yes', 'Banten', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(4, 'yes', 'Bengkulu', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(5, 'yes', 'DI Yogyakarta', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(6, 'yes', 'DKI Jakarta', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(7, 'yes', 'Gorontalo', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(8, 'yes', 'Jambi', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(9, 'yes', 'Jawa Barat', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(10, 'yes', 'Jawa Tengah', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(11, 'yes', 'Jawa Timur', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(12, 'yes', 'Kalimantan Barat', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(13, 'yes', 'Kalimantan Selatan', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(14, 'yes', 'Kalimantan Tengah', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(15, 'yes', 'Kalimantan Timur', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(16, 'yes', 'Kalimantan Utara', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(17, 'yes', 'Kepulauan Riau', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(18, 'yes', 'Lampung', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(19, 'yes', 'Maluku', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(20, 'yes', 'Maluku Utara', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(21, 'yes', 'Nanggroe Aceh Darussalam (NAD)', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(22, 'yes', 'Nusa Tenggara Barat (NTB)', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(23, 'yes', 'Nusa Tenggara Timur (NTT)', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(24, 'yes', 'Papua', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(25, 'yes', 'Papua Barat', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(26, 'yes', 'Riau', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(27, 'yes', 'Sulawesi Barat', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(28, 'yes', 'Sulawesi Selatan', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(29, 'yes', 'Sulawesi Tengah', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(30, 'yes', 'Sulawesi Tenggara', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(31, 'yes', 'Sulawesi Utara', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(32, 'yes', 'Sumatera Barat', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(33, 'yes', 'Sumatera Selatan', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00'),
	(34, 'yes', 'Sumatera Utara', NULL, NULL, '2023-06-01 21:57:00', '2023-06-01 21:57:00');
/*!40000 ALTER TABLE `provinces` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
