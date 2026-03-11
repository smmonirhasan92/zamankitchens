<?php
/**
 * Zaman Kitchens - Shared Admin Header
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/db.php';

// Auth Guard
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $adminTitle ?? 'Admin Dashboard'; ?> | Zaman Kitchens</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Admin Top Bar -->
<div class="bg-gray-900 text-white px-6 py-3 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center font-bold text-sm">ZK</div>
        <a href="dashboard.php" class="font-bold hover:text-amber-400">Zaman Kitchens Admin</a>
    </div>
    <div class="flex items-center gap-6 text-sm">
        <a href="dashboard.php" class="hover:text-amber-400 transition <?php echo $current_page == 'dashboard.php' ? 'text-amber-400 font-bold' : ''; ?>">Dashboard</a>
        <a href="orders.php" class="hover:text-amber-400 transition <?php echo $current_page == 'orders.php' ? 'text-amber-400 font-bold' : ''; ?>">Orders</a>
        <a href="slides.php" class="hover:text-amber-400 transition <?php echo $current_page == 'slides.php' ? 'text-amber-400 font-bold' : ''; ?>">Manage Slider</a>
        <a href="categories.php" class="hover:text-amber-400 transition <?php echo $current_page == 'categories.php' ? 'text-amber-400 font-bold' : ''; ?>">Manage Categories</a>
        <a href="products.php" class="hover:text-amber-400 transition <?php echo $current_page == 'products.php' ? 'text-amber-400 font-bold' : ''; ?>">Products</a>
        <a href="../" target="_blank" class="hover:text-amber-400 transition">View Site ↗</a>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition font-medium">Logout</a>
    </div>
</div>
