CREATE TABLE Phone( Id int unsigned NOT null AUTO_INCREMENT PRIMARY KEY, Id_user int unsigned NOT null, phone varchar(20) DEFAULT NULL, CONSTRAINT fk_Phone_users FOREIGN KEY(Id_user) REFERENCES users (Id_user) ON DELETE CASCADE ON UPDATE CASCADE );



CREATE TABLE customer( Id_user int unsigned NOT null, date_of_birth DATE DEFAULT null, addres varchar(100) NOT null , favorite varchar(100) DEFAULT null, CONSTRAINT fk_customer_users FOREIGN KEY(Id_user) REFERENCES users (Id_user) ON DELETE CASCADE ON UPDATE CASCADE );



CREATE TABLE admin( Id_user int unsigned NOT null, permissions varchar(100) DEFAULT 'limited', addres varchar(100) NOT null , salary varchar(10) NOT null , department varchar(100) DEFAULT null, CONSTRAINT fk_admin_users FOREIGN KEY(Id_user) REFERENCES users (Id_user) ON DELETE CASCADE ON UPDATE CASCADE );




CREATE TABLE employess( Id_user int unsigned NOT null, permissions varchar(100) DEFAULT 'limited', addres varchar(100) NOT null , salary varchar(10) NOT null , department varchar(100) DEFAULT null, dircation varchar(150) DEFAULT null, status_user ENUM('active','inactive','available','inavailable'), CONSTRAINT fk_employess_users FOREIGN KEY(Id_user) REFERENCES users (Id_user) ON DELETE CASCADE ON UPDATE CASCADE );



CREATE TABLE brand( brand_id int unsigned NOT null PRIMARY KEY, name varchar(10) NOT null , log_url varchar(100) DEFAULT null UNIQUE KEY );



CREATE TABLE `warehouse` (
    `warehouse_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `location` TEXT DEFAULT NULL,
    PRIMARY KEY (`warehouse_id`)
) 


CREATE TABLE roles (role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,role_name VARCHAR(100) NOT NULL UNIQUE,description TEXT);


CREATE TABLE permissions ( permission_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,permission_key VARCHAR(100) NOT NULL UNIQUE,description TEXT);

CREATE TABLE user_role (user_id INT UNSIGNED NOT NULL,role_id INT UNSIGNED NOT NULL,
        PRIMARY KEY (user_id, role_id),
        CONSTRAINT fk_user_role_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,
        CONSTRAINT fk_user_role_role
        FOREIGN KEY (role_id)
        REFERENCES roles(role_id)
        ON DELETE CASCADE
);

-- الدور يمتلك عدة صلاحيات
-- =========================================================

CREATE TABLE role_permission (role_id INT UNSIGNED NOT NULL,permission_id INT UNSIGNED NOT NULL,
        PRIMARY KEY (role_id, permission_id),
        CONSTRAINT fk_role_permission_role
        FOREIGN KEY (role_id)
        REFERENCES roles(role_id)
        ON DELETE CASCADE,
        CONSTRAINT fk_role_permission_permission
        FOREIGN KEY (permission_id)
        REFERENCES permissions(permission_id)
        ON DELETE CASCADE
);



CREATE TABLE product ( product_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, brand_id INT UNSIGNED DEFAULT NULL, base_image_url VARCHAR(500) DEFAULT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, KEY idx_brand (brand_id), KEY idx_active (is_active), CONSTRAINT fk_product_brand FOREIGN KEY (brand_id) REFERENCES brand(brand_id) ON DELETE SET NULL ON UPDATE CASCADE );

















CREATE TABLE product_variant ( variant_id INT UNSIGNED NOT NULL AUTO_INCREMENT, product_id INT UNSIGNED NOT NULL, SKU VARCHAR(100) NOT NULL, size_option VARCHAR(50) DEFAULT NULL, color_option VARCHAR(50) DEFAULT NULL, packaging VARCHAR(50) DEFAULT NULL, price DECIMAL(10,2) NOT NULL, weight_kg DECIMAL(8,3) DEFAULT NULL, image_url VARCHAR(500) DEFAULT NULL, PRIMARY KEY (variant_id), UNIQUE KEY uk_sku (SKU), KEY idx_product (product_id), CONSTRAINT fk_variant_product FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE ON UPDATE CASCADE );


CREATE TABLE product_category ( product_id INT UNSIGNED NOT NULL, category_id INT UNSIGNED NOT NULL, PRIMARY KEY (product_id, category_id), CONSTRAINT fk_pc_product FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_pc_category FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE CASCADE ON UPDATE CASCADE );



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
)









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
