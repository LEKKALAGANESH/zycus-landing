-- Zycus landing page database schema (MySQL 8.x)

CREATE TABLE IF NOT EXISTS `submissions` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`        VARCHAR(255) NOT NULL,
    `first_name`   VARCHAR(100) NOT NULL,
    `last_name`    VARCHAR(100) NOT NULL,
    `company_name` VARCHAR(255) NOT NULL,
    `company_size` ENUM('small','mid','enterprise','large_enterprise') NOT NULL,
    `role`         VARCHAR(50)  NOT NULL,
    `use_case`     VARCHAR(50)  NOT NULL,
    `notes`        TEXT         NULL,
    `ip_address`   VARCHAR(45)  NOT NULL,
    `user_agent`   VARCHAR(500) NULL,
    `source_url`   VARCHAR(500) NULL,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_submissions_email` (`email`),
    INDEX `idx_submissions_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rate_limit` (
    `ip_address` VARCHAR(45) NOT NULL,
    `hit_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_rate_limit_ip_hit` (`ip_address`, `hit_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
