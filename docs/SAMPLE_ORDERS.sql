-- Chon database cua project trong HeidiSQL truoc khi chay.
-- Tao 12 don mau chi de xem danh sach; khong co chi tiet san pham.
-- Khong tru ton kho, khong sua don cu. Chay lai khong them trung ma.
-- Can it nhat mot tai khoan nguoidung chua xoa.
SET NAMES utf8mb4;
START TRANSACTION;
SET @sample_user = (SELECT id FROM nguoidung WHERE deleted_at IS NULL ORDER BY id LIMIT 1);

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-001',
    'Khach mau 1', '0900000000', 'khach1@example.com',
    '1 Duong mau, TP Ho Chi Minh', 100000, 0, 100000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 1 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-001');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-002',
    'Khach mau 2', '0900000000', 'khach2@example.com',
    '2 Duong mau, TP Ho Chi Minh', 200000, 0, 200000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 2 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-002');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-003',
    'Khach mau 3', '0900000000', 'khach3@example.com',
    '3 Duong mau, TP Ho Chi Minh', 300000, 0, 300000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 3 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-003');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-004',
    'Khach mau 4', '0900000000', 'khach4@example.com',
    '4 Duong mau, TP Ho Chi Minh', 400000, 0, 400000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 4 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-004');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-005',
    'Khach mau 5', '0900000000', 'khach5@example.com',
    '5 Duong mau, TP Ho Chi Minh', 500000, 0, 500000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 5 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-005');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-006',
    'Khach mau 6', '0900000000', 'khach6@example.com',
    '6 Duong mau, TP Ho Chi Minh', 600000, 0, 600000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 6 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-006');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-007',
    'Khach mau 7', '0900000000', 'khach7@example.com',
    '7 Duong mau, TP Ho Chi Minh', 700000, 0, 700000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 7 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-007');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-008',
    'Khach mau 8', '0900000000', 'khach8@example.com',
    '8 Duong mau, TP Ho Chi Minh', 800000, 0, 800000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 8 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-008');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-009',
    'Khach mau 9', '0900000000', 'khach9@example.com',
    '9 Duong mau, TP Ho Chi Minh', 900000, 0, 900000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 9 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-009');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-010',
    'Khach mau 10', '0900000000', 'khach10@example.com',
    '10 Duong mau, TP Ho Chi Minh', 1000000, 0, 1000000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 10 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-010');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-011',
    'Khach mau 11', '0900000000', 'khach11@example.com',
    '11 Duong mau, TP Ho Chi Minh', 1100000, 0, 1100000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 11 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-011');

INSERT INTO donhang
    (user_id, code, name, phone, email, address, subtotal, discount, total,
     status, order_type, payment_method, payment_status, created_at)
SELECT @sample_user, 'HH-TEST-ORDER-012',
    'Khach mau 12', '0900000000', 'khach12@example.com',
    '12 Duong mau, TP Ho Chi Minh', 1200000, 0, 1200000,
    2, 'delivery', 'cod', 'unpaid', DATE_SUB(NOW(), INTERVAL 12 DAY)
WHERE @sample_user IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM donhang WHERE code = 'HH-TEST-ORDER-012');

COMMIT;
SELECT id, code, name, total FROM donhang WHERE code LIKE 'HH-TEST-ORDER-%' ORDER BY id;

