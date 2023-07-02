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