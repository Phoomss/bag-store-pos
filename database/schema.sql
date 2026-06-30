-- Bag POS and Inventory Management System Database Schema
-- Target: MySQL 8.0

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `inventory_adjustments`;
DROP TABLE IF EXISTS `sale_payments`;
DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `purchase_payments`;
DROP TABLE IF EXISTS `purchase_items`;
DROP TABLE IF EXISTS `purchases`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `login_history`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `user_permissions`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Roles Table
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Permissions Table
CREATE TABLE `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Role Permissions (RBAC Pivot)
CREATE TABLE `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Audit Logs Table
CREATE TABLE `audit_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Login History Table
CREATE TABLE `login_history` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `email` VARCHAR(100) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `login_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Success', 'Failed') NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Categories Table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Brands Table
CREATE TABLE `brands` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Products Table
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sku` VARCHAR(50) NOT NULL UNIQUE,
    `barcode` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `brand_id` INT NULL,
    `category_id` INT NULL,
    `color` VARCHAR(50) NULL,
    `material` VARCHAR(100) NULL,
    `size` VARCHAR(50) NULL,
    `cost_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `selling_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `promotion_price` DECIMAL(10,2) NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `min_stock` INT NOT NULL DEFAULT 5,
    `description` TEXT NULL,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
    INDEX `idx_products_sku` (`sku`),
    INDEX `idx_products_barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Product Images Table
CREATE TABLE `product_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Suppliers Table
CREATE TABLE `suppliers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL UNIQUE,
    `contact_name` VARCHAR(100) NULL,
    `phone` VARCHAR(30) NULL,
    `email` VARCHAR(100) NULL,
    `address` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Customers Table
CREATE TABLE `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NOT NULL UNIQUE,
    `email` VARCHAR(100) NULL,
    `birthday` DATE NULL,
    `gender` ENUM('Male', 'Female', 'Other') NULL,
    `address` TEXT NULL,
    `reward_points` INT DEFAULT 0,
    `membership_level` ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_customers_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Purchases (Inventory Stock-In / Purchase Orders)
CREATE TABLE `purchases` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `purchase_order_no` VARCHAR(50) NOT NULL UNIQUE,
    `supplier_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `status` ENUM('Ordered', 'Received', 'Partial', 'Cancelled') DEFAULT 'Ordered',
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `balance_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_status` ENUM('Paid', 'Unpaid', 'Partial') DEFAULT 'Unpaid',
    `order_date` DATE NOT NULL,
    `received_date` DATE NULL,
    `invoice_no` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Purchase Items
CREATE TABLE `purchase_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `purchase_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `cost_price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `received_quantity` INT DEFAULT 0,
    `subtotal` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Purchase Payments
CREATE TABLE `purchase_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `purchase_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('Cash', 'Bank Transfer', 'Cheque') NOT NULL,
    `payment_date` DATE NOT NULL,
    `reference_no` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Sales Table
CREATE TABLE `sales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_no` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT NULL,
    `user_id` INT NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `coupon_code` VARCHAR(50) NULL,
    `vat_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `change_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(50) NOT NULL, -- 'Cash', 'PromptPay QR', 'Credit Card', 'Bank Transfer', 'Mixed'
    `payment_status` ENUM('Paid', 'Refunded', 'Partially Refunded') DEFAULT 'Paid',
    `status` ENUM('Completed', 'Held', 'Cancelled') DEFAULT 'Completed',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    INDEX `idx_sales_invoice` (`invoice_no`),
    INDEX `idx_sales_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Sale Items
CREATE TABLE `sale_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sale_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `selling_price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `subtotal` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Sale Payments (For Mixed Payments support)
CREATE TABLE `sale_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sale_id` INT NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `reference_no` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Inventory Adjustments
CREATE TABLE `inventory_adjustments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` ENUM('Adjustment', 'Transfer', 'Damaged', 'Lost') NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL, -- can be positive (increase) or negative (decrease)
    `user_id` INT NOT NULL,
    `reason` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Stock Movements (For FIFO & Audit trail)
CREATE TABLE `stock_movements` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `type` ENUM('Purchase', 'Sale', 'Adjustment', 'Transfer', 'Damage', 'Lost', 'Return') NOT NULL,
    `reference_id` INT NULL, -- Maps to purchase_id, sale_id, or adjustment_id
    `quantity` INT NOT NULL, -- Net change (e.g. +10, -5)
    `remaining_stock` INT NOT NULL,
    `cost_price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Expenses Table
CREATE TABLE `expenses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` ENUM('Utilities', 'Salary', 'Rent', 'Internet', 'Transportation', 'Marketing', 'Maintenance', 'Others') NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `description` TEXT NULL,
    `expense_date` DATE NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Settings Table
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) NOT NULL UNIQUE,
    `value_data` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==================== SEED DATA ====================

-- Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Owner', 'สิทธิ์ควบคุมร้านค้าและระบบทั้งหมด'),
(2, 'Admin', 'สิทธิ์ดูแลจัดการระบบทั่วไป'),
(3, 'Cashier', 'สิทธิ์จัดการหน้าจอขายและคืนสินค้า'),
(4, 'Warehouse', 'สิทธิ์จัดการคลังสินค้า ตรวจนับสต็อก และสั่งซื้อสินค้า');

-- Permissions
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'manage_users', 'สร้าง ดู แก้ไข และลบข้อมูลพนักงาน'),
(2, 'manage_settings', 'ตั้งค่าข้อมูลร้านค้าและใบเสร็จ'),
(3, 'manage_products', 'จัดการแคตตาล็อกสินค้า แบรนด์ และหมวดหมู่'),
(4, 'manage_inventory', 'ปรับปรุงยอดสต็อกสินค้า ตรวจสอบสต็อก และของชำรุด'),
(5, 'manage_purchases', 'จัดการรายการสั่งซื้อสินค้าจากคู่ค้า (PO)'),
(6, 'manage_sales', 'ดำเนินการคิดเงินหน้าจอขายและพักบิล'),
(7, 'view_reports', 'เข้าดูรายงานวิเคราะห์การเงิน ยอดขาย และสต็อกสินค้า');

-- Role Permissions Assignment
-- Owner has all (checked dynamically, but seed for safety)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),
-- Admin has most except user management/settings in some corporate systems, let's grant them access to products, inventory, purchases, sales, reports
(2,3),(2,4),(2,5),(2,6),(2,7),
-- Cashier has sales operations
(3,6),
-- Warehouse has inventory and purchases
(4,4),(4,5);

-- Default Users (Password is 'password' hashed with password_hash bcrypt)
-- Hashed password for 'password' -> $2y$10$cMhoQeL5ThRcGrd.0dlqquiPV7DVeYqRhOyXzFR32mSouEXWmrZUG
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `status`) VALUES
(1, 'สมชาย เจ้าของร้าน', 'owner@bagpos.com', '$2y$10$cMhoQeL5ThRcGrd.0dlqquiPV7DVeYqRhOyXzFR32mSouEXWmrZUG', 1, 'Active'),
(2, 'ผู้ดูแลระบบ', 'admin@bagpos.com', '$2y$10$cMhoQeL5ThRcGrd.0dlqquiPV7DVeYqRhOyXzFR32mSouEXWmrZUG', 2, 'Active'),
(3, 'วรรณา แคชเชียร์', 'cashier@bagpos.com', '$2y$10$cMhoQeL5ThRcGrd.0dlqquiPV7DVeYqRhOyXzFR32mSouEXWmrZUG', 3, 'Active'),
(4, 'มานะ คลังสินค้า', 'warehouse@bagpos.com', '$2y$10$cMhoQeL5ThRcGrd.0dlqquiPV7DVeYqRhOyXzFR32mSouEXWmrZUG', 4, 'Active');

-- Categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'กระเป๋าเป้สะพายหลัง', 'กระเป๋าเป้สะพายสองสายสำหรับนักเรียน ท่องเที่ยว หรือใช้งานทั่วไป'),
(2, 'กระเป๋าถือแฟชั่น', 'กระเป๋าถือสุภาพสตรีดีไซน์สวยงามและทันสมัย'),
(3, 'กระเป๋าเดินทาง', 'กระเป๋าล้อลาก กระเป๋าเดินทางขนาดใหญ่ และกระเป๋ายิม'),
(4, 'กระเป๋าเงินและกระเป๋าคลัทช์', 'กระเป๋าใส่ธนบัตร บัตรเครดิต หรือกระเป๋าถือใบเล็ก'),
(5, 'กระเป๋าสะพายข้างและแมสเซนเจอร์', 'กระเป๋าสะพายพาดลำตัว เหมาะสำหรับวัยทำงานและชีวิตประจำวัน');

-- Brands
INSERT INTO `brands` (`id`, `name`, `description`) VALUES
(1, 'Herschel', 'แบรนด์กระเป๋าแฟชั่นและกระเป๋าเดินทางจากแคนาดา'),
(2, 'Samsonite', 'กระเป๋าเดินทางล้อลากและกระเป๋าทำงานเกรดพรีเมียม'),
(3, 'Anello', 'กระเป๋าเป้แฟชั่นสไตล์ญี่ปุ่น มีช่องเปิดกว้างเป็นเอกลักษณ์'),
(4, 'Tumi', 'กระเป๋าเดินทางและกระเป๋าทำงานแบรนด์หรู มีความทนทานสูง'),
(5, 'Fjallraven', 'แบรนด์กระเป๋าแนวเอาท์ดอร์จากสวีเดน โดดเด่นด้วยรุ่น Kanken');

-- Suppliers
INSERT INTO `suppliers` (`id`, `name`, `contact_name`, `phone`, `email`, `address`) VALUES
(1, 'บริษัท โกลบอล แบ็ก ดิสทริบิวเตอร์ จำกัด', 'คุณสมเกียรติ', '0812345678', 'smith@globalbag.com', '123 ถนนคลังสินค้า เขตคลองเตย กรุงเทพมหานคร'),
(2, 'บริษัท เอเชีย เลเธอร์ ซัพพลายเออร์ จำกัด', 'คุณทานากะ', '0922334455', 'sales@asialeather.com', '45 ถนนอุตสาหกรรม เมืองโตเกียว ประเทศญี่ปุ่น'),
(3, 'ร้านขายส่งกระเป๋าและเครื่องประดับโบ๊เบ๊', 'คุณสมชาย', '021112222', 'somchai@bkkaccessories.com', '789 ถนนเยาวราช เขตสัมพันธวงศ์ กรุงเทพมหานคร');

-- Customers
INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `email`, `birthday`, `gender`, `address`, `reward_points`, `membership_level`) VALUES
(1, 'CUST001', 'ลูกค้าทั่วไป', '0000000000', 'walkin@bagpos.com', NULL, NULL, 'N/A', 0, 'Bronze'),
(2, 'CUST002', 'สมประสงค์ ดีเลิศ', '0898765432', 'somprasong@gmail.com', '1990-05-15', 'Male', '12/3 ถนนสุขุมวิท เขตวัฒนา กรุงเทพมหานคร', 150, 'Silver'),
(3, 'CUST003', 'อภิญญา ศรีสวัสดิ์', '0887776655', 'apinya@hotmail.com', '1995-10-22', 'Female', '456 ถนนรัชดาภิเษก เขตห้วยขวาง กรุงเทพมหานคร', 450, 'Gold'),
(4, 'CUST004', 'สมศักดิ์ รักดี', '0855554433', 'john.doe@yahoo.com', '1985-02-28', 'Male', '78 ถนนสีลม เขตบางรัก กรุงเทพมหานคร', 1200, 'Platinum');

-- Settings
INSERT INTO `settings` (`key_name`, `value_data`) VALUES
('store_name', 'ร้านกระเป๋าพรีเมียมเอาท์เล็ต (Bag Store Premium)'),
('store_email', 'contact@bagstore.com'),
('store_phone', '+66 2 123 4567'),
('store_address', 'ห้อง 302 ชั้น 3 ห้างสรรพสินค้าเซ็นทรัลพลาซา กรุงเทพมหานคร ประเทศไทย'),
('tax_rate', '7.0'),
('currency', 'THB'),
('currency_symbol', '฿'),
('receipt_footer', 'ขอบคุณที่ใช้บริการ!\nเปลี่ยนสินค้าได้ภายใน 7 วันพร้อมใบเสร็จรับเงิน'),
('smtp_host', 'smtp.mailtrap.io'),
('smtp_port', '2525'),
('smtp_user', 'username'),
('smtp_pass', 'password'),
('smtp_encryption', 'tls'),
('timezone', 'Asia/Bangkok');

-- Sample Products
INSERT INTO `products` (`id`, `sku`, `barcode`, `name`, `brand_id`, `category_id`, `color`, `material`, `size`, `cost_price`, `selling_price`, `promotion_price`, `stock_quantity`, `min_stock`, `description`, `status`) VALUES
(1, 'PROD-HER-BP-001', '888000111001', 'กระเป๋าเป้ Herschel Little America', 1, 1, 'ดำ', 'ผ้าใบโพลีเอสเตอร์', '25L', 1500.00, 2990.00, 2790.00, 15, 5, 'กระเป๋าเป้เดินทางสไตล์คลาสสิกปีนเขา เหมาะสำหรับการใช้งานประจำวัน', 'Active'),
(2, 'PROD-SAM-TR-002', '888000111002', 'กระเป๋าเดินทางล้อลาก Samsonite EVOA 55 ซม.', 2, 3, 'เงิน', 'โพลีคาร์บอเนต', '20 นิ้ว', 4500.00, 8900.00, NULL, 8, 2, 'กระเป๋าเดินทางล้อลากโครงแข็งพรีเมียม ล้อระบบกันสะเทือน', 'Active'),
(3, 'PROD-ANE-BP-003', '888000111003', 'กระเป๋าเป้ Anello Classic ขนาดปกติ', 3, 1, 'น้ำเงินกรมท่า', 'ผ้าใบโพลีเอสเตอร์', 'ปกติ', 800.00, 1890.00, 1590.00, 25, 10, 'กระเป๋าเป้ดีไซน์ปากกระเป๋าเปิดกว้างมีโครงเหล็กที่เป็นเอกลักษณ์', 'Active'),
(4, 'PROD-FJA-KN-004', '888000111004', 'กระเป๋าเป้ Fjallraven Kanken คลาสสิก', 5, 1, 'เหลืองอบอุ่น', 'ผ้าวิลอน เอฟ (Vinylon F)', '16L', 1200.00, 2650.00, NULL, 3, 5, 'กระเป๋าเป้สะพายหลังดีไซน์ไอคอนิกจากสวีเดน เปิดตัวครั้งแรกในปี 1978', 'Active'),
(5, 'PROD-TUM-MS-005', '888000111005', 'กระเป๋าสะพายข้าง Tumi Alpha 3 ออร์แกไนเซอร์', 4, 5, 'เทาแอนทราไซต์', 'ผ้าไนลอนบาลิสติก FXT', 'กลาง', 6000.00, 12500.00, NULL, 5, 2, 'กระเป๋าแมสเซนเจอร์ทำงานความทนทานสูง พร้อมช่องเก็บของเป็นสัดส่วน', 'Active'),
(6, 'PROD-ANE-WL-006', '888000111006', 'กระเป๋าเงินใบยาว Anello หนังแท้', 3, 4, 'น้ำตาล', 'หนังพียูพรีเมียม', 'ยาว', 400.00, 990.00, 890.00, 30, 8, 'กระเป๋าเงินซิปรอบใบยาว ดีไซน์เรียบหรูใช้งานสะดวก', 'Active');

-- Stock movements for initial quantities
INSERT INTO `stock_movements` (`product_id`, `type`, `reference_id`, `quantity`, `remaining_stock`, `cost_price`) VALUES
(1, 'Adjustment', NULL, 15, 15, 1500.00),
(2, 'Adjustment', NULL, 8, 8, 4500.00),
(3, 'Adjustment', NULL, 25, 25, 800.00),
(4, 'Adjustment', NULL, 3, 3, 1200.00),
(5, 'Adjustment', NULL, 5, 5, 6000.00),
(6, 'Adjustment', NULL, 30, 30, 400.00);
