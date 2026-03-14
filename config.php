<?php
/**
 * Global Configuration for Zaman Kitchens
 */

// Site Information
define('SITE_NAME', 'Zaman Kitchens');
define('SITE_URL', 'https://www.zamankitchens.com');

// Contact Information
define('SITE_PHONE', '01720-579899');
define('SITE_PHONE_RAW', '01720579899');
define('SITE_WHATSAPP', '8801720579899');
define('SITE_ADDRESS', 'House-143, Senpara Parbata, Mirpur-10, Dhaka-1216 (Metro Pillar No-263), Dhaka, Bangladesh, 1216');

// Social Media Links
define('SITE_FB', 'https://www.facebook.com/Zamankitchens1');
define('SITE_YT', 'https://youtube.com/@zamankitchens');
define('SITE_INS', 'https://instagram.com/zamankitchens');

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'zamankitchens_db');
define('DB_USER', 'zamankitchens_admin');
define('DB_PASS', 'Sir@@@admin123');

// Paths
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', SITE_URL . '/assets');

// Security Key
define('SECRET_KEY', 'zaman_kitchen_secret_v1');

// Error Reporting (Set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Dhaka');
