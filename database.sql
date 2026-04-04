-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 04, 2026 at 12:12 PM
-- Server version: 10.11.15-MariaDB-cll-lve
-- PHP Version: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zamankitchens_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$yIXq616lOmoInS3w2hG7J.1qmDTOdABKgau6SKvHR6QE3P3iAmCKC');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_index` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `hero_image`, `image`, `parent_id`, `order_index`) VALUES
(1, 'zaman', 'zaman', 'assets/uploads/ca/zaman-1774613994.jpeg', 'assets/images/cat-cabinet.jpg', NULL, 1),
(2, 'Kitchen Hood', 'kitchen-hood', 'assets/uploads/ca/kitchen-hood-1773733258.jpg', 'assets/images/cat-hood.jpg', NULL, 2),
(3, 'Gas Stove', 'gas-stove', 'assets/uploads/ca/gas-stove-1773733077.jpg', 'assets/images/cat-stove.jpg', NULL, 3),
(5, 'Kitchen', 'kitchen', 'assets/uploads/ca/kitchen-1774614095.jfif', 'assets/uploads/categories/bath-1773397308.jpg', NULL, 4),
(6, 'KITCHEN APPLIANCE', 'kitchen-appliance', 'assets/uploads/ca/kitchen-appliance-1773733107.jpg', NULL, NULL, 5),
(7, 'GEYSER', 'geyser', 'assets/uploads/ca/geyser-1775131299.jpg', NULL, NULL, 6),
(9, 'kitchen Cabinet', 'kitchen-cabinet', 'assets/uploads/ca/kitchen-cabinet-1773820032.jpg', NULL, NULL, 7),
(10, 'Kitchen Accessories', 'kitchen-accessories', 'assets/uploads/ca/kitchen-accessories-1773820740.png', NULL, NULL, 8),
(11, 'Sink', 'sink', 'assets/uploads/ca/sink-1773820794.png', NULL, NULL, 9),
(13, 'Water pureit', 'water-pureit', 'assets/uploads/ca/water-pureit-1773821103.jfif', NULL, NULL, 10),
(14, 'SKB', 'skb', 'assets/uploads/ca/skb-1773821243.jpg', NULL, NULL, 11),
(15, 'Gazi Home Appliance', 'gazi-home-appliance', 'assets/uploads/ca/gazi-home-appliance-1773821331.png', NULL, NULL, 12),
(17, 'Kitchen Home Appliance', 'kitchen-home-appliance', 'assets/uploads/ca/kitchen-home-appliance-1773821863.jpg', NULL, NULL, 13),
(18, 'Kiam', 'kiam', 'assets/uploads/ca/kaim-1773821995.jfif', NULL, NULL, 14),
(19, 'Media/Ariston/Shameem', 'media-ariston-shameem', 'assets/uploads/ca/media-ariston-shameem-1773822150.png', NULL, NULL, 15),
(20, 'Hanger', 'hanger', 'assets/uploads/ca/hanger-1773822302.jfif', NULL, NULL, 16),
(21, 'Mix Self', 'mix-self', 'assets/uploads/ca/mix-self-1773822594.jfif', NULL, NULL, 17),
(23, 'Test Delete', 'test-delete', '', NULL, NULL, 18),
(27, 'Rinnai Gas Stove', 'rinnai-gas-stove', 'assets/uploads/ca/rinnai-gas-stove-1775217469.jfif', NULL, 3, 1),
(28, 'Ariston Gas Stove', 'ariston-gas-stove', 'assets/uploads/ca/ariston-gas-stove-1775217701.jpg', NULL, 3, 2);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `hero_slides`;
CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `button_text` varchar(50) DEFAULT 'Shop Now',
  `button_link` varchar(255) DEFAULT '#products',
  `order_index` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Contacted','Closed') DEFAULT 'New',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `orders` (`id`, `customer_name`, `phone`, `address`, `total_amount`, `status`, `created_at`) VALUES
(1, 'monir hasan', '01985805030', 'Hasib Store, eyarpur union porishod get,Narsinhapur,Asulia', 800283.00, 'Delivered', '2026-03-13 14:16:53'),
(2, 'monir hasan', '01985805030', 'Hasib Store, eyarpur union porishod get,Narsinhapur,Asulia', 191432.00, 'Delivered', '2026-03-14 09:49:24'),
(3, 'Monir Hasan', '01721212121', 'norsingpur,zirabo,dhaka', 9487.00, 'Delivered', '2026-03-14 09:50:28'),
(4, 'Monir Hasan', '01701010101', 'norsingpur,zirabo,dhaka', 26089.00, 'Processing', '2026-03-14 10:36:41'),
(5, 'Monir Hasan', '01712121212', 'norsingpur,zirabo,dhaka', 14193.00, 'Cancelled', '2026-03-14 10:49:12'),
(6, 'monir hasan', '01985805030', 'Hasib Store, eyarpur union porishod get,Narsinhapur,Asulia', 31690.00, 'Pending', '2026-03-14 14:04:46');

-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, NULL, 13, 9487.00),
(2, 1, NULL, 10, 39040.00),
(3, 1, 8, 4, 31690.00),
(4, 1, 9, 3, 14193.00),
(5, 1, 15, 1, 40377.00),
(6, 1, 7, 1, 26089.00),
(7, 1, NULL, 1, 27092.00),
(8, 1, 3, 1, 23655.00),
(9, 2, NULL, 2, 39040.00),
(10, 2, NULL, 1, 27092.00),
(11, 2, 9, 1, 14193.00),
(12, 2, 15, 1, 40377.00),
(13, 2, 8, 1, 31690.00),
(14, 3, NULL, 1, 9487.00),
(15, 4, 7, 1, 26089.00),
(16, 5, 9, 1, 14193.00),
(17, 6, 8, 1, 31690.00);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `price_rules`;
CREATE TABLE `price_rules` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `min_qty` int(11) NOT NULL,
  `discount_type` enum('fixed','percentage') DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
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
  `main_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `barcode`, `slug`, `description`, `price`, `stock_status`, `image_url`, `created_at`, `purchase_price`, `image`, `stock`, `meta_title`, `meta_description`, `variations`, `specifications`, `is_featured`, `stock_qty`, `product_type`, `generic_id`, `dosage_form`, `strength`, `registration_number`, `expiry_date`, `batch_number`, `main_image`) VALUES
(3, 1, 'gazi Smiss stove', NULL, 'gazi-smiss-stove', 'High-quality professional kitchen cabinet for your dream kitchen.', 40.00, 'In Stock', NULL, '2026-03-13 10:14:10', 25.00, 'assets/uploads/products/kitchen-cabinet-pro-3-1773454558.webp', 27, '', '', '[]', '[]', 0, 5, 'physical', NULL, '', '', '', NULL, '', 'assets/uploads/pr/gazi-smiss-stove-1775130599.jpg'),
(4, 2, 'Kitchen Hood Pro 1', NULL, 'kitchen-hood-pro-1', 'High-quality professional kitchen hood for your dream kitchen. Durable and elegant design.', 49997.00, 'In Stock', NULL, '2026-03-13 10:14:10', 11310.00, 'assets/uploads/products/kitchen-hood-pro-1-1773461603.png', 49, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 2, 'Kitchen Hood Pro 3', NULL, 'kitchen-hood-pro-3', 'High-quality professional kitchen hood for your dream kitchen. Durable and elegant design.', 22003.00, 'In Stock', NULL, '2026-03-13 10:14:10', 8708.00, 'assets/uploads/pr/kitchen-hood-pro-3-1773470191.avif', 30, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 3, 'Gas Stove Pro 1', NULL, 'gas-stove-pro-1', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 26089.00, 'In Stock', NULL, '2026-03-13 10:14:10', 11798.00, 'assets/uploads/products/gas-stove-pro-1-1773461628.png', 44, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 3, 'Gas Stove Pro 2', NULL, 'gas-stove-pro-2', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 31690.00, 'In Stock', NULL, '2026-03-13 10:14:10', 12334.00, 'assets/uploads/products/gas-stove-pro-2-1773461546.png', 40, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 3, 'Gas Stove Pro 3', NULL, 'gas-stove-pro-3', 'High-quality professional gas stove for your dream kitchen. Durable and elegant design.', 14193.00, 'In Stock', NULL, '2026-03-13 10:14:10', 6353.00, 'assets/uploads/products/gas-stove-pro-3-1773461505.png', 44, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 5, 'Bath Appliances Pro 3', NULL, 'bath-appliances-pro-3', 'High-quality professional bath appliances for your dream kitchen. Durable and elegant design.', 40377.00, 'In Stock', NULL, '2026-03-13 10:14:10', 23249.00, 'assets/uploads/pr/bath-appliances-pro-3-1773470216.jpg', 47, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 3, 'assjll', NULL, 'assjll', 'djfdoafdsljaklfdosiffl', 456.00, 'In Stock', NULL, '2026-04-02 11:33:44', 333.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 8, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/assjll-1775130964.png'),
(27, 7, 'ajfdsifdjfodiehfkj', NULL, 'ajfdsifdjfodiehfkj', 'ipoiopioklopiop,kjoikloikjljoil', 2500.00, 'In Stock', NULL, '2026-04-02 11:57:53', 2300.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 6, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/ajfdsifdjfodiehfkjier-1775131073.png'),
(28, 3, 'EG-B712G - Gazi Smiss Gas Stove', NULL, 'eg-b712g---gazi-smiss-gas-stove', 'Model : EG-B712G \r\nAuto ignition : 1,00,000+\r\nHeight : 4 inch with leg\r\nBody Measurement : Length - 28 inch, Width - 16 inch\r\nInner Measurement : Length - 24 inch, Width - 14 inch \r\nBody Type : Tempered Glass \r\nOutput Power: 5.0KW + 5.0 KW\r\nBurner Type : Cast Iron\r\nWarranty : 1 Year Service Warranty \r\n', 8000.00, 'In Stock', NULL, '2026-04-03 10:57:49', 9600.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/eg-b712g---gazi-smiss-gas-stove-1775213869.png'),
(29, 3, 'TG-202 - Gazi Smiss Gas Stove', NULL, 'tg-202---gazi-smiss-gas-stove', 'Model : TG-202\r\n\r\nAuto ignition : 50,000+\r\n\r\nGlass Measurement : Length - 28.3 inch, Width - 16.1 inch \r\n\r\nInner Body Measurement : Length - 25 inch, Width - 13.1 inch \r\n\r\nBody Type : High-Quality Tempered Glass \r\n\r\nBurner Type : Brass \r\n\r\nWarranty : 1 Year Service Warranty ', 8000.00, 'In Stock', NULL, '2026-04-03 11:00:48', 9600.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/tg-202---gazi-smiss-gas-stove-1775214048.jpg'),
(30, 3, 'TG-203 - Gazi Smiss Gas Stove', NULL, 'tg-203---gazi-smiss-gas-stove', 'Model : TG-203\r\nAuto ignition : 50,000+\r\nHeight : 4 inch with leg\r\nBody Measurement : Length - 28.3 inch, Width - 16.1 inch \r\nInner Measurement : Length - 25 inch, Width - 13.1 inch \r\nBody Type : Tempered Glass \r\nOutput Power: 4.2 KW\r\nBurner Type : Brass \r\nWarranty : 1 Year Service Warranty ', 10000.00, 'In Stock', NULL, '2026-04-03 11:07:47', 12000.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/tg-203---gazi-smiss-gas-stove-1775214467.png'),
(31, 3, 'EG-B712S - Gazi Smiss Gas Stove', NULL, 'eg-b712s---gazi-smiss-gas-stove', 'Model                       	: EG-B712S \r\n\r\nAuto Ignition            	: 1,00,000+\r\n\r\nHeight                      	: 4 inch with leg\r\n\r\nBody Type              	: Stainless Steel\r\n\r\nBody Measurement 	: Length - 28 inch, Width - 16 inch\r\n\r\nInner Measurement 	: Length - 24 inch, Width - 14 inch\r\n\r\nGas Pressure           	: LPG - 2800Pa, Natural Gas - 2000Pa\r\n\r\nBurner Type             	: Cast Iron', 8000.00, 'In Stock', NULL, '2026-04-03 11:12:51', 9360.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/eg-b712s---gazi-smiss-gas-stove-1775214771.png'),
(32, 3, 'EG-720S - Gazi Smiss Gas Stove', NULL, 'eg-720s---gazi-smiss-gas-stove', 'Model : EG-720S \r\n\r\nAuto ignition : 1,00,000+\r\n\r\nHeight : 4 inch with leg\r\n\r\nBody Measurement : Length - 28 inch, Width - 16 inch \r\n\r\nInner Measurment : Length - 24 inch, Width - 14 inch \r\n\r\nBody Type : Stainless Steel \r\n\r\nOutput Power: 5.0KW + 5.0 KW\r\n\r\nPan Support: Iron made - Even, animal color coating\r\n\r\nGas Pressure: LPG - 2800Pa, NG-2000Pa Cast Iron\r\n\r\nBurner Type : Cast Iron\r\n\r\nWarranty: 1 Year Service Warranty ', 6300.00, 'In Stock', NULL, '2026-04-03 11:14:50', 7440.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/eg-720s---gazi-smiss-gas-stove-1775214927.jfif'),
(34, 3, 'P-311 - Gazi Smiss Gas Stove', NULL, 'p-311---gazi-smiss-gas-stove', 'Model                       	: P-311 \r\n\r\nAuto Ignition            	: 50,000+\r\n\r\nHeight                      	: 4 inch with leg\r\n\r\nBody Measurement  	: Length - 28 inch, Width - 16 inch\r\n\r\nInner Measurement  	: Length - 23 inch, Width - 11 inch\r\n\r\nBody Type              	: Stainless Steel \r\n\r\nBurner Type             	: Brass', 12300.00, 'In Stock', NULL, '2026-04-03 11:44:01', 14400.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/p-311---gazi-smiss-gas-stove-1775216852.jfif'),
(36, 3, 'P-315 - Gazi Smiss Gas Stove', NULL, 'p-315---gazi-smiss-gas-stove', 'Model : P-315\r\n\r\nAuto ignition : 1,00,000+\r\n\r\nGlass Thickness: 8mm\r\n\r\nSS Body Measurement: Length - 29.5 inch, Width - 17 inch \r\n\r\nBody Measurement : Length - 23.5 inch, Width - 12 inch \r\n\r\nBody Type : Stainless Steel \r\n\r\nHeight: 4 inch with leg \r\n\r\nBurner Type : Brass\r\n\r\nWarranty : 1 Year Service Warranty ', 14700.00, 'In Stock', NULL, '2026-04-03 13:03:53', 16800.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/p-315---gazi-smiss-gas-stove-1775221433.png'),
(37, 3, 'P-320C - Gazi Smiss Gas Stove', NULL, 'p-320c---gazi-smiss-gas-stove', 'Model                       	: P-320C \r\n\r\nAuto Ignition            	: 50,000+\r\n\r\nHeight                      	: 4.5 inch with leg\r\n\r\nPated Pressure             : 2.8KPa / 2.0 KPa\r\n\r\nBody Measurement      : Length - 28 inch, Width - 16 inch\r\n\r\nInner Measurement      : Length - 23 inch, Width - 11.5 inch\r\n\r\nBody Type                      : High-Quality Stainless Steel \r\n\r\nBurner Type             	: Brass Burner Cap \r\n\r\nWarranty                         : 1 Year Service Warranty ', 9500.00, 'In Stock', NULL, '2026-04-03 13:06:19', 10800.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/p-320c---gazi-smiss-gas-stove-1775221594.png'),
(38, 3, 'TG-213S - Gazi Smiss Gas Stove', NULL, 'tg-213s---gazi-smiss-gas-stove', 'Model: TG-213S Folding Burner\r\n\r\nAuto ignition: 50,000+\r\n\r\nBody Type: High-Quality Stainless Steel \r\n\r\nBody Measurement: Length - 29.5 inch, With -16.10 inch\r\n\r\nInner Measurement: Length - 28.5 inch, With - 15.5 inch \r\n\r\nBurner Type: Brass Burner Cap \r\n\r\nMetal Knob \r\n\r\nWarranty: 1 Year Service Warranty \r\n\r\n\r\n', 11600.00, 'In Stock', NULL, '2026-04-03 13:08:39', 13200.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/tg-213s---gazi-smiss-gas-stove-1775221719.jpg'),
(39, 3, 'EG-732S - Gazi Smiss Gas Stove', NULL, 'eg-732s---gazi-smiss-gas-stove', '', 14700.00, 'In Stock', NULL, '2026-04-03 13:11:12', 16800.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/eg-732s---gazi-smiss-gas-stove-1775221872.jpg'),
(40, 3, 'GA-BGS-36 - Gazi Smiss Gas Stove', NULL, 'ga-bgs-36---gazi-smiss-gas-stove', 'Model                       	: GA-BGS-36 \r\n\r\nAuto Ignition            	: 50,000+\r\n\r\nHeight                      	: 4 inch with leg\r\n\r\nBody Measurement 	: Length - 29 inch, Width – 17inch\r\n\r\nInner Measurement  	: Length – 23 inch, Width – 12 inch\r\n\r\nBody Type                      : Stainless Steel\r\n\r\nBurner Type             	: Brass\r\n\r\nWarranty                  	: 1 Year Service Warranty ', 14700.00, 'In Stock', NULL, '2026-04-03 13:15:00', 16800.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/ga-bgs-36---gazi-smiss-gas-stove-1775222100.png'),
(41, 3, 'GST-215C - Gazi Gas Stove', NULL, 'gst-215c---gazi-gas-stove', 'Model: GST-215C\r\nGas Type: LPG/NG\r\nBurner Type: Double Burner\r\nAuto Ignition: 50,000 Times\r\nHigh-Quality Auto Pulse Ignition Start\r\nSmoke-Free Blue Flame\r\nLow Gas Consumption\r\n715*410*160mm big body size\r\n0.45x0.35mm, Stainless steel panel with stainless steel cross beam\r\nPan support: 5 ears electroplate\r\nWith a stainless steel water tray\r\nABS Knob\r\nWith a 1pc small grill\r\n100mm+120mm double cast iron burner\r\nBrass burner cap\r\n1 Year Service Warranty', 4800.00, 'In Stock', NULL, '2026-04-03 13:20:56', 5520.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/gst-215c---gazi-gas-stove-1775222456.jpg'),
(42, 3, 'GST-205C - Gazi Gas Stove', NULL, 'gst-205c---gazi-gas-stove', 'Model: GST-205C\r\n\r\nGas Type: LPG/NG\r\n\r\nBurner Type: Double Burner\r\n\r\nAuto Ignition: 50,000 Times\r\n\r\nHigh-Quality Auto Pulse Ignition Start\r\n\r\nSmoke-Free Blue Flame\r\n\r\nLow Gas Consumption\r\n\r\n0.35x0.28mm.Stainless steel panel with stainless steel cross beam\r\n\r\nPan support: 5 ears electroplate\r\n\r\nWith a stainless steel water tray\r\n\r\nABS Knob\r\n\r\nWith a 1pc small grill\r\n\r\n100mm+120mm double cast iron burner\r\n\r\nNG: 100mm + 120mm cast iron burner\r\n\r\n1 Year Service Warranty\r\n\r\n', 3300.00, 'In Stock', NULL, '2026-04-03 13:23:13', 3840.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/gst-205c---gazi-gas-stove-1775222593.jpg'),
(43, 3, 'HTG-2062A - Gazi Gas Stove', NULL, 'htg-2062a---gazi-gas-stove', 'Item Type : Gas Stove\r\nBrand : Gazi\r\nModel : HTG-2062A\r\nType : Double Burner Gas\r\nGas Type : NG/LPG \r\nAuto Ignition with Tornado Blue Flame\r\n50,000 Times Auto Ignition Rated Thermal Flow (Kw)-4.3/3.4\r\nSmoke-Free Blue Flame\r\nRotary Brass Burner Cap\r\nSuper Energy Saving Technology\r\nMultiple Safety Device\r\n1 Service Year Warranty', 2660.00, 'In Stock', NULL, '2026-04-03 13:24:48', 3024.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/htg-2062a---gazi-gas-stove-1775222688.jpg'),
(44, 3, 'HTG-2090A - Gazi Gas Stove', NULL, 'htg-2090a---gazi-gas-stove', 'Brand: Gazi\r\n\r\nModel: HTG-2090A\r\n\r\nGas Type: LPG/NG\r\n\r\nBurner Type: Brass Burner Cap \r\n\r\nPanel Type: MS\r\n\r\nRated Thermal Flow: (KW)-4.3/3.4\r\n\r\nAuto Ignition: 50,000 Times\r\n\r\nHigh-Quality Auto Pulse Ignition Start\r\n\r\nSmoke-Free Blue Flame\r\n\r\nLow Gas Consumption\r\n\r\nMultiple Safety Devices\r\n\r\n120mm & 120mm Double cast iron burner \r\n\r\n0.6mm unitary power-coated body \r\n\r\nSize 725mm X 420mm X 160mm\r\n\r\n1 Year Service Warranty\r\n\r\nPan support: 5 ears electroplate\r\n\r\nWith a stainless steel water tray\r\n\r\nABS Knob\r\n\r\nWith 1 pc small grill\r\n\r\n\r\nInstallation Instructions', 5350.00, 'In Stock', NULL, '2026-04-03 13:26:15', 6120.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/htg-2090a---gazi-gas-stove-1775222811.png'),
(45, 3, 'EG-733S - Gazi Smiss Gas Stove', NULL, 'eg-733s---gazi-smiss-gas-stove', 'Model: EG-733S \r\n\r\nAuto ignition: 50,000+\r\n\r\nBody Type: High-Quality Stainless Steel \r\n\r\nBody Measurement: Length - 29 inch, With -17 inch\r\n\r\nInner Measurement: Length - 27.5 inch, With - 15 inch \r\n\r\nBurner Type: Brass Burner Cap \r\n\r\nWarranty: 1 Year Service Warranty \r\n\r\n\r\n', 15800.00, 'In Stock', NULL, '2026-04-03 13:30:40', 18000.00, 'assets/images/placeholder.jpg', 0, '', '', '[]', '[]', 0, 0, 'physical', NULL, NULL, NULL, NULL, NULL, NULL, 'assets/uploads/pr/eg-733s---gazi-smiss-gas-stove-1775223040.jpg');

-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_label` varchar(50) DEFAULT 'Home',
  `address_line` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_category_parent` (`parent_id`);

ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `price_rules`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `price_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
