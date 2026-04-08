-- ============================================================
-- Zaman Kitchens — UTF8MB4 Character Set Migration
-- Run this on BOTH local (XAMPP) and live (cPanel) databases
-- This fixes: Bengali text, special characters, emoji support
-- ============================================================

-- Step 1: Set connection encoding
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Step 2: Convert all tables to utf8mb4_unicode_ci
ALTER TABLE `admins`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `categories`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `products`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `orders`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `order_items`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `hero_slides`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `leads`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `users`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `user_addresses`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Step 3: Ensure hero_slides has image_path column (compatibility fix)
ALTER TABLE `hero_slides`
    ADD COLUMN IF NOT EXISTS `image_path` varchar(255) DEFAULT NULL;

-- If image_path already exists, copy from image column (one-time sync)
UPDATE `hero_slides` SET `image_path` = `image` WHERE `image_path` IS NULL AND `image` IS NOT NULL;

-- Step 4: Add is_active column to hero_slides if missing
ALTER TABLE `hero_slides`
    ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) DEFAULT 1;

-- Step 5: Add old_price column to products if missing
ALTER TABLE `products`
    ADD COLUMN IF NOT EXISTS `old_price` decimal(10,2) DEFAULT NULL;

-- Verify: Show current charset of key tables
SELECT TABLE_NAME, TABLE_COLLATION
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME;
