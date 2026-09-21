-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for thltw_huy_hoa_24e
CREATE DATABASE IF NOT EXISTS `thltw_huy_hoa_24e` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `thltw_huy_hoa_24e`;

-- Dumping structure for table thltw_huy_hoa_24e.baiviet
CREATE TABLE IF NOT EXISTS `baiviet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chude_id` bigint unsigned DEFAULT NULL,
  `tieude` varchar(255) NOT NULL,
  `noidung` text NOT NULL,
  `hinhanh` varchar(255) DEFAULT NULL,
  `trangthai` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_baiviet_chudebaiviet` (`chude_id`),
  CONSTRAINT `fk_baiviet_chudebaiviet` FOREIGN KEY (`chude_id`) REFERENCES `chudebaiviet` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.banner
CREATE TABLE IF NOT EXISTS `banner` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tieude` varchar(255) DEFAULT NULL,
  `hinhanh` varchar(255) NOT NULL,
  `duongdan` varchar(255) DEFAULT NULL,
  `thutu` int unsigned NOT NULL DEFAULT '0',
  `trangthai` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.chitietdonhang
CREATE TABLE IF NOT EXISTS `chitietdonhang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `donhang_id` bigint unsigned NOT NULL,
  `sanpham_id` bigint unsigned NOT NULL,
  `tensanpham` varchar(200) NOT NULL,
  `dongia` decimal(15,2) NOT NULL,
  `soluong` int unsigned NOT NULL DEFAULT '1',
  `thanhtien` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ctdonhang_donhang` (`donhang_id`),
  KEY `fk_ctdonhang_sanpham` (`sanpham_id`),
  CONSTRAINT `fk_ctdonhang_donhang` FOREIGN KEY (`donhang_id`) REFERENCES `donhang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ctdonhang_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.chitietgiohang
CREATE TABLE IF NOT EXISTS `chitietgiohang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `giohang_id` bigint unsigned NOT NULL,
  `sanpham_id` bigint unsigned NOT NULL,
  `soluong` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_ctgiohang_giohang` (`giohang_id`),
  KEY `fk_ctgiohang_sanpham` (`sanpham_id`),
  CONSTRAINT `fk_ctgiohang_giohang` FOREIGN KEY (`giohang_id`) REFERENCES `giohang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ctgiohang_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.chudebaiviet
CREATE TABLE IF NOT EXISTS `chudebaiviet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenchude` varchar(255) NOT NULL,
  `trangthai` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.danhmuc
CREATE TABLE IF NOT EXISTS `danhmuc` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tendanhmuc` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `noibat` tinyint(1) NOT NULL DEFAULT '0',
  `trangthai` enum('hienthi','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hienthi',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.donhang
CREATE TABLE IF NOT EXISTS `donhang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoidung_id` bigint unsigned NOT NULL,
  `khuyenmai_id` bigint unsigned DEFAULT NULL,
  `hoten_nguoinhan` varchar(150) NOT NULL,
  `sodienthoai` varchar(20) NOT NULL,
  `diachi_giaohang` varchar(255) NOT NULL,
  `tongtien` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tiengiam` decimal(15,2) NOT NULL DEFAULT '0.00',
  `phuongthuc_thanhtoan` varchar(50) NOT NULL,
  `trangthai` enum('cho_xac_nhan','da_xac_nhan','dang_giao','da_giao','da_huy') NOT NULL DEFAULT 'cho_xac_nhan',
  `ghichu` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_donhang_nguoidung` (`nguoidung_id`),
  KEY `fk_donhang_khuyenmai` (`khuyenmai_id`),
  CONSTRAINT `fk_donhang_khuyenmai` FOREIGN KEY (`khuyenmai_id`) REFERENCES `khuyenmai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_donhang_nguoidung` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.giohang
CREATE TABLE IF NOT EXISTS `giohang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nguoidung_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_giohang_nguoidung` (`nguoidung_id`),
  CONSTRAINT `fk_giohang_nguoidung` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.lienhe
CREATE TABLE IF NOT EXISTS `lienhe` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hoten` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `sodienthoai` varchar(20) DEFAULT NULL,
  `noidung` text NOT NULL,
  `trangthai` enum('chua_xu_ly','da_xu_ly') NOT NULL DEFAULT 'chua_xu_ly',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenmenu` varchar(255) NOT NULL,
  `duongdan` varchar(255) NOT NULL,
  `thutu` int unsigned NOT NULL DEFAULT '0',
  `trangthai` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.nguoidung
CREATE TABLE IF NOT EXISTS `nguoidung` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hoten` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sodienthoai` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diachi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matkhau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vaitro` enum('thanhvien','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'thanhvien',
  `trangthai` enum('hoatdong','khoa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hoatdong',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.sanpham
CREATE TABLE IF NOT EXISTS `sanpham` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `danhmuc_id` bigint unsigned NOT NULL,
  `thuonghieu_id` bigint unsigned NOT NULL,
  `tensanpham` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinhanh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia` decimal(15,2) NOT NULL DEFAULT '0.00',
  `giakhuyenmai` decimal(15,2) DEFAULT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `soluongton` int unsigned NOT NULL DEFAULT '0',
  `trangthai` enum('hienthi','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hienthi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sanpham_danhmuc` (`danhmuc_id`),
  KEY `fk_sanpham_thuonghieu` (`thuonghieu_id`),
  CONSTRAINT `fk_sanpham_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `danhmuc` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_sanpham_thuonghieu` FOREIGN KEY (`thuonghieu_id`) REFERENCES `thuonghieu` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.thuonghieu
CREATE TABLE IF NOT EXISTS `thuonghieu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenthuonghieu` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangthai` enum('hienthi','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hienthi',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table thltw_huy_hoa_24e.trangdon
CREATE TABLE IF NOT EXISTS `trangdon` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tieude` varchar(255) NOT NULL,
  `noidung` text NOT NULL,
  `trangthai` enum('visible','hidden') NOT NULL DEFAULT 'visible',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
