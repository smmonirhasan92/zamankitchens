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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .glass-dark { background: rgba(17, 24, 39, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-border { position: relative; border-radius: 1rem; background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.05)); }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

<!-- Modern Admin Top Bar -->
<div class="glass-dark text-white px-8 py-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-gradient-to-tr from-amber-500 to-orange-600 rounded-xl flex items-center justify-center font-black text-lg shadow-lg shadow-amber-500/20">ZK</div>
        <div>
            <a href="dashboard.php" class="font-bold text-lg tracking-tight hover:text-amber-400 transition-colors">Zaman Kitchens</a>
            <div class="text-[10px] font-bold text-amber-500 tracking-[0.2em] uppercase opacity-80">Admin Console</div>
        </div>
    </div>
    
    <div class="flex items-center gap-8">
        <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold text-slate-300">
            <a href="dashboard.php" class="hover:text-amber-400 transition <?php echo $current_page == 'dashboard.php' ? 'text-amber-400 font-bold' : ''; ?>">Dashboard</a>
            <a href="orders.php" class="hover:text-amber-400 transition <?php echo $current_page == 'orders.php' ? 'text-amber-400 font-bold' : ''; ?>">Orders</a>
            <a href="slides.php" class="hover:text-amber-400 transition <?php echo $current_page == 'slides.php' ? 'text-amber-400 font-bold' : ''; ?>">Slider</a>
            <a href="categories.php" class="hover:text-amber-400 transition <?php echo $current_page == 'categories.php' ? 'text-amber-400 font-bold' : ''; ?>">Categories</a>
            <a href="products.php" class="hover:text-amber-400 transition <?php echo $current_page == 'products.php' ? 'text-amber-400 font-bold' : ''; ?>">Products</a>
            <a href="reports.php" class="hover:text-amber-400 transition <?php echo $current_page == 'reports.php' ? 'text-amber-400 font-bold' : ''; ?>">Reports</a>
        </nav>
        
        <div class="h-6 w-[1px] bg-white/10 mx-2 hidden lg:block"></div>
        
        <div class="flex items-center gap-4">
            <a href="../" target="_blank" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 transition text-xs font-bold flex items-center gap-2">
                <span>View Site</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
            <a href="logout.php" class="bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white px-4 py-2 rounded-lg transition text-xs font-bold border border-red-500/20">Logout</a>
        </div>
    </div>
</div>
