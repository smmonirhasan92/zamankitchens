-- Zaman Kitchens - Master Production Setup (v2.2)
-- Source: Live cPanel Export (Apr 02, 2026)
-- Use this file to restore the database to the exact live state.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$yIXq616lOmoInS3w2hG7J.1qmDTOdABKgau6SKvHR6QE3P3iAmCKC')
ON DUPLICATE KEY UPDATE password=VALUES(password);

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_category_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `hero_image`, `image`, `parent_id`) VALUES
(1, 'zaman', 'zaman', 'assets/uploads/ca/zaman-1774613994.jpeg', 'assets/images/cat-cabinet.jpg', NULL),
(2, 'Kitchen Hood', 'kitchen-hood', 'assets/uploads/ca/kitchen-hood-1773733258.jpg', 'assets/images/cat-hood.jpg', NULL),
(3, 'Gas Stove', 'gas-stove', 'assets/uploads/ca/gas-stove-1773733077.jpg', 'assets/images/cat-stove.jpg', NULL),
(5, 'Kitchen', 'kitchen', 'assets/uploads/ca/kitchen-1774614095.jfif', 'assets/uploads/categories/bath-1773397308.jpg', NULL),
(6, 'KITCHEN APPLIANCE', 'kitchen-appliance', 'assets/uploads/ca/kitchen-appliance-1773733107.jpg', NULL, NULL),
(7, 'GEYSER', 'geyser', 'assets/uploads/ca/geyser-1773733158.jpg', NULL, NULL),
(9, 'kitchen Cabinet', 'kitchen-cabinet', 'assets/uploads/ca/kitchen-cabinet-1773820032.jpg', NULL, NULL),
(10, 'Kitchen Accessories', 'kitchen-accessories', 'assets/uploads/ca/kitchen-accessories-1773820740.png', NULL, NULL),
(11, 'Sink', 'sink', 'assets/uploads/ca/sink-1773820794.png', NULL, NULL),
(13, 'Water pureit', 'water-pureit', 'assets/uploads/ca/water-pureit-1773821103.jfif', NULL, NULL),
(14, 'SKB', 'skb', 'assets/uploads/ca/skb-1773821243.jpg', NULL, NULL),
(15, 'Gazi Home Appliance', 'gazi-home-appliance', 'assets/uploads/ca/gazi-home-appliance-1773821331.png', NULL, NULL),
(17, 'Kitchen Home Appliance', 'kitchen-home-appliance', 'assets/uploads/ca/kitchen-home-appliance-1773821863.jpg', NULL, NULL),
(18, 'Kiam', 'kiam', 'assets/uploads/ca/kaim-1773821995.jfif', NULL, NULL),
(19, 'Media/Ariston/Shameem', 'media-ariston-shameem', 'assets/uploads/ca/media-ariston-shameem-1773822150.png', NULL, NULL),
(20, 'Hanger', 'hanger', 'assets/uploads/ca/hanger-1773822302.jfif', NULL, NULL),
(21, 'Mix Self', 'mix-self', 'assets/uploads/ca/mix-self-1773822594.jfif', NULL, NULL),
(23, 'Test Delete', 'test-delete', '', NULL, NULL),
(24, 'monir', 'monir', 'assets/uploads/ca/monir-1774775368.png', NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_status` enum('In Stock','Out of Stock') DEFAULT 'In Stock',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT 'assets/images/placeholder.jpg',
  `stock` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `variations` longtext DEFAULT NULL,
  `specifications` longtext DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `stock_qty` int(11) DEFAULT 0,
  `product_type` varchar(50) DEFAULT 'physical',
  `generic_id` int(11) DEFAULT NULL,
  `dosage_form` varchar(100) DEFAULT NULL,
  `strength` varchar(100) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `barcode`, `slug`, `description`, `price`, `stock_status`, `image_url`, `created_at`, `purchase_price`, `image`, `stock`, `meta_title`, `meta_description`, `variations`, `specifications`, `is_featured`, `stock_qty`, `product_type`, `generic_id`, `dosage_form`, `strength`, `registration_number`, `expiry_date`, `batch_number`, `main_image`) VALUES
(3, 1, 'gazi stove', '', 'gazi-stove', 'High-quality professional kitchen cabinet for your dream kitchen. Durable and elegant design.', 20.00, 'In Stock', NULL, '2026-03-13 10:14:10', 15.00, 'assets/uploads/products/kitchen-cabinet-pro-3-1773454558.webp', 27, '', '', '[]', '[]', 0, 8, 'physical', NULL, '', '', '', NULL, '', 'assets/uploads/pr/gazi-stove-1775104175.jpg'),
(4, 2, 'Kitchen Hood Pro 1', NULL, 'kitchen-hood-pro-1', 'High-quality professional kitchen hood for your dream kitchen. Durable and elegant design.', 49997.00, 'In Stock', NULL, '2026-03-13 10:14:10', 11310.00, 'assets/uploads/products/kitchen-hood-pro-1-1773461603.png', 49, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 2, 'Kitchen Hood Pro 3', NULL, 'kitchen-hood-pro-3', 'High-quality professional kitchen hood for your dream kitchen. Durable and elegant design.', 22003.00, 'In Stock', NULL, '2026-03-13 10:14:10', 8708.00, 'assets/uploads/pr/kitchen-hood-pro-3-1773470191.avif', 30, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 3, 'Gas Stove Pro 1', NULL, 'gas-stove-pro-1', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 26089.00, 'In Stock', NULL, '2026-03-13 10:14:10', 11798.00, 'assets/uploads/products/gas-stove-pro-1-1773461628.png', 44, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 3, 'Gas Stove Pro 2', NULL, 'gas-stove-pro-2', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 31690.00, 'In Stock', NULL, '2026-03-13 10:14:10', 12334.00, 'assets/uploads/products/gas-stove-pro-2-1773461546.png', 40, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 3, 'Gas Stove Pro 3', NULL, 'gas-stove-pro-3', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 14193.00, 'In Stock', NULL, '2026-03-13 10:14:10', 6353.00, 'assets/uploads/products/gas-stove-pro-3-1773461505.png', 44, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, NULL, 'Premium Sinks Pro 1', NULL, 'premium-sinks-pro-1', 'High-quality professional premium sinks for your dream kitchen. Durable and elegant design.', 21277.00, 'In Stock', NULL, '2026-03-13 10:14:10', 17030.00, 'assets/uploads/pr/premium-sinks-pro-1-1773470165.webp', 25, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, NULL, 'Premium Sinks Pro 2', NULL, 'premium-sinks-pro-2', 'High-quality professional premium sinks for your dream kitchen. Durable and elegant design.', 49907.00, 'In Stock', NULL, '2026-03-13 10:14:10', 13096.00, 'assets/uploads/products/premium-sinks-pro-2-1773461442.png', 29, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, NULL, 'Premium Sinks Pro 3', NULL, 'premium-sinks-pro-3', 'High-quality professional premium sinks for your dream kitchen. Durable and elegant design.', 37478.00, 'In Stock', NULL, '2026-03-13 10:14:10', 21207.00, 'assets/uploads/products/premium-sinks-pro-3-1773461366.avif', 19, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 5, 'Bath Appliances Pro 1', NULL, 'bath-appliances-pro-1', 'High-quality professional bath appliances for your dream kitchen. Durable and elegant design.', 9487.00, 'In Stock', NULL, '2026-03-13 10:14:10', 23511.00, 'assets/uploads/products/bath-appliances-pro-1-1773461476.png', 20, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 5, 'Bath Appliances Pro 2', NULL, 'bath-appliances-pro-2', 'High-quality professional bath appliances for your dream kitchen. Durable and elegant design.', 39040.00, 'In Stock', NULL, '2026-03-13 10:14:10', 10685.00, 'assets/uploads/products/bath-appliances-pro-2-1773461460.png', 32, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 5, 'Bath Appliances Pro 3', NULL, 'bath-appliances-pro-3', 'High-quality professional bath appliances for your dream kitchen. Durable and elegant design.', 40377.00, 'In Stock', NULL, '2026-03-13 10:14:10', 23249.00, 'assets/uploads/pr/bath-appliances-pro-3-1773470216.jpg', 47, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------
-- Users (Frontend Login Support)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Leads, Orders, and Other Core Tables
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Contacted','Closed') DEFAULT 'New',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `price_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `min_qty` int(11) NOT NULL,
  `discount_type` enum('fixed','percentage') DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `button_text` varchar(50) DEFAULT 'Shop Now',
  `button_link` varchar(255) DEFAULT '#products',
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
