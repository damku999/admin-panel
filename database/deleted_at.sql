CREATE TABLE IF NOT EXISTS `fuel_types` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(125) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` tinyint NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

REPLACE INTO `fuel_types` (
    `id`,
    `name`,
    `status`,
    `created_at`,
    `updated_at`,
    `deleted_at`
)
VALUES
    (
        1,
        'Petrol',
        1,
        '2023-06-07 08:09:14',
        '2023-06-07 08:09:14',
        NULL
    ),
    (
        2,
        'Diesel',
        1,
        '2023-06-07 08:09:47',
        '2023-06-07 08:09:47',
        NULL
    ),
    (
        3,
        'Petrol/CNG',
        1,
        '2023-06-07 08:10:15',
        '2023-06-07 08:10:15',
        NULL
    ),
    (
        4,
        'CNG',
        1,
        '2023-06-07 08:10:28',
        '2023-06-07 08:10:28',
        NULL
    );

CREATE TABLE IF NOT EXISTS `policy_types` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` tinyint NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

REPLACE INTO `policy_types` (
    `id`,
    `name`,
    `status`,
    `created_at`,
    `updated_at`,
    `deleted_at`
)
VALUES
    (
        1,
        'Used',
        1,
        '2023-06-07 07:04:43',
        '2023-06-07 07:04:43',
        NULL
    ),
    (
        2,
        'Rollover',
        1,
        '2023-06-07 06:54:36',
        '2023-06-07 06:54:36',
        NULL
    ),
    (
        3,
        'Fresh',
        1,
        '2023-06-07 06:54:49',
        '2023-06-07 07:05:24',
        NULL
    ),
    (
        4,
        'Name Addition',
        1,
        '2023-06-07 06:54:57',
        '2023-06-07 07:05:39',
        NULL
    ),
    (
        5,
        'Renewal',
        1,
        '2023-06-07 13:48:09',
        '2023-06-07 13:48:10',
        NULL
    ),
    (
        6,
        'Endorsment',
        1,
        '2023-06-07 06:55:12',
        '2023-06-07 07:05:59',
        NULL
    );

ALTER TABLE
    `customers`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `branches`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `brokers`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `fuel_types`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `insurance_companies`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `policy_types`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `relationship_managers`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `users`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `premium_types`
ADD
    COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL
AFTER
    `updated_at`;

ALTER TABLE
    `customers`
ADD
    COLUMN `type` ENUM('Corporate', 'Retail') NULL DEFAULT NULL
AFTER
    `engagement_anniversary_date`;

ALTER TABLE
    `customers`
ADD
    COLUMN `pan_card_number` VARCHAR(50) NULL DEFAULT NULL
AFTER
    `status`,
ADD
    COLUMN `aadhar_card_number` VARCHAR(50) NULL DEFAULT NULL
AFTER
    `pan_card_number`,
ADD
    COLUMN `gst_number` VARCHAR(50) NULL DEFAULT NULL
AFTER
    `aadhar_card_number`,
ADD
    COLUMN `pan_card_path` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `gst_number`,
ADD
    COLUMN `aadhar_card_path` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `pan_card_path`,
ADD
    COLUMN `gst_path` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `aadhar_card_path`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `tp_expiry_date` DATE NULL DEFAULT NULL
AFTER
    `expired_date`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `commission_on` ENUM('net_premium', 'od_premium', 'tp_premium') NULL DEFAULT NULL
AFTER
    `make_model`,
ADD
    COLUMN `my_commission_percentage` DOUBLE NULL DEFAULT NULL
AFTER
    `sgst2`,
ADD
    COLUMN `my_commission_amount` DOUBLE NULL DEFAULT NULL
AFTER
    `my_commission_percentage`,
ADD
    COLUMN `transfer_commission_percentage` DOUBLE UNSIGNED NULL DEFAULT NULL
AFTER
    `my_commission_amount`,
ADD
    COLUMN `transfer_commission_amount` DOUBLE NULL DEFAULT NULL
AFTER
    `transfer_commission_percentage`,
ADD
    COLUMN `actual_earnings` DOUBLE NULL DEFAULT NULL
AFTER
    `transfer_commission_amount`;

ALTER TABLE
    `customer_insurances` CHANGE COLUMN `extra6` `policy_document_path` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci'
AFTER
    `insurance_status`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `ncb_percentage` DOUBLE NULL DEFAULT NULL
AFTER
    `actual_earnings`;

ALTER TABLE
    `customer_insurances` CHANGE COLUMN `extra7` `mfg_year` VARCHAR(125) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci'
AFTER
    `policy_document_path`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `gross_vehicle_weight` VARCHAR(500) NULL DEFAULT NULL
AFTER
    `policy_document_path`;

ALTER TABLE
    `branches`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `brokers`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `customers`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `fuel_types`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `insurance_companies`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `policy_types`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `premium_types`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `reference_users`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `relationship_managers`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `users`
ADD
    COLUMN `created_by` INT NULL DEFAULT NULL
AFTER
    `deleted_at`,
ADD
    COLUMN `updated_by` INT NULL DEFAULT NULL
AFTER
    `created_by`,
ADD
    COLUMN `deleted_by` INT NULL DEFAULT NULL
AFTER
    `updated_by`;

ALTER TABLE
    `premium_types`
ADD
    COLUMN `is_life_insurance_policies` TINYINT(3) NOT NULL DEFAULT '0'
AFTER
    `is_vehicle`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `reference_commission_percentage` DOUBLE NULL DEFAULT NULL
AFTER
    `transfer_commission_amount`,
ADD
    COLUMN `reference_commission_amount` DOUBLE NULL DEFAULT NULL
AFTER
    `reference_commission_percentage`,
ADD
    COLUMN `reference_by` INT NULL DEFAULT NULL
AFTER
    `mfg_year`;

ALTER TABLE
    `customer_insurances`
ADD
    COLUMN `plan_name` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `reference_by`,
ADD
    COLUMN `premium_paying_term` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `plan_name`,
ADD
    COLUMN `policy_term` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `premium_paying_term`,
ADD
    COLUMN `sum_insured` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `policy_term`,
ADD
    COLUMN `pension_amount_yearly` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `sum_insured`,
ADD
    COLUMN `approx_maturity_amount` VARCHAR(150) NULL DEFAULT NULL
AFTER
    `pension_amount_yearly`,
ADD
    COLUMN `remarks` TEXT NULL DEFAULT NULL
AFTER
    `approx_maturity_amount`;