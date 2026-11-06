-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: thltw_huy_hoa_24e
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `baiviet`
--

DROP TABLE IF EXISTS `baiviet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `baiviet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_type` enum('post','page') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '1',
  `position` enum('slideshow','advertise') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'slideshow',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cauhinh`
--

DROP TABLE IF EXISTS `cauhinh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cauhinh` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenwebsite` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `sodienthoai` varchar(20) DEFAULT NULL,
  `diachi` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietdonhang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `price` bigint unsigned NOT NULL,
  `qty` int unsigned NOT NULL,
  `amount` bigint unsigned NOT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kitchen_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `priority` tinyint unsigned NOT NULL DEFAULT '0',
  `queued_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `preparing_at` timestamp NULL DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `served_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orderdetail_kitchen_status_index` (`kitchen_status`),
  KEY `orderdetail_priority_index` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chitietgiohang`
--

DROP TABLE IF EXISTS `chitietgiohang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietgiohang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `giohang_id` bigint unsigned NOT NULL,
  `sanpham_id` bigint unsigned NOT NULL,
  `soluong` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_ctgiohang_giohang` (`giohang_id`),
  KEY `fk_ctgiohang_sanpham` (`sanpham_id`),
  CONSTRAINT `fk_ctgiohang_giohang` FOREIGN KEY (`giohang_id`) REFERENCES `giohang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ctgiohang_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chitietphieukho`
--

DROP TABLE IF EXISTS `chitietphieukho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietphieukho` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phieukho_id` bigint unsigned NOT NULL,
  `sanpham_id` bigint unsigned NOT NULL,
  `soluong` int unsigned NOT NULL,
  `gianhap` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ctphieukho_phieukho` (`phieukho_id`),
  KEY `fk_ctphieukho_sanpham` (`sanpham_id`),
  CONSTRAINT `fk_ctphieukho_phieukho` FOREIGN KEY (`phieukho_id`) REFERENCES `phieukho` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ctphieukho_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chudebaiviet`
--

DROP TABLE IF EXISTS `chudebaiviet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chudebaiviet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_usages`
--

DROP TABLE IF EXISTS `coupon_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `discount` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupon_usages_order_id_unique` (`order_id`),
  KEY `coupon_usages_coupon_id_index` (`coupon_id`),
  KEY `coupon_usages_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` bigint unsigned NOT NULL,
  `min_order` bigint unsigned NOT NULL DEFAULT '0',
  `max_discount` bigint unsigned DEFAULT NULL,
  `usage_limit` int unsigned DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `danhmuc`
--

DROP TABLE IF EXISTS `danhmuc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `danhmuc` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int unsigned NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donhang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_id` bigint unsigned DEFAULT NULL,
  `reservation_id` bigint unsigned DEFAULT NULL,
  `order_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `guest_count` smallint unsigned NOT NULL DEFAULT '1',
  `subtotal` bigint unsigned NOT NULL DEFAULT '0',
  `discount` bigint unsigned NOT NULL DEFAULT '0',
  `total` bigint unsigned NOT NULL DEFAULT '0',
  `payment_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `guest_session_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code_unique` (`code`),
  KEY `order_table_id_foreign` (`table_id`),
  KEY `order_reservation_id_foreign` (`reservation_id`),
  KEY `order_status_index` (`status`),
  KEY `order_order_type_index` (`order_type`),
  KEY `order_payment_status_index` (`payment_status`),
  KEY `donhang_guest_session_id_index` (`guest_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_product_id_unique` (`user_id`,`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `giohang`
--

DROP TABLE IF EXISTS `giohang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `giohang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoidung_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_giohang_nguoidung` (`nguoidung_id`),
  CONSTRAINT `fk_giohang_nguoidung` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guest_sessions`
--

DROP TABLE IF EXISTS `guest_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `items` json DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guest_sessions_token_hash_unique` (`token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `khuyenmai`
--

DROP TABLE IF EXISTS `khuyenmai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `khuyenmai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenkhuyenmai` varchar(255) NOT NULL,
  `makhuyenmai` varchar(50) NOT NULL,
  `loaigiam` enum('phantram','sotien') NOT NULL,
  `giatri` decimal(15,2) NOT NULL,
  `giatridonhangtoithieu` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ngaybatdau` datetime NOT NULL,
  `ngayketthuc` datetime NOT NULL,
  `trangthai` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `makhuyenmai` (`makhuyenmai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lichsutrangthaidonhang`
--

DROP TABLE IF EXISTS `lichsutrangthaidonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lichsutrangthaidonhang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `donhang_id` bigint unsigned NOT NULL,
  `nguoidung_id` bigint unsigned NOT NULL,
  `trangthai` enum('cho_xac_nhan','da_xac_nhan','dang_giao','da_giao','da_huy') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_lsttdh_donhang` (`donhang_id`),
  KEY `fk_lsttdh_nguoidung` (`nguoidung_id`),
  CONSTRAINT `fk_lsttdh_donhang` FOREIGN KEY (`donhang_id`) REFERENCES `donhang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lsttdh_nguoidung` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lienhe`
--

DROP TABLE IF EXISTS `lienhe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lienhe` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `replay_id` int unsigned NOT NULL DEFAULT '0',
  `reply_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_id` int unsigned DEFAULT NULL,
  `type` enum('category','brand','topic','page','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nguoidung`
--

DROP TABLE IF EXISTS `nguoidung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nguoidung` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` enum('admin','manager','waiter','kitchen','warehouse','customer','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email_unique` (`email`),
  UNIQUE KEY `user_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `from_status` tinyint unsigned DEFAULT NULL,
  `to_status` tinyint unsigned NOT NULL,
  `actor_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_history_order_id_index` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_attempts`
--

DROP TABLE IF EXISTS `payment_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint unsigned NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VND',
  `gateway_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_attempts_order_id_unique` (`order_id`),
  UNIQUE KEY `payment_attempts_reference_unique` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phieukho`
--

DROP TABLE IF EXISTS `phieukho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phieukho` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoidung_id` bigint unsigned NOT NULL,
  `loaiphieu` enum('nhap','xuat') NOT NULL,
  `ghichu` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_phieukho_nguoidung` (`nguoidung_id`),
  CONSTRAINT `fk_phieukho_nguoidung` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_reviews_product_id_user_id_unique` (`product_id`,`user_id`),
  KEY `product_reviews_product_id_index` (`product_id`),
  KEY `product_reviews_is_visible_index` (`is_visible`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sanpham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_buy` bigint unsigned NOT NULL,
  `price_sale` bigint unsigned DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL,
  `detail` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'phần',
  `prep_minutes` smallint unsigned NOT NULL DEFAULT '15',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `discount_percent` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_sku_unique` (`sku`),
  KEY `product_category_id_status_index` (`category_id`,`status`),
  KEY `product_brand_id_status_index` (`brand_id`,`status`),
  KEY `product_is_available_index` (`is_available`),
  KEY `product_is_featured_index` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_document_items`
--

DROP TABLE IF EXISTS `stock_document_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_document_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL,
  `unit_cost` bigint unsigned NOT NULL DEFAULT '0',
  `stock_before` int unsigned NOT NULL,
  `stock_after` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_document_items_document_id_index` (`document_id`),
  KEY `stock_document_items_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_documents`
--

DROP TABLE IF EXISTS `stock_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_documents_reference_unique` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `thuonghieu`
--

DROP TABLE IF EXISTS `thuonghieu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thuonghieu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `replay_id` int unsigned NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trangdon`
--

DROP TABLE IF EXISTS `trangdon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trangdon` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_type` enum('post','page') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-06 15:59:49
-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: thltw_huy_hoa_24e
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `baiviet`
--

LOCK TABLES `baiviet` WRITE;
/*!40000 ALTER TABLE `baiviet` DISABLE KEYS */;
INSERT INTO `baiviet` VALUES (1,NULL,'Giới thiệu HH Shop','about','1. Chào mừng đến với HH Shop\nHH Shop mang đến không gian mua sắm trực tuyến dành cho người yêu công nghệ. Từ điện thoại và laptop đến phụ kiện, chúng tôi giúp bạn tìm kiếm, lựa chọn và đặt mua thiết bị phù hợp với nhu cầu.\n\n2. Lựa chọn phù hợp với bạn\nMỗi người có cách sử dụng công nghệ khác nhau. Bạn có thể tìm theo danh mục, thương hiệu và khoảng giá để lựa chọn sản phẩm phù hợp với mục đích sử dụng và ngân sách.\n\n3. Điện thoại cho cuộc sống mỗi ngày\nKhám phá điện thoại phục vụ liên lạc, chụp ảnh, học tập và giải trí. Tham khảo thông tin trên trang chi tiết và trao đổi với HH Shop nếu bạn cần tư vấn thêm.\n\n4. Laptop cho học tập và công việc\nTìm kiếm laptop cho học tập, làm việc và sáng tạo. Hãy cân nhắc cấu hình, kích thước và khả năng kết nối theo các ứng dụng bạn thường sử dụng.\n\n5. Phụ kiện và thiết bị gaming\nHoàn thiện góc làm việc và giải trí với bàn phím, chuột, màn hình, thiết bị lưu trữ, tai nghe và loa. Danh mục được chia thành các nhóm để bạn dễ tìm sản phẩm.\n\n6. Thông tin rõ ràng trước khi mua\nGiá bán, mức giảm giá và tình trạng hàng được thể hiện trên website. Bạn có thể xem mô tả và đánh giá của người dùng trước khi quyết định đặt hàng.\n\n7. Mua sắm thuận tiện\nThêm sản phẩm vào giỏ, kiểm tra số lượng, áp dụng mã giảm giá hợp lệ và điền thông tin nhận hàng. Tổng tiền được hiển thị để bạn kiểm tra trước khi xác nhận.\n\n8. Thanh toán theo lựa chọn của bạn\nHH Shop hỗ trợ thanh toán khi nhận hàng và thanh toán chuyển khoản qua liên kết QR được tạo cho từng đơn. Bạn có thể kiểm tra trạng thái thanh toán trong chi tiết đơn hàng.\n\n9. Quản lý mua sắm trong tài khoản\nLưu sản phẩm yêu thích, cập nhật hồ sơ và theo dõi đơn hàng tại một nơi. Email xác nhận và thông báo trạng thái giúp bạn nắm được tiến trình xử lý đơn.\n\n10. Kết nối với HH Shop\nNếu cần tư vấn sản phẩm hoặc hỗ trợ đơn hàng, hãy sử dụng các kênh được công bố trên trang Liên hệ. HH Shop luôn trân trọng phản hồi để cải thiện trải nghiệm mua sắm.',NULL,'page','Công nghệ gần hơn với bạn — lựa chọn thiết bị phù hợp cho học tập, công việc và giải trí.','2026-10-18 23:10:21',3,'2026-11-01 23:20:07',3,NULL,1),(2,NULL,'Chính sách mua hàng','purchase-policy','1. Thông tin trước khi đặt hàng\nVui lòng đọc tên sản phẩm, mô tả, giá bán và tình trạng hàng trước khi mua. Hình ảnh thể hiện sản phẩm; màu sắc có thể khác nhẹ do màn hình và điều kiện chụp. Nếu cần xác nhận thông số hoặc phụ kiện đi kèm, hãy liên hệ HH Shop.\n\n2. Giá bán và mã giảm giá\nGiá được hiển thị bằng đồng Việt Nam. Mã giảm giá áp dụng theo thời gian, giá trị đơn tối thiểu và số lượt còn lại. Một đơn sử dụng một mã; kiểm tra số tiền giảm và tổng thanh toán ở bước đặt hàng.\n\n3. Quy trình đặt hàng\nĐăng nhập tài khoản, chọn sản phẩm và số lượng, mở giỏ hàng rồi nhập thông tin nhận hàng. Kiểm tra lại nội dung đơn và phương thức thanh toán trước khi bấm xác nhận.\n\n4. Thông tin nhận hàng\nVui lòng cung cấp chính xác họ tên, số điện thoại, email và địa chỉ. Email xác nhận được gửi đến địa chỉ bạn nhập trong đơn. Thay đổi hồ sơ cá nhân không tự thay đổi thông tin của đơn đã đặt.\n\n5. Thanh toán khi nhận hàng\nVới phương thức thanh toán khi nhận hàng, đơn được tiếp nhận ở trạng thái Chờ xác nhận. HH Shop kiểm tra và xác nhận đơn trước khi chuẩn bị hàng. Vui lòng thanh toán theo tổng tiền được xác nhận khi nhận hàng.\n\n6. Thanh toán chuyển khoản\nSử dụng liên kết thanh toán trong đơn để quét QR đúng số tiền và nội dung được tạo. Khi hệ thống xác thực thanh toán thành công, trạng thái thanh toán và xác nhận đơn được cập nhật. Nếu đã chuyển tiền nhưng chưa thấy cập nhật, hãy liên hệ hỗ trợ kèm mã đơn và thông tin giao dịch.\n\n7. Xác nhận và giao nhận\nBạn có thể theo dõi các bước Chờ xác nhận, Đã xác nhận, Đang giao và Hoàn thành trong tài khoản. Thông tin giao nhận cần điều chỉnh hoặc phát sinh sẽ được trao đổi qua thông tin liên hệ trong đơn. Vui lòng kiểm tra sản phẩm và giữ thông tin mua hàng khi nhận.\n\n8. Thay đổi hoặc hủy đơn\nBạn có thể tự hủy đơn chưa thanh toán khi còn Chờ xác nhận trong trang chi tiết đơn hàng. Với đơn đã xác nhận hoặc đã thanh toán, vui lòng liên hệ HH Shop để kiểm tra khả năng xử lý. Nếu có vấn đề về hàng hóa, cửa hàng sẽ trao đổi hướng xử lý; hủy đơn đã trả tiền không đồng nghĩa tiền đã được hoàn.\n\n9. Hỗ trợ đổi trả và bảo hành\nKhi cần hỗ trợ, hãy cung cấp mã đơn, tên sản phẩm, tình trạng và hình ảnh liên quan. Giữ sản phẩm, phụ kiện và chứng từ mua hàng để đối chiếu. Điều kiện tiếp nhận và phương án xử lý được xác định theo từng sản phẩm, tình trạng thực tế và chính sách bảo hành áp dụng; vui lòng liên hệ trước khi gửi hàng.\n\n10. Bảo vệ thông tin và liên hệ\nKhông cung cấp mật khẩu, mã OTP hoặc token đăng nhập cho người khác. HH Shop không yêu cầu các thông tin này để tra cứu đơn hàng. Nếu có thắc mắc về chính sách hoặc cần hỗ trợ, hãy sử dụng kênh liên hệ công bố trên website.',NULL,'page','Thông tin đặt hàng, thanh toán, giao nhận và hỗ trợ sau mua tại HH Shop.','2026-10-18 23:10:21',3,'2026-11-01 23:20:07',3,NULL,1),(3,NULL,'Liên hệ HH Shop','contact','1. Chúng tôi có thể giúp gì cho bạn?\nBạn cần chọn thiết bị, kiểm tra đơn hay hỗ trợ sau mua? Liên hệ HH Shop qua email hoặc số điện thoại được công bố bên dưới. Chuẩn bị thông tin liên quan sẽ giúp việc hỗ trợ nhanh và chính xác hơn.\n\n2. Tư vấn sản phẩm\nCho chúng tôi biết nhu cầu sử dụng, ngân sách và sản phẩm bạn đang quan tâm. HH Shop sẽ trao đổi thêm để giúp bạn cân nhắc lựa chọn phù hợp.\n\n3. Hỗ trợ đặt hàng\nNếu gặp khó khăn khi thêm sản phẩm vào giỏ, áp dụng mã giảm giá hoặc xác nhận đơn, hãy gửi tên sản phẩm và bước bạn đang thực hiện. Có thể kèm ảnh thông báo lỗi, nhưng cần che thông tin riêng tư.\n\n4. Tra cứu tiến trình đơn\nĐăng nhập và mở danh sách đơn hàng để xem trạng thái mới nhất. Khi liên hệ, cung cấp mã đơn dạng HH-000000 để HH Shop đối chiếu đúng giao dịch.\n\n5. Điều chỉnh thông tin nhận hàng\nGửi mã đơn cùng thông tin cần thay đổi nếu nhập nhầm tên, số điện thoại hoặc địa chỉ. HH Shop sẽ kiểm tra tình trạng xử lý trước khi xác nhận khả năng điều chỉnh.\n\n6. Hỗ trợ thanh toán\nNếu cần kiểm tra chuyển khoản, vui lòng cung cấp mã đơn, số tiền và thời điểm thanh toán. Không gửi mật khẩu ngân hàng hoặc mã OTP. Bạn cũng có thể sử dụng nút Kiểm tra thanh toán trong chi tiết đơn.\n\n7. Hỗ trợ tài khoản\nSử dụng Quên mật khẩu ở trang đăng nhập để nhận liên kết đặt lại qua email đăng ký. Nếu không nhận được thư, kiểm tra mục Spam. Với vấn đề khác, cung cấp email tài khoản và mô tả tình huống, không gửi mật khẩu.\n\n8. Hỗ trợ sau mua\nĐể yêu cầu hỗ trợ sản phẩm, chuẩn bị mã đơn, tên thiết bị và mô tả vấn đề. Hình ảnh hoặc video thể hiện tình trạng giúp HH Shop tiếp nhận và trao đổi hướng xử lý phù hợp.\n\n9. Góp ý và hợp tác\nHH Shop trân trọng góp ý về sản phẩm, dịch vụ và trải nghiệm website. Với đề xuất hợp tác, vui lòng gửi tên đơn vị, nội dung trao đổi và thông tin liên hệ để phản hồi.\n\n10. Kênh liên hệ chính thức\nSử dụng thông tin liên hệ hiển thị trên trang này và chân trang website. Vui lòng kiểm tra đúng địa chỉ người nhận trước khi gửi thông tin hoặc thanh toán. HH Shop không yêu cầu cung cấp mật khẩu hay mã OTP qua các kênh hỗ trợ.',NULL,'page','Tư vấn sản phẩm, hỗ trợ đặt hàng và đồng hành cùng bạn trong quá trình sử dụng.','2026-10-18 23:10:21',3,'2026-11-01 23:20:07',3,NULL,1),(4,1,'HH Shop cập nhật trải nghiệm mua sắm','hh-shop-cap-nhat-trai-nghiem-mua-sam','HH Shop bổ sung phần chấm điểm và đánh giá tại trang sản phẩm. Đăng nhập để chia sẻ trải nghiệm, cập nhật hoặc xóa đánh giá của mình. Bạn cũng có thể mở Đơn hàng của tôi để theo dõi trạng thái và thông tin nhận hàng. Đây là bài viết mẫu phục vụ đồ án.',NULL,'post','Khám phá đánh giá sản phẩm và theo dõi đơn hàng.','2026-10-26 00:11:22',3,'2026-10-26 00:11:22',NULL,NULL,1),(5,2,'Trải nghiệm thanh toán tại HH Shop','trai-nghiem-thanh-toan-tai-hh-shop','Trong buổi thực hành, nhóm sẽ trình diễn luồng giỏ hàng, đặt hàng và lựa chọn COD, MoMo, PayPal hoặc thẻ nội địa qua VNPAY. Các phương thức online chỉ khả dụng khi quản trị viên đã cấu hình tài khoản sandbox. Không sử dụng thông tin thẻ thật. Lịch tổ chức sẽ được nhóm cập nhật sau. Đây là nội dung sự kiện mẫu.',NULL,'post','Hoạt động thực hành quy trình đặt hàng và thanh toán thử nghiệm.','2026-10-26 00:11:22',3,'2026-10-26 00:24:58',3,NULL,1);
/*!40000 ALTER TABLE `baiviet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
INSERT INTO `banner` VALUES (1,'Công nghệ mới. Cảm hứng mỗi ngày.','/products',1,'slideshow','Khám phá những chiếc điện thoại đồng hành cùng phong cách của bạn.','/images/banners/hh-phone.png','2026-10-26 00:46:42',1,'2026-10-26 00:46:42',NULL,NULL,1),(2,'Bật ý tưởng. Mở tương lai.','/products',2,'slideshow','Laptop cho học tập, làm việc và những ý tưởng không giới hạn.','/images/banners/hh-laptop.png','2026-10-26 00:46:42',1,'2026-10-26 00:46:42',NULL,NULL,1),(3,'Kết nối trọn vẹn. Tận hưởng mỗi ngày.','/products',3,'slideshow','Tai nghe và phụ kiện tinh tế cho mọi khoảnh khắc của bạn.','/images/banners/hh-accessories.png','2026-10-26 00:46:42',1,'2026-10-26 00:46:42',NULL,NULL,1);
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cauhinh`
--

LOCK TABLES `cauhinh` WRITE;
/*!40000 ALTER TABLE `cauhinh` DISABLE KEYS */;
INSERT INTO `cauhinh` VALUES (1,'HH Shop','http://localhost:3000/hh-shop-demo.svg','http://localhost:3000/default-favicon.icon','huy09747@gmail.com','0843212278','55/8 TP Hồ Chí Minh','https://web.facebook.com/NMHuy10105/','https://example.com/hh-shop/instagram','https://www.youtube.com/@QWeb-ll7mk','HH Shop | Điện thoại, laptop và phụ kiện công nghệ','HH Shop Cửa Hàng Công Nghệ Thứ hai VN','HH Shop, điện thoại, laptop, phụ kiện, công nghệ, THLTW','2026-10-18 23:10:21','2026-11-01 23:48:53');
/*!40000 ALTER TABLE `cauhinh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chitietdonhang`
--

LOCK TABLES `chitietdonhang` WRITE;
/*!40000 ALTER TABLE `chitietdonhang` DISABLE KEYS */;
INSERT INTO `chitietdonhang` VALUES (1,1,3,15990000,1,15990000,'iPhone 13 Pro',NULL,'queued',0,'2026-10-05 11:04:57',NULL,NULL,NULL),(2,1,4,3990000,1,3990000,'Oppo A57',NULL,'queued',0,'2026-10-05 11:04:57',NULL,NULL,NULL),(3,14,3,15990000,1,15990000,'iPhone 13 Pro',NULL,'queued',0,'2026-10-12 06:07:19',NULL,NULL,NULL),(4,15,16,190000,1,190000,'Gậy selfie',NULL,'queued',0,'2026-10-12 10:47:03',NULL,NULL,NULL),(5,16,17,261000,1,261000,'Đèn selfie',NULL,'queued',0,'2026-10-19 06:25:54',NULL,NULL,NULL),(6,17,17,261000,1,261000,'Đèn selfie',NULL,'queued',0,'2026-10-19 07:04:33',NULL,NULL,NULL),(7,18,17,261000,1,261000,'Đèn selfie',NULL,'queued',0,'2026-10-30 20:17:54',NULL,NULL,NULL),(8,18,33,3490000,2,6980000,'Loa JBL Charge 5',NULL,'queued',0,'2026-10-30 20:17:54',NULL,NULL,NULL),(9,19,32,2490000,1,2490000,'Loa JBL Flip 6',NULL,'queued',0,'2026-10-30 22:30:37',NULL,NULL,NULL),(10,19,33,3490000,1,3490000,'Loa JBL Charge 5',NULL,'queued',0,'2026-10-30 22:30:37',NULL,NULL,NULL),(11,20,33,3490000,1,3490000,'Loa JBL Charge 5',NULL,'queued',0,'2026-10-30 22:36:15',NULL,NULL,NULL),(12,21,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-10-30 22:43:08',NULL,NULL,NULL),(13,22,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-10-30 22:46:09',NULL,NULL,NULL),(14,23,17,261000,1,261000,'Đèn selfie',NULL,'queued',0,'2026-11-02 02:34:51',NULL,NULL,NULL),(15,24,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 02:35:56',NULL,NULL,NULL),(16,25,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 02:50:22',NULL,NULL,NULL),(17,26,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 02:53:56',NULL,NULL,NULL),(18,27,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 02:59:01',NULL,NULL,NULL),(19,28,31,801000,1,801000,'Loa JBL Go 3',NULL,'queued',0,'2026-11-02 05:54:23',NULL,NULL,NULL),(20,29,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 05:58:16',NULL,NULL,NULL),(21,30,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 08:13:41',NULL,NULL,NULL),(22,31,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 08:35:02',NULL,NULL,NULL),(23,32,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 08:35:22',NULL,NULL,NULL),(24,33,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-02 08:36:08',NULL,NULL,NULL),(25,34,33,10000,1,10000,'Loa JBL Charge 5',NULL,'queued',0,'2026-11-06 08:37:56',NULL,NULL,NULL);
/*!40000 ALTER TABLE `chitietdonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chitietgiohang`
--

LOCK TABLES `chitietgiohang` WRITE;
/*!40000 ALTER TABLE `chitietgiohang` DISABLE KEYS */;
/*!40000 ALTER TABLE `chitietgiohang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chitietphieukho`
--

LOCK TABLES `chitietphieukho` WRITE;
/*!40000 ALTER TABLE `chitietphieukho` DISABLE KEYS */;
/*!40000 ALTER TABLE `chitietphieukho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `chudebaiviet`
--

LOCK TABLES `chudebaiviet` WRITE;
/*!40000 ALTER TABLE `chudebaiviet` DISABLE KEYS */;
INSERT INTO `chudebaiviet` VALUES (1,'Tin tức','tin-tuc',0,'Tin tức của HH Shop','2026-10-26 00:11:22',1,'2026-10-26 00:11:22',NULL,NULL,1),(2,'Sự kiện','su-kien',0,'Sự kiện của HH Shop','2026-10-26 00:11:22',1,'2026-10-26 00:11:22',NULL,NULL,1);
/*!40000 ALTER TABLE `chudebaiviet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `coupon_usages`
--

LOCK TABLES `coupon_usages` WRITE;
/*!40000 ALTER TABLE `coupon_usages` DISABLE KEYS */;
INSERT INTO `coupon_usages` VALUES (1,1,7,33,1000,'2026-11-02 01:36:08','2026-11-02 01:36:08');
/*!40000 ALTER TABLE `coupon_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'HUY','percent',10,10000,NULL,10,'2026-11-02 04:08:00','2026-11-03 04:08:00',1,'2026-11-01 21:09:23','2026-11-01 21:09:23');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `danhmuc`
--

LOCK TABLES `danhmuc` WRITE;
/*!40000 ALTER TABLE `danhmuc` DISABLE KEYS */;
INSERT INTO `danhmuc` VALUES (13,'Laptop','hh-sample-laptop','https://cdn.dummyjson.com/product-images/laptops/apple-macbook-pro-14-inch-space-grey/thumbnail.webp',0,2,'Laptop tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1),(14,'Tai nghe','hh-sample-tai-nghe','https://cdn.dummyjson.com/product-images/mobile-accessories/apple-airpods/thumbnail.webp',16,3,'Tai nghe tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-12 01:07:58',NULL,NULL,1),(15,'Sạc điện thoại','hh-sample-sac-dien-thoai','https://cdn.dummyjson.com/product-images/mobile-accessories/apple-iphone-charger/thumbnail.webp',20,4,'Sạc điện thoại tại HH Shop.','2026-10-04 23:57:16',1,'2026-11-01 23:02:18',NULL,NULL,1),(16,'Phụ kiện','hh-sample-phu-kien','https://cdn.dummyjson.com/product-images/mobile-accessories/apple-magsafe-battery-pack/thumbnail.webp',0,5,'Phụ kiện tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-05 00:28:58',NULL,NULL,1),(20,'Điện Thoại','dien-thoai','/storage/images/gW3hzrpmf9tds9yA39czDtRpTCzWQevCEpuwz9n1.jpg',0,0,'Điện thoại tại HH Shop','2026-10-12 04:40:40',1,'2026-10-12 04:40:40',NULL,NULL,1),(21,'Bàn phím','hh-expand-ban-phim','/images/catalog-real/sanpham-19.jpg',0,10,'Bàn phím dành cho học tập, làm việc và giải trí.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1),(22,'Màn hình','hh-expand-man-hinh','/images/catalog-real/sanpham-22.jpg',0,11,'Màn hình dành cho học tập, làm việc và giải trí.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1),(23,'Đồng hồ thông minh','hh-expand-dong-ho-thong-minh','/images/catalog-real/sanpham-26.png',0,12,'Đồng hồ thông minh dành cho học tập, làm việc và giải trí.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1),(24,'Thiết bị lưu trữ','hh-expand-thiet-bi-luu-tru','/images/catalog-real/sanpham-30.jpg',0,13,'Thiết bị lưu trữ dành cho học tập, làm việc và giải trí.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1),(25,'Loa Bluetooth','hh-expand-loa-bluetooth','/images/catalog-real/sanpham-33.png',0,14,'Loa Bluetooth dành cho học tập, làm việc và giải trí.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1),(26,'Gaming','gaming','/images/hierarchy/logitech-pro-x.jpg',0,20,'Thiết bị gaming dành cho góc chơi game và giải trí.','2026-11-01 23:06:20',1,'2026-11-01 23:06:20',NULL,NULL,1),(27,'Bàn phím gaming','ban-phim-gaming','/images/hierarchy/logitech-pro-x.jpg',26,1,'Bàn phím gaming thuộc nhóm Gaming.','2026-11-01 23:06:20',1,'2026-11-01 23:06:20',NULL,NULL,1),(28,'Chuột gaming','chuot-gaming','/images/hierarchy/logitech-g502.jpg',26,2,'Chuột gaming thuộc nhóm Gaming.','2026-11-01 23:06:20',1,'2026-11-01 23:06:20',NULL,NULL,1);
/*!40000 ALTER TABLE `danhmuc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `donhang`
--

LOCK TABLES `donhang` WRITE;
/*!40000 ALTER TABLE `donhang` DISABLE KEYS */;
INSERT INTO `donhang` VALUES (1,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-10-05 04:04:57','2026-10-05 04:04:57',NULL,NULL,2,'HH-01M45VSZFMG0G77PTGWS4F7MHQ',NULL,NULL,'delivery',1,19980000,0,19980000,'unpaid','cod',NULL,NULL,NULL),(2,2,'Khach mau 1','0900000000','khach1@example.com','1 Duong mau, TP Ho Chi Minh',NULL,'2026-10-11 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-001',NULL,NULL,'delivery',1,100000,0,100000,'unpaid','cod',NULL,NULL,NULL),(3,2,'Khach mau 2','0900000000','khach2@example.com','2 Duong mau, TP Ho Chi Minh',NULL,'2026-10-10 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-002',NULL,NULL,'delivery',1,200000,0,200000,'unpaid','cod',NULL,NULL,NULL),(4,2,'Khach mau 3','0900000000','khach3@example.com','3 Duong mau, TP Ho Chi Minh',NULL,'2026-10-09 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-003',NULL,NULL,'delivery',1,300000,0,300000,'unpaid','cod',NULL,NULL,NULL),(5,2,'Khach mau 4','0900000000','khach4@example.com','4 Duong mau, TP Ho Chi Minh',NULL,'2026-10-08 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-004',NULL,NULL,'delivery',1,400000,0,400000,'unpaid','cod',NULL,NULL,NULL),(6,2,'Khach mau 5','0900000000','khach5@example.com','5 Duong mau, TP Ho Chi Minh',NULL,'2026-10-07 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-005',NULL,NULL,'delivery',1,500000,0,500000,'unpaid','cod',NULL,NULL,NULL),(7,2,'Khach mau 6','0900000000','khach6@example.com','6 Duong mau, TP Ho Chi Minh',NULL,'2026-10-06 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-006',NULL,NULL,'delivery',1,600000,0,600000,'unpaid','cod',NULL,NULL,NULL),(8,2,'Khach mau 7','0900000000','khach7@example.com','7 Duong mau, TP Ho Chi Minh',NULL,'2026-10-05 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-007',NULL,NULL,'delivery',1,700000,0,700000,'unpaid','cod',NULL,NULL,NULL),(9,2,'Khach mau 8','0900000000','khach8@example.com','8 Duong mau, TP Ho Chi Minh',NULL,'2026-10-04 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-008',NULL,NULL,'delivery',1,800000,0,800000,'unpaid','cod',NULL,NULL,NULL),(10,2,'Khach mau 9','0900000000','khach9@example.com','9 Duong mau, TP Ho Chi Minh',NULL,'2026-10-03 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-009',NULL,NULL,'delivery',1,900000,0,900000,'unpaid','cod',NULL,NULL,NULL),(11,2,'Khach mau 10','0900000000','khach10@example.com','10 Duong mau, TP Ho Chi Minh',NULL,'2026-10-02 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-010',NULL,NULL,'delivery',1,1000000,0,1000000,'unpaid','cod',NULL,NULL,NULL),(12,2,'Khach mau 11','0900000000','khach11@example.com','11 Duong mau, TP Ho Chi Minh',NULL,'2026-10-01 04:42:31',NULL,NULL,NULL,2,'HH-TEST-ORDER-011',NULL,NULL,'delivery',1,1100000,0,1100000,'unpaid','cod',NULL,NULL,NULL),(13,2,'Khach mau 12','0900000000','khach12@example.com','12 Duong mau, TP Ho Chi Minh',NULL,'2026-09-30 04:42:31','2026-10-11 23:05:18',NULL,NULL,1,'HH-TEST-ORDER-012',NULL,NULL,'delivery',1,1200000,0,1200000,'unpaid','cod',NULL,NULL,NULL),(14,2,'Nguyễn Mạnh Huy','0843212278','huy09747@gmail.com','asd',NULL,'2026-10-11 23:07:19','2026-10-11 23:36:18',NULL,NULL,2,'HH-01M4QBJ0JZ067DN7E7K09QN15B',NULL,NULL,'delivery',1,15990000,0,15990000,'unpaid','cod',NULL,NULL,NULL),(15,5,'Nguyen van A','123456789','nguyenvana@gmail.com','8/6',NULL,'2026-10-12 03:47:03','2026-10-12 03:47:03',NULL,NULL,2,'HH-01M4QVJ7YJ2YX3HDW37HNQTV0V',NULL,NULL,'delivery',1,190000,0,190000,'unpaid','cod',NULL,NULL,NULL),(16,4,'Nguyen Manh Huy','84444444444','huy123@gmail.com','asd',NULL,'2026-10-18 23:25:54','2026-10-18 23:25:54',NULL,NULL,2,'HH-01M59DD30XZQ2YF833JRN212CB',NULL,NULL,'delivery',1,261000,0,261000,'unpaid','cod',NULL,NULL,NULL),(17,6,'asaa','8888888888','huy1234@gmail.com','213',NULL,'2026-10-19 00:04:33','2026-10-19 00:04:33',NULL,NULL,2,'HH-01M59FKV6S6VYZ0GWSD7YMT176',NULL,NULL,'delivery',1,261000,0,261000,'unpaid','cod',NULL,NULL,NULL),(18,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-10-30 13:17:54','2026-10-30 13:17:54',NULL,NULL,2,'HH-01M677CE8Q6XKB2EHFN3M5A0RV',NULL,NULL,'delivery',1,7241000,0,7241000,'unpaid','cod',NULL,NULL,NULL),(19,4,'Nguyen Manh Huy','123456899','huy123@gmail.com','asd',NULL,'2026-10-30 15:30:37','2026-10-30 15:30:37',NULL,NULL,2,'HH-01M67EZEBYY1SRED0ZVS3W810K',NULL,NULL,'delivery',1,5980000,0,5980000,'unpaid','payos',NULL,NULL,NULL),(20,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-10-30 15:36:15','2026-10-30 15:36:15',NULL,NULL,2,'HH-01M67F9R194MNCE6QPJE3BJM46',NULL,NULL,'delivery',1,3490000,0,3490000,'unpaid','payos',NULL,NULL,NULL),(21,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-10-30 15:43:08','2026-10-30 15:43:34',NULL,NULL,2,'HH-01M67FPBSDJ7J3PKZ74TMVQ85M',NULL,NULL,'delivery',1,10000,0,10000,'paid','payos',NULL,NULL,NULL),(22,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-10-30 15:46:09','2026-10-30 15:46:27',NULL,NULL,1,'HH-01M67FVWCRPP831YKT9NXKF03D',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','cod',NULL,NULL,NULL),(23,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-11-01 19:34:51','2026-11-01 19:35:31',NULL,NULL,5,'HH-01M6D1R32RZVT4TXEC81J5JYJJ',NULL,NULL,'delivery',1,261000,0,261000,'unpaid','cod',NULL,NULL,NULL),(24,4,'Nguyen Manh Huy','123456789','huy123@gmail.com','asd',NULL,'2026-11-01 19:35:56','2026-11-01 19:35:56',NULL,NULL,1,'HH-01M6D1T2CK3PEBS54NCVTVC4N4',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','payos',NULL,NULL,NULL),(25,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-01 19:50:22','2026-11-01 19:51:09',NULL,NULL,2,'HH-01M6D2MG3D0FK0VP61WPFB8TNB',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','payos',NULL,NULL,NULL),(26,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-01 19:53:56','2026-11-01 19:54:22',NULL,NULL,2,'HH-01M6D2V0DKANBB7W2Z38PP56DQ',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','cod',NULL,NULL,NULL),(27,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-01 19:59:01','2026-11-01 20:00:34',NULL,NULL,2,'HH-01M6D34AVCRNZHH4A28C4M1NBY',NULL,NULL,'delivery',1,10000,0,10000,'paid','payos',NULL,NULL,NULL),(28,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-01 22:54:23','2026-11-01 22:54:23',NULL,NULL,1,'HH-01M6DD5E5S26GFBT66N2JCB1P3',NULL,NULL,'delivery',1,801000,0,801000,'unpaid','payos',NULL,NULL,NULL),(29,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-01 22:58:16','2026-11-01 22:58:56',NULL,NULL,2,'HH-01M6DDCHWSPMBPT2AE26CQ19ZH',NULL,NULL,'delivery',1,10000,0,10000,'paid','payos',NULL,NULL,NULL),(30,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-02 01:13:41','2026-11-02 01:13:41',NULL,NULL,1,'HH-01M6DN4G8JJSQ9PYWJFG9J69VK',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','payos',NULL,NULL,NULL),(31,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-02 01:35:02','2026-11-02 01:35:02',NULL,NULL,1,'HH-01M6DPBKDXRF27CN1G58MZM369',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','cod',NULL,NULL,NULL),(32,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-02 01:35:22','2026-11-02 01:35:22',NULL,NULL,1,'HH-01M6DPC6XJ5AG6NCS7601GMB4F',NULL,NULL,'delivery',1,10000,0,10000,'unpaid','payos',NULL,NULL,NULL),(33,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-02 01:36:08','2026-11-02 01:37:24',NULL,NULL,4,'HH-01M6DPDK7J73Q7G4KFJ951SE6T',NULL,NULL,'delivery',1,10000,1000,9000,'paid','payos',NULL,NULL,NULL),(34,7,'Nguyen Huy','1234567899','nmhuy101@gmail.com','asd',NULL,'2026-11-06 01:37:56','2026-11-06 01:42:53',NULL,NULL,2,'HH-01M6R03SJ8A82C0RY8WPT46E4N',NULL,NULL,'delivery',1,10000,0,10000,'paid','payos',NULL,NULL,NULL);
/*!40000 ALTER TABLE `donhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (3,7,33,'2026-11-01 20:39:03','2026-11-01 20:39:03');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `giohang`
--

LOCK TABLES `giohang` WRITE;
/*!40000 ALTER TABLE `giohang` DISABLE KEYS */;
INSERT INTO `giohang` VALUES (1,4,'2026-10-05 00:58:55','2026-10-05 00:58:55'),(2,2,'2026-10-11 23:07:13','2026-10-11 23:07:13'),(3,5,'2026-10-12 03:47:01','2026-10-12 03:47:01'),(4,6,'2026-10-19 00:04:26','2026-10-19 00:04:26'),(5,7,'2026-11-01 19:50:19','2026-11-01 19:50:19');
/*!40000 ALTER TABLE `giohang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `khuyenmai`
--

LOCK TABLES `khuyenmai` WRITE;
/*!40000 ALTER TABLE `khuyenmai` DISABLE KEYS */;
/*!40000 ALTER TABLE `khuyenmai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lichsutrangthaidonhang`
--

LOCK TABLES `lichsutrangthaidonhang` WRITE;
/*!40000 ALTER TABLE `lichsutrangthaidonhang` DISABLE KEYS */;
/*!40000 ALTER TABLE `lichsutrangthaidonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lienhe`
--

LOCK TABLES `lienhe` WRITE;
/*!40000 ALTER TABLE `lienhe` DISABLE KEYS */;
/*!40000 ALTER TABLE `lienhe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Trang chủ','/',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,1),(2,'Sản phẩm','/products',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,2),(3,'Danh mục','/#danh-muc',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,3),(4,'Bài viết','/news',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,4),(5,'Giới thiệu','/about',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,5),(6,'Liên hệ','/contact',NULL,'custom','2026-10-26 01:10:47',1,'2026-10-26 01:10:47',NULL,NULL,1,6);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_21_074314_create_personal_access_tokens_table',1),(5,'2026_09_27_142907_add_deleted_at_to_danhmuc_table',1),(6,'2026_10_12_000001_add_discount_percent_to_sanpham',2),(7,'2026_10_26_000001_create_reviews_and_payments',3),(8,'2026_10_26_000002_add_review_visibility',4),(9,'2026_10_26_000003_add_menu_sort_order',5),(10,'2026_11_02_000001_create_shop_features',6),(11,'2026_11_03_000001_complete_ecommerce_features',7),(12,'2026_11_03_000001_add_avatar_to_nguoidung',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nguoidung`
--

LOCK TABLES `nguoidung` WRITE;
/*!40000 ALTER TABLE `nguoidung` DISABLE KEYS */;
INSERT INTO `nguoidung` VALUES (2,'Nguyễn Mạnh Huy','huy09747@gmail.com','0843212278','huy123','$2y$12$FhJrvkxJv/AVpIhlmCPdLe/Ve.HpoA7EUfWLthl.ofEaO/n9qMlPe',NULL,NULL,NULL,'user','2026-09-28 04:55:56',1,'2026-09-28 04:55:56',NULL,NULL,1,NULL),(3,'Admin Test','admin@gmail.com','0901234568','admin123','$2y$12$39MzCox4L1aiwZof3O1msuJT018oKd1Oxso6RXHSZX4sDGOCTjTx2',NULL,NULL,NULL,'admin','2026-09-28 05:05:06',1,'2026-09-28 05:05:06',NULL,NULL,1,NULL),(4,'Nguyen Manh Huy','huy123@gmail.com','123456','huy1234','$2y$12$O0U/Tt2ScLHGUH1JsBxBF.RnS3SQ2jdk0tO3k5SY/ZEUpLzhrKPK.',NULL,'asd',NULL,'user','2026-10-05 00:35:00',1,'2026-10-12 03:02:22',NULL,NULL,1,NULL),(5,'Nguyen van A','nguyenvana@gmail.com','123456789','A1234','$2y$12$3EFDhZyvASSANiQ3rxyyY.9INkHpw/wIkZPVwlLRKwaXMfsuVEziC',NULL,'8/6',NULL,'user','2026-10-12 03:11:19',1,'2026-10-12 03:11:19',NULL,NULL,1,NULL),(6,'asaa','huy1234@gmail.com','8888888888','huy1234','$2y$12$zBOA4qVahyGp2IeMhEU7yOJUYfG384YXyoZwY6dHU16vZExSjTzHi',NULL,NULL,NULL,'user','2026-10-18 23:27:22',1,'2026-10-18 23:27:22',NULL,NULL,1,NULL),(7,'Nguyen Huy','nmhuy101@gmail.com','1234567899','huy123','$2y$12$aqrpWoCXsI4I7WFH8p43..OvSYw7sex1dld6WYW3RH389MyCoUTVW',NULL,'asd',NULL,'user','2026-11-01 19:50:09',1,'2026-11-01 19:50:09',NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `nguoidung` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
INSERT INTO `order_status_history` VALUES (1,30,NULL,1,7,'2026-11-02 01:13:41'),(2,31,NULL,1,7,'2026-11-02 01:35:02'),(3,32,NULL,1,7,'2026-11-02 01:35:22'),(4,33,NULL,1,7,'2026-11-02 01:36:08'),(5,33,1,2,NULL,'2026-11-02 01:36:34'),(6,33,2,3,3,'2026-11-02 01:37:20'),(7,33,3,4,3,'2026-11-02 01:37:24'),(8,34,NULL,1,7,'2026-11-06 01:37:56'),(9,34,1,2,7,'2026-11-06 01:42:53');
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payment_attempts`
--

LOCK TABLES `payment_attempts` WRITE;
/*!40000 ALTER TABLE `payment_attempts` DISABLE KEYS */;
INSERT INTO `payment_attempts` VALUES (1,19,'3534872679267','payos',5980000,'VND','95a5ee016e3c40b998fb4e0395c72013','https://pay.payos.vn/web/95a5ee016e3c40b998fb4e0395c72013','pending','2026-10-30 15:31:26','2026-10-30 15:31:27'),(2,20,'6674419695867','payos',3490000,'VND','f02dd10587414e429763a46ca6c3be61','https://pay.payos.vn/web/f02dd10587414e429763a46ca6c3be61','pending','2026-10-30 15:36:35','2026-10-30 15:36:36'),(3,21,'1958347366862','payos',10000,'VND','9c736d56bf7f42cd8b5ac1ddf020959d','https://pay.payos.vn/web/9c736d56bf7f42cd8b5ac1ddf020959d','paid','2026-10-30 15:43:09','2026-10-30 15:43:34'),(4,24,'2728347780315','payos',10000,'VND','f90137db0ec648b0b4b652d3029f3719','https://pay.payos.vn/web/f90137db0ec648b0b4b652d3029f3719','pending','2026-11-01 19:35:57','2026-11-01 19:36:17'),(5,25,'8987339968992','payos',10000,'VND','090e385eb157409d934da88b502c1919','https://pay.payos.vn/web/090e385eb157409d934da88b502c1919','pending','2026-11-01 19:50:23','2026-11-01 19:51:00'),(6,27,'3445213533643','payos',10000,'VND','b5624f88d6dd48768ca73f0c4b1b6e20','https://pay.payos.vn/web/b5624f88d6dd48768ca73f0c4b1b6e20','paid','2026-11-01 19:59:02','2026-11-01 20:00:34'),(7,28,'7972730034176','payos',801000,'VND','0ed083a617f448ee9f380f4c23d2e225','https://pay.payos.vn/web/0ed083a617f448ee9f380f4c23d2e225','pending','2026-11-01 22:54:27','2026-11-01 23:17:46'),(8,29,'6368173360150','payos',10000,'VND','389b1ce5e7a248dc9ee20e848582a46d','https://pay.payos.vn/web/389b1ce5e7a248dc9ee20e848582a46d','paid','2026-11-01 22:58:17','2026-11-01 22:58:56'),(9,30,'5051657656464','payos',10000,'VND','214050aa7192494689eb4d94a1762e85','https://pay.payos.vn/web/214050aa7192494689eb4d94a1762e85','pending','2026-11-02 01:13:42','2026-11-02 01:13:43'),(10,32,'7748386609905','payos',10000,'VND','122e4f664dd942cf8c523378aa779d36','https://pay.payos.vn/web/122e4f664dd942cf8c523378aa779d36','pending','2026-11-02 01:35:23','2026-11-02 01:35:25'),(11,33,'8766894921611','payos',9000,'VND','030ca12520fa452d8e7ba6cddfb94327','https://pay.payos.vn/web/030ca12520fa452d8e7ba6cddfb94327','paid','2026-11-02 01:36:08','2026-11-02 01:36:34'),(12,34,'3728563115869','payos',10000,'VND','c896f1499011495db66fd46dd6c2c17f','https://pay.payos.vn/web/c896f1499011495db66fd46dd6c2c17f','paid','2026-11-06 01:38:00','2026-11-06 01:42:53');
/*!40000 ALTER TABLE `payment_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `phieukho`
--

LOCK TABLES `phieukho` WRITE;
/*!40000 ALTER TABLE `phieukho` DISABLE KEYS */;
/*!40000 ALTER TABLE `phieukho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,33,4,5,'sản phẩm tuyệt','2026-10-30 13:20:59','2026-10-30 13:58:20',1),(3,33,7,1,'sản phẩm tệ','2026-11-02 01:12:00','2026-11-02 01:12:00',1),(4,35,7,5,'21','2026-11-02 01:38:16','2026-11-02 01:38:16',1),(5,34,7,3,'asd','2026-11-06 01:56:23','2026-11-06 01:56:23',1);
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sanpham`
--

LOCK TABLES `sanpham` WRITE;
/*!40000 ALTER TABLE `sanpham` DISABLE KEYS */;
INSERT INTO `sanpham` VALUES (2,20,1,'iPhone 15','iphone-15',20000000,21000000,'/storage/images/rNjsexSzeCDlnlVHJt6scHV2NVFK7P6a60CUQWNL.jpg',10,'Điện thoại iPhone 15',NULL,'2026-09-28 08:39:10',1,'2026-10-12 04:47:54',NULL,NULL,1,NULL,'Chiếc',15,1,0,0),(3,20,1,'iPhone 13 Pro','hh-sample-product-1',11193000,15990000,'/storage/images/FpxHAlNl8Lf94Zp1NvNjndtb9BG2lWI0fdLAjbFz.jpg',8,'iPhone 13 Pro. Thiết kế tiện dụng cho công việc, học tập và giải trí.','iPhone 13 Pro tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-12 04:53:53',NULL,NULL,1,'HH-SAMPLE-001','Cái',0,1,1,0),(4,20,1,'iPhone 13 Pro1','iphone-13-pro1',10000000,15000000,'/storage/images/C3PXjn0y7IFyP1mtEZmwmBJppqQfUlg4Jh12xATL.png',10,'Oppo A57. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Oppo A57 tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-12 04:53:37',NULL,NULL,1,'HH-SAMPLE-002','Cái',0,1,1,0),(5,20,1,'Oppo F19 Pro Plus','hh-sample-product-3',4893000,6990000,'https://cdn.dummyjson.com/product-images/smartphones/oppo-f19-pro-plus/thumbnail.webp',12,'Oppo F19 Pro Plus. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Oppo F19 Pro Plus tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-12 04:49:38',NULL,NULL,1,'HH-SAMPLE-003','Cái',0,1,1,0),(6,13,2,'MacBook Pro 14','hh-sample-product-4',23093000,32990000,'https://cdn.dummyjson.com/product-images/laptops/apple-macbook-pro-14-inch-space-grey/thumbnail.webp',13,'MacBook Pro 14. Thiết kế tiện dụng cho công việc, học tập và giải trí.','MacBook Pro 14 tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-004','cái',0,1,1,0),(7,13,2,'Huawei Matebook X Pro','hh-sample-product-5',16092999,22990000,'https://cdn.dummyjson.com/product-images/laptops/huawei-matebook-x-pro/thumbnail.webp',14,'Huawei Matebook X Pro. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Huawei Matebook X Pro tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-005','cái',0,1,0,0),(8,13,2,'Asus Zenbook Pro','hh-sample-product-6',20293000,28990000,'https://cdn.dummyjson.com/product-images/laptops/asus-zenbook-pro-dual-screen-laptop/thumbnail.webp',15,'Asus Zenbook Pro. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Asus Zenbook Pro tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-006','cái',0,1,0,0),(9,14,2,'Apple AirPods','hh-sample-product-7',2092999,2990000,'https://cdn.dummyjson.com/product-images/mobile-accessories/apple-airpods/thumbnail.webp',16,'Apple AirPods. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Apple AirPods tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-007','cái',0,1,0,0),(10,14,2,'AirPods Max Silver','hh-sample-product-8',7692999,10990000,'https://cdn.dummyjson.com/product-images/mobile-accessories/apple-airpods-max-silver/thumbnail.webp',17,'AirPods Max Silver. Thiết kế tiện dụng cho công việc, học tập và giải trí.','AirPods Max Silver tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-008','cái',0,1,0,0),(11,14,2,'Beats Flex','hh-sample-product-9',903000,1290000,'https://cdn.dummyjson.com/product-images/mobile-accessories/beats-flex-wireless-earphones/thumbnail.webp',18,'Beats Flex. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Beats Flex tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-009','cái',0,1,0,0),(12,15,3,'Sạc iPhone','hh-sample-product-10',343000,490000,'https://cdn.dummyjson.com/product-images/mobile-accessories/apple-iphone-charger/thumbnail.webp',19,'Sạc iPhone. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Sạc iPhone tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-18 23:04:11',NULL,NULL,1,'HH-SAMPLE-010','Cái',0,1,1,0),(13,15,2,'Sạc không dây AirPower','hh-sample-product-11',693000,990000,'https://cdn.dummyjson.com/product-images/mobile-accessories/apple-airpower-wireless-charger/thumbnail.webp',20,'Sạc không dây AirPower. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Sạc không dây AirPower tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-011','cái',0,1,0,0),(14,15,2,'Pin MagSafe','hh-sample-product-12',1042999,1490000,'https://cdn.dummyjson.com/product-images/mobile-accessories/apple-magsafe-battery-pack/thumbnail.webp',21,'Pin MagSafe. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Pin MagSafe tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-04 23:57:16',NULL,NULL,1,'HH-SAMPLE-012','cái',0,1,0,0),(15,16,1,'Ốp lưng iPhone 12','hh-sample-product-13',273000,390000,'https://cdn.dummyjson.com/product-images/mobile-accessories/iphone-12-silicone-case-with-magsafe-plum/thumbnail.webp',22,'Ốp lưng iPhone 12. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Ốp lưng iPhone 12 tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-18 23:04:28',NULL,NULL,1,'HH-SAMPLE-013','Cái',0,1,0,0),(16,16,3,'Gậy selfie','hh-sample-product-14',133000,190000,'https://cdn.dummyjson.com/product-images/mobile-accessories/selfie-stick-monopod/thumbnail.webp',22,'Gậy selfie. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Gậy selfie tại HH Shop.','2026-10-04 23:57:16',1,'2026-10-18 23:04:20',NULL,NULL,1,'HH-SAMPLE-014','Cái',0,1,0,0),(17,16,1,'Đèn selfie','hh-sample-product-15',203000,290000,'https://cdn.dummyjson.com/product-images/mobile-accessories/selfie-lamp-with-iphone/thumbnail.webp',21,'Đèn selfie. Thiết kế tiện dụng cho công việc, học tập và giải trí.','Đèn selfie tại HH Shop.','2026-10-04 23:57:16',1,'2026-11-01 19:35:31',NULL,NULL,1,'HH-SAMPLE-015','Cái',0,1,0,10),(19,21,4,'Bàn phím Logitech K120','hh-expand-ban-phim-logitech-k120',139300,199000,'/images/catalog-real/sanpham-19.jpg',20,'Bàn phím Logitech K120\nSản phẩm mẫu thuộc nhóm Bàn phím.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Bàn phím Logitech K120 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-001','cái',0,1,0,10),(20,21,4,'Bàn phím Logitech K380','hh-expand-ban-phim-logitech-k380',553000,790000,'/images/catalog-real/sanpham-20.jpg',21,'Bàn phím Logitech K380\nSản phẩm mẫu thuộc nhóm Bàn phím.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Bàn phím Logitech K380 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-002','cái',0,1,1,0),(21,21,4,'Bàn phím Logitech MX Keys','hh-expand-ban-phim-logitech-mx-keys',1673000,2390000,'/images/catalog-real/sanpham-21.jpg',22,'Bàn phím Logitech MX Keys\nSản phẩm mẫu thuộc nhóm Bàn phím.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Bàn phím Logitech MX Keys — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-003','cái',0,1,0,0),(22,22,5,'Màn hình Dell P2422H','hh-expand-man-hinh-dell-p2422h',2303000,3290000,'/images/catalog-real/sanpham-22.jpg',23,'Màn hình Dell P2422H\nSản phẩm mẫu thuộc nhóm Màn hình.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Màn hình Dell P2422H — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-004','cái',0,1,0,10),(23,22,5,'Màn hình Dell S2721HN','hh-expand-man-hinh-dell-s2721hn',2933000,4190000,'/images/catalog-real/sanpham-23.webp',24,'Màn hình Dell S2721HN\nSản phẩm mẫu thuộc nhóm Màn hình.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Màn hình Dell S2721HN — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-005','cái',0,1,1,0),(24,22,5,'Màn hình Dell U2723QE','hh-expand-man-hinh-dell-u2723qe',8393000,11990000,'/images/catalog-real/sanpham-24.jpg',25,'Màn hình Dell U2723QE\nSản phẩm mẫu thuộc nhóm Màn hình.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Màn hình Dell U2723QE — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-006','cái',0,1,0,0),(25,23,6,'Đồng hồ Xiaomi Smart Band 8','hh-expand-dong-ho-xiaomi-smart-band-8',553000,790000,'/images/catalog-real/sanpham-25.jpg',26,'Đồng hồ Xiaomi Smart Band 8\nSản phẩm mẫu thuộc nhóm Đồng hồ thông minh.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Đồng hồ Xiaomi Smart Band 8 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-007','cái',0,1,0,10),(26,23,6,'Đồng hồ Xiaomi Redmi Watch 3','hh-expand-dong-ho-xiaomi-redmi-watch-3',1042999,1490000,'/images/catalog-real/sanpham-26.png',27,'Đồng hồ Xiaomi Redmi Watch 3\nSản phẩm mẫu thuộc nhóm Đồng hồ thông minh.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Đồng hồ Xiaomi Redmi Watch 3 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-008','cái',0,1,1,0),(27,23,6,'Đồng hồ Xiaomi Watch S1','hh-expand-dong-ho-xiaomi-watch-s1',2092999,2990000,'/images/catalog-real/sanpham-27.jpg',28,'Đồng hồ Xiaomi Watch S1\nSản phẩm mẫu thuộc nhóm Đồng hồ thông minh.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Đồng hồ Xiaomi Watch S1 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-009','cái',0,1,0,0),(28,24,7,'USB Kingston DataTraveler 64GB','hh-expand-usb-kingston-datatraveler-64gb',104300,149000,'/images/catalog-real/sanpham-28.jpg',29,'USB Kingston DataTraveler 64GB\nSản phẩm mẫu thuộc nhóm Thiết bị lưu trữ.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','USB Kingston DataTraveler 64GB — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-010','cái',0,1,0,10),(29,24,7,'Thẻ nhớ Kingston Canvas 128GB','hh-expand-the-nho-kingston-canvas-128gb',209300,299000,'/images/catalog-real/sanpham-29.jpg',30,'Thẻ nhớ Kingston Canvas 128GB\nSản phẩm mẫu thuộc nhóm Thiết bị lưu trữ.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Thẻ nhớ Kingston Canvas 128GB — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-011','cái',0,1,1,0),(30,24,7,'SSD Kingston NV2 1TB','hh-expand-ssd-kingston-nv2-1tb',1113000,1590000,'/images/catalog-real/sanpham-30.jpg',31,'SSD Kingston NV2 1TB\nSản phẩm mẫu thuộc nhóm Thiết bị lưu trữ.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','SSD Kingston NV2 1TB — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-26 01:10:46',NULL,NULL,1,'HH-EXPAND-012','cái',0,1,0,0),(31,25,8,'Loa JBL Go 3','hh-expand-loa-jbl-go-3',623000,890000,'/images/catalog-real/sanpham-31.jpg',31,'Loa JBL Go 3\nSản phẩm mẫu thuộc nhóm Loa Bluetooth.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Loa JBL Go 3 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-11-01 22:54:23',NULL,NULL,1,'HH-EXPAND-013','cái',0,1,0,10),(32,25,8,'Loa JBL Flip 6','hh-expand-loa-jbl-flip-6',1743000,2490000,'/images/catalog-real/sanpham-32.png',32,'Loa JBL Flip 6\nSản phẩm mẫu thuộc nhóm Loa Bluetooth.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Loa JBL Flip 6 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-10-30 15:30:37',NULL,NULL,1,'HH-EXPAND-014','cái',0,1,1,0),(33,25,8,'Loa JBL Charge 5','hh-expand-loa-jbl-charge-5',1000,10000,'/images/catalog-real/sanpham-33.png',18,'Loa JBL Charge 5\nSản phẩm mẫu thuộc nhóm Loa Bluetooth.\nGiá và tồn kho là dữ liệu minh họa cho đồ án. Hình ảnh là hình minh họa nhóm sản phẩm, không phải ảnh thực tế.\nLiên hệ cửa hàng để được tư vấn trước khi đặt mua.','Loa JBL Charge 5 — lựa chọn cho không gian công nghệ của bạn.','2026-10-26 01:10:46',1,'2026-11-06 01:37:56',NULL,NULL,1,'HH-EXPAND-015','Cái',0,1,0,0),(34,27,4,'Bàn phím Logitech G Pro X','ban-phim-logitech-g-pro-x',1800000,2490000,'/images/hierarchy/logitech-pro-x.jpg',15,'Bàn phím cơ dạng tenkeyless, đèn RGB và kết nối USB, phù hợp chơi game và làm việc.','Bàn phím Logitech G Pro X dành cho góc gaming của bạn.','2026-11-01 23:06:20',1,'2026-11-01 23:06:20',NULL,NULL,1,'HH-GAMING-1','cái',0,1,0,0),(35,28,4,'Chuột Logitech G502 HERO','chuot-logitech-g502-hero',700000,1090000,'/images/hierarchy/logitech-g502.jpg',15,'Chuột gaming có dây, thiết kế công thái học, nhiều nút lập trình và cảm biến HERO.','Chuột Logitech G502 HERO dành cho góc gaming của bạn.','2026-11-01 23:06:20',1,'2026-11-01 23:06:20',NULL,NULL,1,'HH-GAMING-2','cái',0,1,0,0);
/*!40000 ALTER TABLE `sanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `stock_document_items`
--

LOCK TABLES `stock_document_items` WRITE;
/*!40000 ALTER TABLE `stock_document_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_document_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `stock_documents`
--

LOCK TABLES `stock_documents` WRITE;
/*!40000 ALTER TABLE `stock_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `thuonghieu`
--

LOCK TABLES `thuonghieu` WRITE;
/*!40000 ALTER TABLE `thuonghieu` DISABLE KEYS */;
INSERT INTO `thuonghieu` VALUES (1,'Apple','apple',0,'','/images/catalog-real/thuonghieu-1.png','2026-09-28 10:09:20',1,NULL,'',0,NULL,NULL,NULL,1),(3,'Samsung','samsung',0,'asd','/images/catalog-real/thuonghieu-3.png','2026-10-18 23:03:39',1,NULL,'asd',0,'2026-10-18 23:03:39',NULL,NULL,1),(4,'Logitech','logitech',0,'Danh mục sản phẩm Logitech tại HH Shop.','/images/catalog-real/thuonghieu-4.jpg','2026-10-26 01:10:46',1,NULL,'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.',0,'2026-10-26 01:10:46',NULL,NULL,1),(5,'Dell','dell',0,'Danh mục sản phẩm Dell tại HH Shop.','/images/catalog-real/thuonghieu-5.png','2026-10-26 01:10:46',1,NULL,'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.',0,'2026-10-26 01:10:46',NULL,NULL,1),(6,'Xiaomi','xiaomi',0,'Danh mục sản phẩm Xiaomi tại HH Shop.','/images/catalog-real/thuonghieu-6.png','2026-10-26 01:10:46',1,NULL,'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.',0,'2026-10-26 01:10:46',NULL,NULL,1),(7,'Kingston','kingston',0,'Danh mục sản phẩm Kingston tại HH Shop.','/images/catalog-real/thuonghieu-7.webp','2026-10-26 01:10:46',1,NULL,'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.',0,'2026-10-26 01:10:46',NULL,NULL,1),(8,'JBL','jbl',0,'Danh mục sản phẩm JBL tại HH Shop.','/images/catalog-real/thuonghieu-8.png','2026-10-26 01:10:46',1,NULL,'Thông tin và giá bán trong bộ dữ liệu này phục vụ minh họa đồ án.',0,'2026-10-26 01:10:46',NULL,NULL,1);
/*!40000 ALTER TABLE `thuonghieu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `trangdon`
--

LOCK TABLES `trangdon` WRITE;
/*!40000 ALTER TABLE `trangdon` DISABLE KEYS */;
/*!40000 ALTER TABLE `trangdon` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-06 15:59:49
