<?php
/**
 * Global Configuration for Zaman Kitchens
 */

// ==============================
// Environment Detection
// ==============================
$isLocalEnv = in_array($_SERVER['SERVER_NAME'] ?? 'cli', ['localhost', '127.0.0.1', '::1', '']);

// Site Information
define('SITE_NAME', 'Zaman Kitchens');
define('SITE_URL', $isLocalEnv ? 'http://localhost/zamankitchen' : 'https://www.zamankitchens.com');

// Contact Information
define('SITE_PHONE', '01720-579899');
define('SITE_PHONE_RAW', '01720579899');
define('SITE_WHATSAPP', '8801720579899');
define('SITE_ADDRESS', 'House-143, Senpara Parbata, Mirpur-10, Dhaka-1216 (Metro Pillar No-263), Dhaka, Bangladesh, 1216');

// Social Media Links
define('SITE_FB', 'https://www.facebook.com/Zamankitchens1');
define('SITE_YT', 'https://youtube.com/@zamankitchens');
define('SITE_INS', 'https://instagram.com/zamankitchens');

// ==============================
// Database Credentials
// ==============================
if ($isLocalEnv) {
    // Local development
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'zamankitchens_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // cPanel Live Server
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'zamankitchens_db');
    define('DB_USER', 'zamankitchens_admin');
    define('DB_PASS', 'Sir@@@admin123'); // Change this after first deploy
}

// Paths
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', SITE_URL . '/assets');

// Security Key
define('SECRET_KEY', 'zaman_kitchen_secret_v1');

// ==============================
// Error Reporting
// ==============================
if ($isLocalEnv) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('Asia/Dhaka');
