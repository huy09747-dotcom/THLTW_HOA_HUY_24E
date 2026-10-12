-- Chon dung database cua project trong HeidiSQL truoc khi chay.
-- Chi them cot discount_percent neu chua co; khong xoa du lieu.
SET @hh_update_sql = IF(
    EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'sanpham'
          AND COLUMN_NAME = 'discount_percent'
    ),
    'SELECT ''Cot discount_percent da ton tai, khong thay doi.'' AS message',
    'ALTER TABLE sanpham ADD COLUMN discount_percent TINYINT UNSIGNED NOT NULL DEFAULT 0'
);

PREPARE hh_update_statement FROM @hh_update_sql;
EXECUTE hh_update_statement;
DEALLOCATE PREPARE hh_update_statement;

SHOW COLUMNS FROM sanpham LIKE 'discount_percent';
