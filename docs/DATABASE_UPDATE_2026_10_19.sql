-- Dong bo cau truc bang cauhinh cho chuc nang Cau hinh website.
-- Chon database du an truoc khi chay. Khong DROP, TRUNCATE hay ghi de du lieu.
-- Bang da ton tai se duoc giu nguyen; script khong ALTER bang khac cau truc.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `cauhinh` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenwebsite` VARCHAR(255) NOT NULL,
    `logo` VARCHAR(255) DEFAULT NULL,
    `favicon` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `sodienthoai` VARCHAR(20) DEFAULT NULL,
    `diachi` VARCHAR(255) DEFAULT NULL,
    `facebook` VARCHAR(255) DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `youtube` VARCHAR(255) DEFAULT NULL,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `seo_description` TEXT,
    `seo_keywords` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chi them cau hinh mac dinh neu bang trong. Khong ghi de cau hinh cua thanh vien.
INSERT INTO `cauhinh` (`tenwebsite`, `seo_title`, `seo_description`)
SELECT 'HH Shop', 'HH Shop | Công nghệ mỗi ngày', 'Khám phá sản phẩm công nghệ tại HH Shop.'
WHERE NOT EXISTS (SELECT 1 FROM `cauhinh`);

SHOW COLUMNS FROM `cauhinh`;
