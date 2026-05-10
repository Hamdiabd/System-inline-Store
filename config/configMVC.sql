
CREATE DATABASE IF NOT EXISTS supermarket_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE supermarket_db;
-- ============================================================
-- 1. جدول المستخدمين (USER)
-- ============================================================
CREATE TABLE `user` (
    `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('customer', 'admin', 'support', 'inventory') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='جدول المستخدمين الأساسي - كل أنواع المستخدمين';

-- ============================================================
-- 2. جدول العملاء (CUSTOMER)
-- ============================================================
CREATE TABLE `customer` (
    `user_id` INT UNSIGNED NOT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `default_address` TEXT DEFAULT NULL,
    `preferences_json` JSON DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_customer_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بيانات العملاء';

-- ============================================================
-- 3. جدول المشرفين (ADMIN)
-- ============================================================
CREATE TABLE `admin` (
    `user_id` INT UNSIGNED NOT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `permissions` VARCHAR(50) DEFAULT 'limited',
    `salary` DECIMAL(10,2) DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بيانات المشرفين';

-- ============================================================
-- 4. جدول موظفي الدعم (SUPPORT_STAFF)
-- ============================================================
CREATE TABLE `support_staff` (
    `user_id` INT UNSIGNED NOT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `is_online` TINYINT(1) NOT NULL DEFAULT 0,
    `salary` DECIMAL(10,2) DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_support_staff_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بيانات موظفي الدعم';

-- ============================================================
-- 5. جدول مديري المخزون (INVENTORY_MANAGER)
-- ============================================================
CREATE TABLE `inventory_manager` (
    `user_id` INT UNSIGNED NOT NULL,
    `warehouse_id` INT UNSIGNED DEFAULT NULL,
    `salary` DECIMAL(10,2) DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_inventory_manager_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بيانات مسؤولي المخزون';

-- ============================================================
-- 6. جدول العلامات التجارية (BRAND)
-- ============================================================
CREATE TABLE `brand` (
    `brand_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `logo_url` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`brand_id`),
    UNIQUE KEY `uk_brand_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='العلامات التجارية';

-- ============================================================
-- 7. جدول المستودعات (WAREHOUSE)
-- ============================================================
CREATE TABLE `warehouse` (
    `warehouse_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `location` TEXT DEFAULT NULL,
    PRIMARY KEY (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='المستودعات';

-- ============================================================
-- 8. جدول الأقسام (CATEGORY)
-- ============================================================
CREATE TABLE `category` (
    `category_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `parent_category_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`category_id`),
    KEY `idx_parent` (`parent_category_id`),
    CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_category_id`) 
        REFERENCES `category`(`category_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='أقسام المنتجات';

-- ============================================================
-- 9. جدول المنتجات (PRODUCT)
-- ============================================================
CREATE TABLE `product` (
    `product_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `brand_id` INT UNSIGNED DEFAULT NULL,
    `base_image_url` VARCHAR(500) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`product_id`),
    KEY `idx_brand` (`brand_id`),
    KEY `idx_active` (`is_active`),
    CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) 
        REFERENCES `brand`(`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='جدول المنتجات الأساسي';

-- ============================================================
-- 10. جدول سجل تعديلات المنتجات (PRODUCT_AUDIT)
-- ============================================================
CREATE TABLE `product_audit` (
    `audit_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `action` VARCHAR(20) NOT NULL COMMENT 'INSERT, UPDATE, DELETE',
    `old_data_json` JSON DEFAULT NULL,
    `new_data_json` JSON DEFAULT NULL,
    `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`audit_id`),
    KEY `idx_product` (`product_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_changed_at` (`changed_at`),
    CONSTRAINT `fk_audit_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل تعديلات المنتجات';

-- ============================================================
-- 11. جدول متغيرات المنتجات (PRODUCT_VARIANT)
-- ============================================================
CREATE TABLE `product_variant` (
    `variant_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED NOT NULL,
    `SKU` VARCHAR(100) NOT NULL,
    `size_option` VARCHAR(50) DEFAULT NULL,
    `color_option` VARCHAR(50) DEFAULT NULL,
    `packaging` VARCHAR(50) DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `weight_kg` DECIMAL(8,3) DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`variant_id`),
    UNIQUE KEY `uk_sku` (`SKU`),
    KEY `idx_product` (`product_id`),
    CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='متغيرات المنتج - حسب الحجم واللون والتغليف';

-- ============================================================
-- 12. جدول العلاقة: منتج - قسم (PRODUCT_CATEGORY)
-- ============================================================
CREATE TABLE `product_category` (
    `product_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `category_id`),
    CONSTRAINT `fk_pc_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) 
        REFERENCES `category`(`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='علاقة المنتجات بالأقسام - متعدد لمتعدد';

-- ============================================================
-- 13. جدول الموردين (SUPPLIER)
-- ============================================================
CREATE TABLE `supplier` (
    `supplier_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_name` VARCHAR(255) NOT NULL,
    `contact_person` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `rating_avg` DECIMAL(3,2) DEFAULT NULL,
    PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='الموردين';

-- ============================================================
-- 14. جدول العلاقة: منتج - مورد (PRODUCT_SUPPLIER)
-- ============================================================
CREATE TABLE `product_supplier` (
    `product_id` INT UNSIGNED NOT NULL,
    `supplier_id` INT UNSIGNED NOT NULL,
    `supply_price` DECIMAL(10,2) NOT NULL,
    `lead_time_days` INT DEFAULT NULL,
    `minimum_order` INT DEFAULT 1,
    PRIMARY KEY (`product_id`, `supplier_id`),
    CONSTRAINT `fk_ps_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ps_supplier` FOREIGN KEY (`supplier_id`) 
        REFERENCES `supplier`(`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='علاقة المنتجات بالموردين - متعدد لمتعدد مع شروط التوريد';

-- ============================================================
-- 15. جدول علاقة المستودع بالمورد (لإعادة التخزين)
-- سيتم إنشاء INVENTORY لاحقاً، لكن العلاقة هنا
-- ============================================================
-- نضيف المفتاح الأجنبي في inventory_manager بعد إنشاء warehouse
ALTER TABLE `inventory_manager`
    ADD CONSTRAINT `fk_im_warehouse` FOREIGN KEY (`warehouse_id`) 
        REFERENCES `warehouse`(`warehouse_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
-- 16. جدول المخزون (INVENTORY)
-- ============================================================
CREATE TABLE `inventory` (
    `inventory_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `variant_id` INT UNSIGNED NOT NULL,
    `warehouse_id` INT UNSIGNED NOT NULL,
    `quantity_in_stock` INT NOT NULL DEFAULT 0,
    `reorder_level` INT NOT NULL DEFAULT 10,
    `reorder_quantity` INT NOT NULL DEFAULT 50,
    `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`inventory_id`),
    UNIQUE KEY `uk_variant_warehouse` (`variant_id`, `warehouse_id`),
    KEY `idx_warehouse` (`warehouse_id`),
    CONSTRAINT `fk_inventory_variant` FOREIGN KEY (`variant_id`) 
        REFERENCES `product_variant`(`variant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_inventory_warehouse` FOREIGN KEY (`warehouse_id`) 
        REFERENCES `warehouse`(`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='المخزون - يربط المتغيرات بالمستودعات';

-- ============================================================
-- 17. جدول سجل حركات المخزون (INVENTORY_LOG)
-- ============================================================
CREATE TABLE `inventory_log` (
    `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `inventory_id` INT UNSIGNED NOT NULL,
    `change_quantity` INT NOT NULL COMMENT 'موجب للزيادة، سالب للنقصان',
    `change_reason` VARCHAR(50) NOT NULL COMMENT 'sale, restock, return, adjustment',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_inventory` (`inventory_id`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_log_inventory` FOREIGN KEY (`inventory_id`) 
        REFERENCES `inventory`(`inventory_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل حركات المخزون';

-- ============================================================
-- 18. جدول شركات الشحن (CARRIER)
-- ============================================================
CREATE TABLE `carrier` (
    `carrier_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `api_endpoint` VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='شركات الشحن الخارجي';

-- ============================================================
-- 19. جدول الطلبات (ORDER_HEADER)
-- ============================================================
CREATE TABLE `order_header` (
    `order_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `order_status` VARCHAR(30) NOT NULL DEFAULT 'pending',
    `shipping_address` TEXT DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    PRIMARY KEY (`order_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_status` (`order_status`),
    KEY `idx_order_date` (`order_date`),
    CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='جدول الطلبات';

-- ============================================================
-- 20. جدول بنود الطلب (ORDER_ITEM)
-- ============================================================
CREATE TABLE `order_item` (
    `order_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `variant_id` INT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`order_item_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_oi_variant` FOREIGN KEY (`variant_id`) 
        REFERENCES `product_variant`(`variant_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بنود الطلب';

-- ============================================================
-- 21. جدول سجل حالات الطلب (ORDER_STATUS_HISTORY)
-- ============================================================
CREATE TABLE `order_status_history` (
    `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `status` VARCHAR(30) NOT NULL,
    `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `changed_by` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`history_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل تغييرات حالة الطلب';

-- ============================================================
-- 22. جدول طرق الدفع المحفوظة (PAYMENT_METHOD)
-- ============================================================
CREATE TABLE `payment_method` (
    `method_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `payment_type` VARCHAR(30) NOT NULL COMMENT 'card, wallet, cod',
    `details_encrypted` TEXT DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`method_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_pm_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='طرق الدفع المحفوظة للعملاء';

-- ============================================================
-- 23. جدول المدفوعات (PAYMENT)
-- ============================================================
CREATE TABLE `payment` (
    `payment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `payment_method_id` INT UNSIGNED DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_status` VARCHAR(30) NOT NULL DEFAULT 'pending' COMMENT 'pending, success, failed, refunded',
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`payment_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_payment_method` FOREIGN KEY (`payment_method_id`) 
        REFERENCES `payment_method`(`method_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='عمليات الدفع';

-- ============================================================
-- 24. جدول التحقق الأمني للدفع (PAYMENT_VERIFICATION)
-- ============================================================
CREATE TABLE `payment_verification` (
    `verification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payment_id` INT UNSIGNED NOT NULL,
    `otp_hash` VARCHAR(255) DEFAULT NULL,
    `verified_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`verification_id`),
    CONSTRAINT `fk_pv_payment` FOREIGN KEY (`payment_id`) 
        REFERENCES `payment`(`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='التحقق الأمني لعمليات الدفع';

-- ============================================================
-- 25. جدول الشحنات (SHIPMENT)
-- ============================================================
CREATE TABLE `shipment` (
    `shipment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `carrier_id` INT UNSIGNED DEFAULT NULL,
    `tracking_number` VARCHAR(255) DEFAULT NULL,
    `shipment_status` VARCHAR(30) NOT NULL DEFAULT 'packed' COMMENT 'packed, in_transit, delivered',
    `estimated_delivery` DATETIME DEFAULT NULL,
    PRIMARY KEY (`shipment_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_shipment_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_shipment_carrier` FOREIGN KEY (`carrier_id`) 
        REFERENCES `carrier`(`carrier_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='الشحنات عبر شركات خارجية';

-- ============================================================
-- 26. جدول تتبع الشحنات (SHIPMENT_TRACKING)
-- ============================================================
CREATE TABLE `shipment_tracking` (
    `tracking_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `shipment_id` INT UNSIGNED NOT NULL,
    `tracking_status` VARCHAR(255) DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT DEFAULT NULL,
    PRIMARY KEY (`tracking_id`),
    KEY `idx_shipment` (`shipment_id`),
    CONSTRAINT `fk_st_shipment` FOREIGN KEY (`shipment_id`) 
        REFERENCES `shipment`(`shipment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='تتبع الشحنات الخارجية';

-- ============================================================
-- 27. جدول مناديب التوصيل (DELIVERY_DRIVER)
-- ============================================================
CREATE TABLE `delivery_driver` (
    `driver_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `driver_name` VARCHAR(255) NOT NULL,
    `vehicle_number` VARCHAR(50) DEFAULT NULL,
    `available` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='مناديب التوصيل';

-- ============================================================
-- 28. جدول التوصيل المباشر (ORDER_DELIVERY)
-- ============================================================
CREATE TABLE `order_delivery` (
    `delivery_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `driver_id` INT UNSIGNED DEFAULT NULL,
    `delivery_status` VARCHAR(30) NOT NULL DEFAULT 'assigned' COMMENT 'assigned, picked, in_transit, delivered',
    `current_lat` DECIMAL(10,8) DEFAULT NULL,
    `current_long` DECIMAL(11,8) DEFAULT NULL,
    `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`delivery_id`),
    KEY `idx_order` (`order_id`),
    KEY `idx_driver` (`driver_id`),
    CONSTRAINT `fk_od_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_od_driver` FOREIGN KEY (`driver_id`) 
        REFERENCES `delivery_driver`(`driver_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='عمليات التوصيل عبر مناديب الأسطول';

-- ============================================================
-- 29. جدول سجل حالات التوصيل (DELIVERY_STATUS_LOG)
-- ============================================================
CREATE TABLE `delivery_status_log` (
    `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `delivery_id` INT UNSIGNED NOT NULL,
    `driver_id` INT UNSIGNED DEFAULT NULL,
    `delivery_status` VARCHAR(30) NOT NULL,
    `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT DEFAULT NULL,
    PRIMARY KEY (`log_id`),
    KEY `idx_delivery` (`delivery_id`),
    KEY `idx_driver` (`driver_id`),
    CONSTRAINT `fk_dsl_delivery` FOREIGN KEY (`delivery_id`) 
        REFERENCES `order_delivery`(`delivery_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dsl_driver` FOREIGN KEY (`driver_id`) 
        REFERENCES `delivery_driver`(`driver_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل تغيير حالات التوصيل';

-- ============================================================
-- 30. جدول تتبع الموقع الحي للتوصيل (DELIVERY_TRACKING)
-- ============================================================
CREATE TABLE `delivery_tracking` (
    `tracking_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `delivery_id` INT UNSIGNED NOT NULL,
    `latitude` DECIMAL(10,8) DEFAULT NULL,
    `longitude` DECIMAL(11,8) DEFAULT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tracking_id`),
    KEY `idx_delivery` (`delivery_id`),
    KEY `idx_timestamp` (`timestamp`),
    CONSTRAINT `fk_dt_delivery` FOREIGN KEY (`delivery_id`) 
        REFERENCES `order_delivery`(`delivery_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='نقاط التتبع الجغرافي الحي';

-- ============================================================
-- 31. جدول المرتجعات (RETURN_REQUEST)
-- ============================================================
CREATE TABLE `return_request` (
    `return_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `return_status` VARCHAR(30) NOT NULL DEFAULT 'requested' COMMENT 'requested, approved, rejected, completed',
    `reason_text` TEXT DEFAULT NULL,
    `refund_amount` DECIMAL(10,2) DEFAULT NULL,
    `request_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`return_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_return_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='طلبات الإرجاع';

-- ============================================================
-- 32. جدول بنود المرتجعات (RETURN_ITEM)
-- ============================================================
CREATE TABLE `return_item` (
    `return_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `return_id` INT UNSIGNED NOT NULL,
    `order_item_id` INT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL,
    PRIMARY KEY (`return_item_id`),
    KEY `idx_return` (`return_id`),
    CONSTRAINT `fk_ri_return` FOREIGN KEY (`return_id`) 
        REFERENCES `return_request`(`return_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ri_order_item` FOREIGN KEY (`order_item_id`) 
        REFERENCES `order_item`(`order_item_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='بنود الإرجاع';

-- ============================================================
-- 33. جدول العروض الترويجية (PROMOTION)
-- ============================================================
CREATE TABLE `promotion` (
    `promotion_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `promo_name` VARCHAR(255) NOT NULL,
    `promo_description` TEXT DEFAULT NULL,
    `discount_type` VARCHAR(20) NOT NULL COMMENT 'percentage, fixed',
    `discount_value` DECIMAL(10,2) NOT NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `conditions_json` JSON DEFAULT NULL,
    PRIMARY KEY (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='العروض الترويجية';

-- ============================================================
-- 34. جدول أهداف العروض (PROMOTION_TARGET)
-- ============================================================
CREATE TABLE `promotion_target` (
    `target_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `promotion_id` INT UNSIGNED NOT NULL,
    `target_type` VARCHAR(20) NOT NULL COMMENT 'product, category, brand',
    `target_entity_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`target_id`),
    KEY `idx_promotion` (`promotion_id`),
    CONSTRAINT `fk_pt_promotion` FOREIGN KEY (`promotion_id`) 
        REFERENCES `promotion`(`promotion_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='ربط العروض بالمنتجات أو الأقسام أو العلامات';

-- ============================================================
-- 35. جدول الكوبونات (COUPON)
-- ============================================================
CREATE TABLE `coupon` (
    `coupon_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `discount_type` VARCHAR(20) NOT NULL COMMENT 'percentage, fixed',
    `value` DECIMAL(10,2) NOT NULL,
    `max_uses` INT NOT NULL DEFAULT 100,
    `current_uses` INT NOT NULL DEFAULT 0,
    `valid_from` DATETIME NOT NULL,
    `valid_to` DATETIME NOT NULL,
    PRIMARY KEY (`coupon_id`),
    UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='الكوبونات';

-- ============================================================
-- 36. جدول استخدام الكوبونات (COUPON_USAGE)
-- ============================================================
CREATE TABLE `coupon_usage` (
    `usage_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `coupon_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `order_id` INT UNSIGNED NOT NULL,
    `used_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`usage_id`),
    KEY `idx_coupon` (`coupon_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_order` (`order_id`),
    CONSTRAINT `fk_cu_coupon` FOREIGN KEY (`coupon_id`) 
        REFERENCES `coupon`(`coupon_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_cu_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_cu_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل استخدام الكوبونات';

-- ============================================================
-- 37. جدول برامج الولاء (LOYALTY_PROGRAM)
-- ============================================================
CREATE TABLE `loyalty_program` (
    `program_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_name` VARCHAR(255) NOT NULL,
    `points_per_currency` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    `rules_json` JSON DEFAULT NULL,
    PRIMARY KEY (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='برامج الولاء';

-- ============================================================
-- 38. جدول اشتراكات العملاء في الولاء (CUSTOMER_LOYALTY)
-- ============================================================
CREATE TABLE `customer_loyalty` (
    `customer_id` INT UNSIGNED NOT NULL,
    `program_id` INT UNSIGNED NOT NULL,
    `points_balance` INT NOT NULL DEFAULT 0,
    `tier` VARCHAR(20) NOT NULL DEFAULT 'silver' COMMENT 'silver, gold, platinum',
    PRIMARY KEY (`customer_id`, `program_id`),
    CONSTRAINT `fk_cl_customer` FOREIGN KEY (`customer_id`) 
        REFERENCES `customer`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_cl_program` FOREIGN KEY (`program_id`) 
        REFERENCES `loyalty_program`(`program_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='اشتراكات العملاء في برامج الولاء';

-- ============================================================
-- 39. جدول استبدال نقاط الولاء (LOYALTY_REDEMPTION)
-- ============================================================
CREATE TABLE `loyalty_redemption` (
    `redemption_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED NOT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `points_used` INT NOT NULL,
    `reward_description` VARCHAR(255) DEFAULT NULL,
    `redeemed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`redemption_id`),
    KEY `idx_customer` (`customer_id`),
    CONSTRAINT `fk_lr_customer` FOREIGN KEY (`customer_id`) 
        REFERENCES `customer`(`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_lr_order` FOREIGN KEY (`order_id`) 
        REFERENCES `order_header`(`order_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='عمليات استبدال نقاط الولاء';

-- ============================================================
-- 40. جدول تقييمات المنتجات (REVIEW)
-- ============================================================
CREATE TABLE `review` (
    `review_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL COMMENT 'من 1 إلى 5',
    `comment_text` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`review_id`),
    UNIQUE KEY `uk_user_product` (`user_id`, `product_id`),
    KEY `idx_product` (`product_id`),
    CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='تقييمات العملاء للمنتجات';

-- ============================================================
-- 41. جدول تقييمات الموردين (REVIEW_SUPPLIER)
-- ============================================================
CREATE TABLE `review_supplier` (
    `review_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `supplier_id` INT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL COMMENT 'من 1 إلى 5',
    `comment_text` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`review_id`),
    UNIQUE KEY `uk_user_supplier` (`user_id`, `supplier_id`),
    KEY `idx_supplier` (`supplier_id`),
    CONSTRAINT `fk_rs_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rs_supplier` FOREIGN KEY (`supplier_id`) 
        REFERENCES `supplier`(`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='تقييمات الموردين من قبل الموظفين';

-- ============================================================
-- 42. جدول سلوك المستخدم (USER_BEHAVIOR)
-- ============================================================
CREATE TABLE `user_behavior` (
    `behavior_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `action_type` VARCHAR(30) NOT NULL COMMENT 'view, cart, order, search',
    `event_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `session_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`behavior_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_product` (`product_id`),
    KEY `idx_event` (`event_time`),
    CONSTRAINT `fk_ub_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ub_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سجل سلوك المستخدمين - لتغذية نظام التوصيات';

-- ============================================================
-- 43. جدول تقارب المنتجات للتوصيات (PRODUCT_AFFINITY)
-- ============================================================
CREATE TABLE `product_affinity` (
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `affinity_score` FLOAT NOT NULL DEFAULT 0,
    `last_calculated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `product_id`),
    CONSTRAINT `fk_pa_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_pa_product` FOREIGN KEY (`product_id`) 
        REFERENCES `product`(`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='نتائج تقارب المنتجات للتوصيات';

-- ============================================================
-- 44. جدول جلسات الدردشة (CHAT_SESSION)
-- ============================================================
CREATE TABLE `chat_session` (
    `session_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `support_staff_id` INT UNSIGNED DEFAULT NULL,
    `session_status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'active, closed',
    `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ended_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`session_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_staff` (`support_staff_id`),
    CONSTRAINT `fk_cs_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_cs_staff` FOREIGN KEY (`support_staff_id`) 
        REFERENCES `support_staff`(`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='جلسات الدردشة المباشرة';

-- ============================================================
-- 45. جدول رسائل الدردشة (CHAT_MESSAGE)
-- ============================================================
CREATE TABLE `chat_message` (
    `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_id` INT UNSIGNED NOT NULL,
    `sender_type` VARCHAR(10) NOT NULL COMMENT 'user, bot, agent',
    `message_text` TEXT NOT NULL,
    `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`),
    KEY `idx_session` (`session_id`),
    CONSTRAINT `fk_cm_session` FOREIGN KEY (`session_id`) 
        REFERENCES `chat_session`(`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='رسائل الدردشة';

-- ============================================================
-- 46. جدول سلة التسوق (CART)
-- ============================================================
CREATE TABLE `cart` (
    `cart_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cart_id`),
    UNIQUE KEY `uk_user_cart` (`user_id`),
    CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='سلة التسوق - سلة واحدة لكل عميل';

-- ============================================================
-- 47. جدول بنود سلة التسوق (CART_ITEM)
-- ============================================================
CREATE TABLE `cart_item` (
    `cart_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_id` INT UNSIGNED NOT NULL,
    `variant_id` INT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    PRIMARY KEY (`cart_item_id`),
    UNIQUE KEY `uk_cart_variant` (`cart_id`, `variant_id`),
    CONSTRAINT `fk_ci_cart` FOREIGN KEY (`cart_id`) 
        REFERENCES `cart`(`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ci_variant` FOREIGN KEY (`variant_id`) 
        REFERENCES `product_variant`(`variant_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='عناصر سلة التسوق';

-- ============================================================
-- تم بنجاح!
-- ============================================================


-- ============================================================
-- 1. جدول المستخدمين (USER)
-- ============================================================
-- كلمات المرور افتراضية مشفرة (يفترض استخدام password_hash في الواقع)
INSERT INTO `user` (`user_id`, `email`, `password_hash`, `full_name`, `phone`, `role`) VALUES
(1, 'fatima@supermarket.com', '$2y$10$hash12345678901234567890', 'فاطمة العمري', '0501111111', 'admin'),
(2, 'ahmed@supermarket.com', '$2y$10$hash12345678901234567891', 'أحمد السيد', '0502222222', 'admin'),
(3, 'sara@supermarket.com', '$2y$10$hash12345678901234567892', 'سارة القحطاني', '0503333333', 'inventory'),
(4, 'khaled@supermarket.com', '$2y$10$hash12345678901234567893', 'خالد المطيري', '0504444444', 'inventory'),
(5, 'lamia@supermarket.com', '$2y$10$hash12345678901234567894', 'لمياء الحربي', '0505555555', 'support'),
(6, 'nasser@supermarket.com', '$2y$10$hash12345678901234567895', 'ناصر الشمري', '0506666666', 'support'),
(7, 'ali@email.com', '$2y$10$hash12345678901234567896', 'علي محمد', '0507777777', 'customer'),
(8, 'noura@email.com', '$2y$10$hash12345678901234567897', 'نورة العتيبي', '0508888888', 'customer'),
(9, 'salem@email.com', '$2y$10$hash12345678901234567898', 'سالم الجهني', '0509999999', 'customer'),
(10, 'mona@email.com', '$2y$10$hash12345678901234567899', 'منى عبدالله', '0500000000', 'customer');

-- ============================================================
-- 2. جدول العملاء (CUSTOMER)
-- ============================================================
INSERT INTO `customer` (`user_id`, `date_of_birth`, `default_address`, `preferences_json`) VALUES
(7, '1990-03-15', 'الرياض - حي النرجس - شارع التخصصي - مبنى 12', '{"language": "ar", "diet": "none", "notifications": true}'),
(8, '1985-08-22', 'جدة - حي الروضة - شارع صاري - شقة 5', '{"language": "ar", "diet": "vegetarian", "notifications": true}'),
(9, '1995-12-10', 'الدمام - حي الفيصلية - شارع الملك فهد', '{"language": "ar", "diet": "none", "notifications": false}'),
(10, '2000-06-01', 'الرياض - حي الملك عبدالله - شارع العليا - عمارة 3', '{"language": "ar", "diet": "halal", "notifications": true}');

-- ============================================================
-- 3. جدول المشرفين (ADMIN)
-- ============================================================
INSERT INTO `admin` (`user_id`, `department`, `permissions`, `salary`) VALUES
(1, 'الإدارة العليا', 'full', 15000.00),
(2, 'قسم المنتجات', 'limited', 9000.00);

-- ============================================================
-- 4. جدول موظفي الدعم (SUPPORT_STAFF)
-- ============================================================
INSERT INTO `support_staff` (`user_id`, `department`, `is_online`, `salary`) VALUES
(5, 'الدعم الفني', 1, 6500.00),
(6, 'الدعم الفني', 0, 6500.00);

-- ============================================================
-- 7. جدول المستودعات (WAREHOUSE)
-- ============================================================


INSERT INTO `warehouse` (`warehouse_id`, `name`, `location`) VALUES
(1, 'مستودع الرياض المركزي', 'الرياض - المنطقة الصناعية - طريق الخرج'),
(2, 'مستودع جدة', 'جدة - المنطقة الصناعية - طريق مكة'),
(3, 'مستودع الدمام', 'الدمام - المنطقة الصناعية - طريق الدمام-الجبيل');
-- ============================================================
-- 5. جدول مسؤولي المخزون (INVENTORY_MANAGER)
-- ============================================================
INSERT INTO `inventory_manager` (`user_id`, `warehouse_id`, `salary`) VALUES
(3, 1, 7500.00),
(4, 1, 7500.00);


-- ============================================================
-- 6. جدول العلامات التجارية (BRAND)
-- ============================================================
INSERT INTO `brand` (`name`, `logo_url`) VALUES
( ' المراعي', 'https://example.com/logos/almarai.png'),
( 'ندى', 'https://example.com/logos/nada.png'),
( 'الراجحي', 'https://example.com/logos/alrajhi.png'),
( 'بيبسي', 'https://example.com/logos/pepsi.png'),
( 'الريف', 'https://example.com/logos/alreef.png');



-- ============================================================
-- 8. جدول الأقسام (CATEGORY)
-- ============================================================
INSERT INTO `category` (`category_id`, `name`, `parent_category_id`) VALUES
(1, 'الألبان والأجبان', NULL),
(2, 'المشروبات', NULL),
(3, 'المخبوزات', NULL),
(4, 'حليب', 1),
(5, 'أجبان', 1),
(6, 'عصائر', 2),
(7, 'مشروبات غازية', 2),
(8, 'خبز', 3),
(9, 'حليب طويل الأجل', 4),
(10, 'حليب طازج', 4);

-- ============================================================
-- 9. جدول المنتجات (PRODUCT)
-- ============================================================
INSERT INTO `product` (`product_id`, `name`, `description`, `brand_id`, `base_image_url`, `is_active`) VALUES
(1, 'حليب كامل الدسم', 'حليب طازج كامل الدسم من المراعي', 1, 'https://example.com/images/almarai-full-milk.jpg', 1),
(2, 'حليب قليل الدسم', 'حليب طازج قليل الدسم من ندى', 2, 'https://example.com/images/nada-lowfat-milk.jpg', 1),
(3, 'جبنة شيدر', 'جبنة شيدر طبيعية من المراعي', 1, 'https://example.com/images/almarai-cheddar.jpg', 1),
(4, 'جبنة موزاريلا', 'جبنة موزاريلا للبيتزا من الراجحي', 3, 'https://example.com/images/alrajhi-mozzarella.jpg', 1),
(5, 'عصير برتقال', 'عصير برتقال طبيعي من المراعي', 1, 'https://example.com/images/almarai-orange-juice.jpg', 1),
(6, 'بيبسي كولا', 'مشروب غازي بيبسي كولا', 4, 'https://example.com/images/pepsi-cola.jpg', 1),
(7, 'خبز توست أبيض', 'خبز توست أبيض طري من الريف', 5, 'https://example.com/images/alreef-white-toast.jpg', 1),
(8, 'خبز توست بر', 'خبز توست بر صحي من الريف', 5, 'https://example.com/images/alreef-brown-toast.jpg', 1);

-- ============================================================
-- 10. جدول سجل تعديلات المنتجات (PRODUCT_AUDIT)
-- ============================================================
INSERT INTO `product_audit` (`audit_id`, `product_id`, `user_id`, `action`, `old_data_json`, `new_data_json`, `changed_at`) VALUES
(1, 1, 1, 'INSERT', NULL, '{"name": "حليب كامل الدسم", "brand_id": 1, "is_active": true}', '2026-04-01 08:00:00'),
(2, 6, 2, 'UPDATE', '{"price": 3.50}', '{"price": 3.00}', '2026-04-15 10:30:00'),
(3, 7, 2, 'UPDATE', '{"is_active": false}', '{"is_active": true}', '2026-04-20 14:00:00');

-- ============================================================
-- 11. جدول متغيرات المنتج (PRODUCT_VARIANT)
-- ============================================================
INSERT INTO `product_variant` (`variant_id`, `product_id`, `SKU`, `size_option`, `packaging`, `price`, `weight_kg`) VALUES
(1, 1, 'ALM-FULL-1L', '1 لتر', 'كرتون', 7.50, 1.050),
(2, 1, 'ALM-FULL-2L', '2 لتر', 'بلاستيك', 13.00, 2.100),
(3, 2, 'NDA-LOW-1L', '1 لتر', 'كرتون', 7.00, 1.050),
(4, 2, 'NDA-LOW-2L', '2 لتر', 'بلاستيك', 12.50, 2.100),
(5, 3, 'ALM-CHED-200G', '200 جرام', 'غلاف بلاستيكي', 15.00, 0.220),
(6, 3, 'ALM-CHED-500G', '500 جرام', 'غلاف بلاستيكي', 28.00, 0.520),
(7, 4, 'ALR-MOZ-200G', '200 جرام', 'غلاف بلاستيكي', 12.00, 0.220),
(8, 4, 'ALR-MOZ-500G', '500 جرام', 'غلاف بلاستيكي', 22.00, 0.520),
(9, 5, 'ALM-ORJ-330ML', '330 مل', 'علبة', 3.00, 0.350),
(10, 5, 'ALM-ORJ-1L', '1 لتر', 'كرتون', 7.00, 1.050),
(11, 6, 'PEP-COLA-330ML', '330 مل', 'علبة', 3.00, 0.350),
(12, 6, 'PEP-COLA-2L', '2 لتر', 'بلاستيك', 8.00, 2.100),
(13, 7, 'ALR-WTOAST-1PCK', 'رغيف واحد', 'كيس', 4.00, 0.400),
(14, 8, 'ALR-BTOAST-1PCK', 'رغيف واحد', 'كيس', 5.00, 0.400);

-- ============================================================
-- 12. جدول العلاقة: منتج - قسم (PRODUCT_CATEGORY)
-- ============================================================
INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(1, 4), (1, 10),   -- حليب كامل الدسم: حليب + حليب طازج
(2, 4), (2, 10),   -- حليب قليل الدسم: حليب + حليب طازج
(3, 5),             -- جبنة شيدر: أجبان
(4, 5),             -- جبنة موزاريلا: أجبان
(5, 6),             -- عصير برتقال: عصائر
(6, 7),             -- بيبسي كولا: مشروبات غازية
(7, 8),             -- خبز توست أبيض: خبز
(8, 8);             -- خبز توست بر: خبز

-- ============================================================
-- 13. جدول الموردين (SUPPLIER)
-- ============================================================
INSERT INTO `supplier` (`supplier_id`, `company_name`, `contact_person`, `email`, `phone`, `rating_avg`) VALUES
(1, 'شركة المراعي', 'محمد العتيبي', 'sales@almarai.sa', '0111111111', 4.80),
(2, 'شركة ندى', 'عبدالله الحربي', 'sales@nada.sa', '0112222222', 4.50),
(3, 'مصنع الراجحي للأجبان', 'فهد الراجحي', 'sales@alrajhi-cheese.sa', '0113333333', 4.20),
(4, 'شركة بيبسيكو السعودية', 'سامر القاسم', 'sales@pepsico.sa', '0114444444', 4.90),
(5, 'مخابز الريف', 'خالد السبيعي', 'sales@alreef-bakery.sa', '0115555555', 4.60);

-- ============================================================
-- 14. جدول العلاقة: منتج - مورد (PRODUCT_SUPPLIER)
-- ============================================================
INSERT INTO `product_supplier` (`product_id`, `supplier_id`, `supply_price`, `lead_time_days`, `minimum_order`) VALUES
(1, 1, 5.50, 2, 100),    -- حليب كامل الدسم من المراعي
(1, 2, 5.80, 3, 80),     -- حليب كامل الدسم من ندى
(2, 2, 5.00, 3, 80),     -- حليب قليل الدسم من ندى
(3, 1, 11.00, 3, 50),    -- جبنة شيدر من المراعي
(4, 3, 8.50, 4, 30),     -- جبنة موزاريلا من الراجحي
(5, 1, 2.00, 2, 200),    -- عصير برتقال من المراعي
(6, 4, 2.20, 1, 500),    -- بيبسي من بيبسيكو
(7, 5, 2.50, 1, 150),    -- خبز توست أبيض من الريف
(8, 5, 3.00, 1, 100);    -- خبز توست بر من الريف

-- ============================================================
-- 16. جدول المخزون (INVENTORY)
-- ============================================================
INSERT INTO `inventory` (`inventory_id`, `variant_id`, `warehouse_id`, `quantity_in_stock`, `reorder_level`, `reorder_quantity`) VALUES
(1, 1, 1, 500, 50, 200),   -- حليب كامل الدسم 1لتر في الرياض
(2, 1, 2, 150, 30, 100),   -- حليب كامل الدسم 1لتر في جدة
(3, 2, 1, 300, 40, 150),   -- حليب كامل الدسم 2لتر في الرياض
(4, 3, 1, 120, 25, 100),   -- حليب قليل الدسم 1لتر في الرياض
(5, 5, 1, 80, 20, 60),     -- جبنة شيدر 200ج في الرياض
(6, 6, 1, 45, 15, 50),     -- جبنة شيدر 500ج في الرياض
(7, 7, 1, 90, 20, 60),     -- جبنة موزاريلا 200ج في الرياض
(8, 9, 1, 600, 100, 300),  -- عصير برتقال 330مل في الرياض
(9, 10, 1, 250, 50, 150),  -- عصير برتقال 1لتر في الرياض
(10, 11, 1, 1000, 200, 500), -- بيبسي 330مل في الرياض
(11, 11, 2, 400, 80, 200), -- بيبسي 330مل في جدة
(12, 12, 1, 350, 70, 200), -- بيبسي 2لتر في الرياض
(13, 13, 1, 200, 40, 100), -- خبز توست أبيض في الرياض
(14, 14, 1, 120, 30, 80);  -- خبز توست بر في الرياض

-- ============================================================
-- 17. جدول سجل حركات المخزون (INVENTORY_LOG)
-- ============================================================
INSERT INTO `inventory_log` (`log_id`, `inventory_id`, `change_quantity`, `change_reason`) VALUES
(1, 1, 500, 'restock'),
(2, 3, 300, 'restock'),
(3, 8, -50, 'sale'),
(4, 10, -100, 'sale'),
(5, 13, -20, 'sale'),
(6, 5, 20, 'return'),
(7, 10, -200, 'sale'),
(8, 1, 200, 'restock');

-- ============================================================
-- 18. جدول شركات الشحن (CARRIER)
-- ============================================================
INSERT INTO `carrier` (`carrier_id`, `name`, `api_endpoint`) VALUES
(1, 'أرامكس', 'https://api.aramex.com/tracking'),
(2, 'دي إتش إل', 'https://api.dhl.com/tracking'),
(3, 'فيديكس', 'https://api.fedex.com/tracking'),
(4, 'ناقل', 'https://api.naqel.com.sa/tracking');

-- ============================================================
-- 19. جدول الطلبات (ORDER_HEADER)
-- ============================================================
INSERT INTO `order_header` (`order_id`, `user_id`, `order_date`, `total_amount`, `order_status`, `shipping_address`) VALUES
(1, 7, '2026-04-10 09:15:00', 51.50, 'delivered', 'الرياض - حي النرجس - شارع التخصصي - مبنى 12'),
(2, 8, '2026-04-12 14:30:00', 23.00, 'delivered', 'جدة - حي الروضة - شارع صاري - شقة 5'),
(3, 9, '2026-04-18 11:00:00', 35.00, 'shipped', 'الدمام - حي الفيصلية - شارع الملك فهد'),
(4, 10, '2026-04-25 20:45:00', 18.00, 'pending', 'الرياض - حي الملك عبدالله - شارع العليا - عمارة 3'),
(5, 7, '2026-04-30 16:10:00', 22.00, 'confirmed', 'الرياض - حي النرجس - شارع التخصصي - مبنى 12');

-- ============================================================
-- 20. جدول بنود الطلب (ORDER_ITEM)
-- ============================================================
INSERT INTO `order_item` (`order_item_id`, `order_id`, `variant_id`, `quantity`, `unit_price`) VALUES
-- الطلب 1: علي اشترى حليب كامل الدسم 2لتر + جبنة شيدر 200ج + بيبسي 330مل
(1, 1, 2, 2, 13.00),
(2, 1, 5, 1, 15.00),
(3, 1, 11, 3, 3.00),
-- الطلب 2: نورة اشترت عصير برتقال 1لتر + خبز توست أبيض
(4, 2, 10, 2, 7.00),
(5, 2, 13, 2, 4.00),
-- الطلب 3: سالم اشترى جبنة موزاريلا 500ج + بيبسي 2لتر + خبز توست بر
(6, 3, 8, 1, 22.00),
(7, 3, 12, 1, 8.00),
(8, 3, 14, 1, 5.00),
-- الطلب 4: منى اشترت حليب قليل الدسم 1لتر + عصير برتقال 330مل ×3
(9, 4, 3, 1, 7.00),
(10, 4, 9, 3, 3.00),
-- الطلب 5: علي اشترى جبنة شيدر 500ج + حليب كامل الدسم 1لتر
(11, 5, 6, 1, 28.00),
(12, 5, 1, 2, 7.50);

-- ============================================================
-- 21. جدول سجل حالات الطلب (ORDER_STATUS_HISTORY)
-- ============================================================
INSERT INTO `order_status_history` (`history_id`, `order_id`, `status`, `changed_at`, `changed_by`) VALUES
(1, 1, 'pending', '2026-04-10 09:15:00', 'علي محمد'),
(2, 1, 'confirmed', '2026-04-10 09:20:00', 'فاطمة العمري'),
(3, 1, 'shipped', '2026-04-10 10:00:00', 'فاطمة العمري'),
(4, 1, 'delivered', '2026-04-10 12:30:00', 'سامر (مندوب)'),
(5, 2, 'pending', '2026-04-12 14:30:00', 'نورة العتيبي'),
(6, 2, 'confirmed', '2026-04-12 14:35:00', 'أحمد السيد'),
(7, 2, 'delivered', '2026-04-12 18:00:00', 'باسل (مندوب)'),
(8, 3, 'pending', '2026-04-18 11:00:00', 'سالم الجهني'),
(9, 3, 'confirmed', '2026-04-18 11:10:00', 'فاطمة العمري'),
(10, 3, 'shipped', '2026-04-18 13:00:00', 'فاطمة العمري'),
(11, 4, 'pending', '2026-04-25 20:45:00', 'منى عبدالله'),
(12, 5, 'pending', '2026-04-30 16:10:00', 'علي محمد'),
(13, 5, 'confirmed', '2026-04-30 16:15:00', 'أحمد السيد');

-- ============================================================
-- 22. جدول طرق الدفع المحفوظة (PAYMENT_METHOD)
-- ============================================================
INSERT INTO `payment_method` (`method_id`, `user_id`, `payment_type`, `details_encrypted`, `is_default`) VALUES
(1, 7, 'card', 'encrypted_card_data_ali_visa', 1),
(2, 7, 'cod', NULL, 0),
(3, 8, 'wallet', 'encrypted_wallet_data_noura', 1),
(4, 9, 'card', 'encrypted_card_data_salem_mastercard', 1),
(5, 10, 'cod', NULL, 1);

-- ============================================================
-- 23. جدول المدفوعات (PAYMENT)
-- ============================================================
INSERT INTO `payment` (`payment_id`, `order_id`, `payment_method_id`, `amount`, `payment_status`, `transaction_id`) VALUES
(1, 1, 1, 51.50, 'success', 'TXN-20260410-001'),
(2, 2, 3, 23.00, 'success', 'TXN-20260412-002'),
(3, 3, 4, 35.00, 'success', 'TXN-20260418-003'),
(4, 4, 5, 18.00, 'pending', NULL),
(5, 5, 1, 22.00, 'success', 'TXN-20260430-004');

-- ============================================================
-- 24. جدول التحقق الأمني للدفع (PAYMENT_VERIFICATION)
-- ============================================================
INSERT INTO `payment_verification` (`verification_id`, `payment_id`, `otp_hash`, `verified_at`) VALUES
(1, 1, '$2y$10$otp_hash_001', '2026-04-10 09:16:00'),
(2, 2, '$2y$10$otp_hash_002', '2026-04-12 14:31:00'),
(3, 3, '$2y$10$otp_hash_003', '2026-04-18 11:01:00'),
(4, 5, '$2y$10$otp_hash_005', '2026-04-30 16:11:00');

-- ============================================================
-- 25. جدول الشحنات (SHIPMENT)
-- ============================================================
INSERT INTO `shipment` (`shipment_id`, `order_id`, `carrier_id`, `tracking_number`, `shipment_status`, `estimated_delivery`) VALUES
(1, 1, 1, 'ARAMEX-20260410-001', 'delivered', '2026-04-10 14:00:00'),
(2, 2, 4, 'NAQEL-20260412-002', 'delivered', '2026-04-12 20:00:00'),
(3, 3, 1, 'ARAMEX-20260418-003', 'in_transit', '2026-04-20 12:00:00'),
(4, 5, 4, 'NAQEL-20260430-005', 'packed', '2026-05-01 18:00:00');

-- ============================================================
-- 26. جدول تتبع الشحنات (SHIPMENT_TRACKING)
-- ============================================================
INSERT INTO `shipment_tracking` (`tracking_id`, `shipment_id`, `tracking_status`, `location`, `timestamp`) VALUES
(1, 1, 'تم استلام الشحنة', 'الرياض - مركز التوزيع', '2026-04-10 10:00:00'),
(2, 1, 'في الطريق', 'الرياض - حي النرجس', '2026-04-10 11:30:00'),
(3, 1, 'تم التوصيل', 'الرياض - شارع التخصصي', '2026-04-10 12:30:00'),
(4, 2, 'تم استلام الشحنة', 'جدة - مركز التوزيع', '2026-04-12 15:00:00'),
(5, 2, 'في الطريق', 'جدة - حي الروضة', '2026-04-12 17:00:00'),
(6, 2, 'تم التوصيل', 'جدة - شارع صاري', '2026-04-12 18:00:00'),
(7, 3, 'تم استلام الشحنة', 'الدمام - مركز التوزيع', '2026-04-18 13:00:00'),
(8, 3, 'في الطريق', 'الدمام - حي الفيصلية', '2026-04-18 16:00:00'),
(9, 4, 'تم تجهيز الشحنة', 'الرياض - مركز التوزيع', '2026-04-30 17:00:00');

-- ============================================================
-- 27. جدول مناديب التوصيل (DELIVERY_DRIVER)
-- ============================================================
INSERT INTO `delivery_driver` (`driver_id`, `driver_name`, `vehicle_number`, `available`) VALUES
(1, 'سامر السعيد', 'سيارة - ABC 1234', 1),
(2, 'باسل العنزي', 'دراجة نارية - M 5678', 1),
(3, 'فارس الشمري', 'سيارة - XYZ 9101', 0),
(4, 'أيمن الحربي', 'دراجة نارية - M 1122', 1);

-- ============================================================
-- 28. جدول التوصيل المباشر (ORDER_DELIVERY)
-- ============================================================
INSERT INTO `order_delivery` (`delivery_id`, `order_id`, `driver_id`, `delivery_status`, `current_lat`, `current_long`, `last_updated`) VALUES
(1, 1, 1, 'delivered', 24.7136, 46.6753, '2026-04-10 12:30:00'),
(2, 2, 2, 'delivered', 21.5433, 39.1728, '2026-04-12 18:00:00'),
(3, 3, 1, 'in_transit', 26.4207, 50.0888, '2026-04-18 16:00:00'),
(4, 5, 4, 'assigned', NULL, NULL, '2026-04-30 16:20:00');

-- ============================================================
-- 29. جدول سجل حالات التوصيل (DELIVERY_STATUS_LOG)
-- ============================================================
INSERT INTO `delivery_status_log` (`log_id`, `delivery_id`, `driver_id`, `delivery_status`, `changed_at`, `notes`) VALUES
(1, 1, 1, 'assigned', '2026-04-10 09:30:00', 'تم إسناد الطلب لسامر'),
(2, 1, 1, 'picked', '2026-04-10 09:45:00', 'تم استلام الطلب من المستودع'),
(3, 1, 1, 'in_transit', '2026-04-10 10:00:00', 'بدأ التوصيل'),
(4, 1, 1, 'delivered', '2026-04-10 12:30:00', 'تم التسليم'),
(5, 2, 2, 'assigned', '2026-04-12 14:40:00', 'تم إسناد الطلب لباسل'),
(6, 2, 2, 'picked', '2026-04-12 15:00:00', 'تم استلام الطلب'),
(7, 2, 2, 'delivered', '2026-04-12 18:00:00', 'تم التسليم'),
(8, 3, 1, 'assigned', '2026-04-18 11:20:00', 'تم إسناد الطلب لسامر'),
(9, 3, 1, 'picked', '2026-04-18 12:00:00', 'تم استلام الطلب'),
(10, 3, 1, 'in_transit', '2026-04-18 13:30:00', 'في الطريق للتوصيل'),
(11, 4, 4, 'assigned', '2026-04-30 16:20:00', 'تم إسناد الطلب لأيمن');

-- ============================================================
-- 30. جدول تتبع الموقع الحي للتوصيل (DELIVERY_TRACKING)
-- ============================================================
INSERT INTO `delivery_tracking` (`tracking_id`, `delivery_id`, `latitude`, `longitude`, `timestamp`) VALUES
(1, 1, 24.7000, 46.6800, '2026-04-10 10:05:00'),
(2, 1, 24.7100, 46.6780, '2026-04-10 10:30:00'),
(3, 1, 24.7130, 46.6760, '2026-04-10 11:00:00'),
(4, 1, 24.7136, 46.6753, '2026-04-10 12:30:00'),
(5, 2, 21.5300, 39.1800, '2026-04-12 16:00:00'),
(6, 2, 21.5400, 39.1750, '2026-04-12 17:30:00'),
(7, 2, 21.5433, 39.1728, '2026-04-12 18:00:00'),
(8, 3, 26.4100, 50.0900, '2026-04-18 14:00:00'),
(9, 3, 26.4150, 50.0890, '2026-04-18 15:30:00'),
(10, 3, 26.4207, 50.0888, '2026-04-18 16:00:00');

-- ============================================================
-- 31. جدول المرتجعات (RETURN_REQUEST)
-- ============================================================
INSERT INTO `return_request` (`return_id`, `order_id`, `return_status`, `reason_text`, `refund_amount`, `request_date`) VALUES
(1, 1, 'completed', 'عبوة بيبسي تالفة', 3.00, '2026-04-11 10:00:00'),
(2, 2, 'rejected', 'المنتج مطابق للمواصفات', NULL, '2026-04-13 09:00:00');

-- ============================================================
-- 32. جدول بنود المرتجعات (RETURN_ITEM)
-- ============================================================
INSERT INTO `return_item` (`return_item_id`, `return_id`, `order_item_id`, `quantity`) VALUES
(1, 1, 3, 1);  -- إرجاع علبة بيبسي واحدة من الطلب 1

-- ============================================================
-- 33. جدول العروض الترويجية (PROMOTION)
-- ============================================================
INSERT INTO `promotion` (`promotion_id`, `promo_name`, `promo_description`, `discount_type`, `discount_value`, `start_date`, `end_date`, `conditions_json`) VALUES
(1, 'خصم مشتريات الألبان', 'خصم 10% على جميع منتجات الألبان', 'percentage', 10.00, '2026-04-01 00:00:00', '2026-04-30 23:59:59', '{"min_quantity": 2}'),
(2, 'عرض المشروبات', 'خصم 5 ريال على المشروبات الغازية', 'fixed', 5.00, '2026-04-15 00:00:00', '2026-05-15 23:59:59', NULL),
(3, 'تخفيضات العيد', 'خصم 20% على المخبوزات', 'percentage', 20.00, '2026-04-20 00:00:00', '2026-05-05 23:59:59', '{"min_quantity": 3}');

-- ============================================================
-- 34. جدول أهداف العروض (PROMOTION_TARGET)
-- ============================================================
INSERT INTO `promotion_target` (`target_id`, `promotion_id`, `target_type`, `target_entity_id`) VALUES
(1, 1, 'category', 1),    -- خصم الألبان يشمل قسم "الألبان والأجبان" (category_id=1)
(2, 2, 'product', 6),     -- عرض المشروبات يشمل "بيبسي كولا" (product_id=6)
(3, 3, 'category', 3);    -- تخفيضات العيد تشمل قسم "المخبوزات" (category_id=3)

-- ============================================================
-- 35. جدول الكوبونات (COUPON)
-- ============================================================
INSERT INTO `coupon` (`coupon_id`, `code`, `discount_type`, `value`, `max_uses`, `current_uses`, `valid_from`, `valid_to`) VALUES
(1, 'WELCOME2026', 'fixed', 15.00, 100, 23, '2026-01-01 00:00:00', '2026-12-31 23:59:59'),
(2, 'RAMADAN10', 'percentage', 10.00, 500, 150, '2026-03-01 00:00:00', '2026-04-30 23:59:59'),
(3, 'SPECIAL50', 'fixed', 50.00, 50, 5, '2026-04-01 00:00:00', '2026-06-30 23:59:59'),
(4, 'MILK5', 'percentage', 5.00, 200, 0, '2026-05-01 00:00:00', '2026-05-31 23:59:59');

-- ============================================================
-- 36. جدول استخدام الكوبونات (COUPON_USAGE)
-- ============================================================
INSERT INTO `coupon_usage` (`usage_id`, `coupon_id`, `user_id`, `order_id`, `used_at`) VALUES
(1, 1, 7, 1, '2026-04-10 09:15:00'),
(2, 2, 8, 2, '2026-04-12 14:30:00'),
(3, 1, 10, 4, '2026-04-25 20:45:00');

-- ============================================================
-- 37. جدول برامج الولاء (LOYALTY_PROGRAM)
-- ============================================================
INSERT INTO `loyalty_program` (`program_id`, `program_name`, `points_per_currency`, `rules_json`) VALUES
(1, 'برنامج الولاء الذهبي', 1.00, '{"silver": 0, "gold": 500, "platinum": 1000}'),
(2, 'برنامج العملاء الجدد', 2.00, '{"silver": 0, "gold": 200, "platinum": 500}');

-- ============================================================
-- 38. جدول اشتراكات العملاء في الولاء (CUSTOMER_LOYALTY)
-- ============================================================
INSERT INTO `customer_loyalty` (`customer_id`, `program_id`, `points_balance`, `tier`) VALUES
(7, 1, 750, 'gold'),
(8, 1, 320, 'silver'),
(9, 2, 150, 'silver'),
(10, 2, 80, 'silver'),
(7, 2, 200, 'silver');

-- ============================================================
-- 39. جدول استبدال نقاط الولاء (LOYALTY_REDEMPTION)
-- ============================================================
INSERT INTO `loyalty_redemption` (`redemption_id`, `customer_id`, `order_id`, `points_used`, `reward_description`) VALUES
(1, 7, 5, 100, 'خصم 10 ريال على الطلب'),
(2, 8, 2, 50, 'توصيل مجاني');

-- ============================================================
-- 40. جدول تقييمات المنتجات (REVIEW)
-- ============================================================
INSERT INTO `review` (`review_id`, `user_id`, `product_id`, `rating`, `comment_text`, `created_at`) VALUES
(1, 7, 1, 5, 'حليب طازج وطعم ممتاز', '2026-04-11 15:00:00'),
(2, 8, 5, 4, 'عصير برتقال لذيذ لكن السعر مرتفع قليلاً', '2026-04-13 10:00:00'),
(3, 9, 6, 3, 'بيبسي عادي، لا جديد', '2026-04-19 14:00:00'),
(4, 10, 7, 5, 'خبز التوست الأبيض طري جداً ورائع', '2026-04-26 09:00:00'),
(5, 7, 3, 4, 'جبنة شيدر جيدة للشطائر', '2026-04-28 20:00:00');

-- ============================================================
-- 41. جدول تقييمات الموردين (REVIEW_SUPPLIER)
-- ============================================================
INSERT INTO `review_supplier` (`review_id`, `user_id`, `supplier_id`, `rating`, `comment_text`, `created_at`) VALUES
(1, 1, 1, 5, 'ممتاز، التزام بالمواعيد', '2026-04-05 08:00:00'),
(2, 2, 2, 4, 'جيد لكن أحياناً يتأخر', '2026-04-06 09:00:00'),
(3, 1, 4, 5, 'خدمة سريعة وأسعار تنافسية', '2026-04-08 11:00:00'),
(4, 3, 5, 4, 'منتجات طازجة لكن الكميات صغيرة', '2026-04-10 07:30:00');

-- ============================================================
-- 42. جدول سلوك المستخدم (USER_BEHAVIOR)
-- ============================================================
INSERT INTO `user_behavior` (`behavior_id`, `user_id`, `product_id`, `action_type`, `event_time`, `session_id`) VALUES
(1, 7, 1, 'view', '2026-04-10 08:00:00', 1001),
(2, 7, 2, 'view', '2026-04-10 08:05:00', 1001),
(3, 7, 1, 'cart', '2026-04-10 08:10:00', 1001),
(4, 7, 7, 'view', '2026-04-10 08:15:00', 1001),
(5, 7, 5, 'cart', '2026-04-10 08:16:00', 1001),
(6, 8, 5, 'view', '2026-04-12 13:00:00', 2001),
(7, 8, 1, 'view', '2026-04-12 13:30:00', 2001),
(8, 9, 8, 'view', '2026-04-18 10:00:00', 3001),
(9, 2, 3, 'search', '2026-04-25 19:00:00', 4001),
(10, 4, 3, 'view', '2026-04-25 19:10:00', 4001);

-- ============================================================
-- 43. جدول تقارب المنتجات للتوصيات (PRODUCT_AFFINITY)
-- ============================================================
INSERT INTO `product_affinity` (`user_id`, `product_id`, `affinity_score`) VALUES
(7, 1, 0.85),
(7, 3, 0.60),
(7, 6, 0.45),
(8, 5, 0.70),
(8, 7, 0.50),
(9, 6, 0.55),
(10, 3, 0.75),
(10, 7, 0.65);

-- ============================================================
-- 44. جدول جلسات الدردشة (CHAT_SESSION)
-- ============================================================
INSERT INTO `chat_session` (`session_id`, `user_id`, `support_staff_id`, `session_status`, `started_at`, `ended_at`) VALUES
(1, 7, 5, 'closed', '2026-04-10 08:30:00', '2026-04-10 08:45:00'),
(2, 8, 6, 'closed', '2026-04-12 13:15:00', '2026-04-12 13:25:00'),
(3, 9, 5, 'active', '2026-04-30 22:00:00', NULL);

-- ============================================================
-- 45. جدول رسائل الدردشة (CHAT_MESSAGE)
-- ============================================================
INSERT INTO `chat_message` (`message_id`, `session_id`, `sender_type`, `message_text`, `sent_at`) VALUES
(1, 1, 'user', 'مرحباً، هل يوجد توصيل سريع؟', '2026-04-10 08:30:00'),
(2, 1, 'agent', 'نعم، التوصيل خلال ساعتين داخل الرياض', '2026-04-10 08:32:00'),
(3, 1, 'user', 'شكراً، سأطلب الآن', '2026-04-10 08:35:00'),
(4, 2, 'user', 'عصير البرتقال متوفر؟', '2026-04-12 13:15:00'),
(5, 2, 'agent', 'نعم متوفر بعبوات 330 مل و1 لتر', '2026-04-12 13:20:00'),
(6, 3, 'user', 'أريد تغيير عنوان التوصيل', '2026-04-30 22:00:00');

-- ============================================================
-- 46. جدول سلة التسوق (CART)
-- ============================================================
INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
(1, 7, '2026-04-01 10:00:00'),
(2, 8, '2026-04-12 13:00:00'),
(3, 10, '2026-04-20 08:30:00');

-- ============================================================
-- 47. جدول بنود سلة التسوق (CART_ITEM)
-- ============================================================
INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `variant_id`, `quantity`) VALUES
(1, 1, 1, 3),   -- سلة علي: 3 حليب كامل الدسم 1لتر
(2, 1, 9, 2),   -- سلة علي: 2 عصير برتقال 330مل
(3, 2, 10, 1),  -- سلة نورة: 1 عصير برتقال 1لتر
(4, 3, 13, 2),  -- سلة منى: 2 خبز توست أبيض
(5, 3, 6, 1);   -- سلة منى: 1 جبنة شيدر 500ج

-- ============================================================
-- تم إدخال جميع السجلات بنجاح!
-- ============================================================