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
        .glass-header { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        .sidebar-link-active { color: #f59e0b; background: rgba(245, 158, 11, 0.05); }
    </style>
</head>
<body class="bg-[#F9FAFB] text-slate-900">

<!-- Elegant Admin Top Bar -->
<div class="glass-header text-slate-900 px-8 py-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center font-black text-white text-lg shadow-lg shadow-amber-500/30">ZK</div>
        <div>
            <a href="dashboard.php" class="font-bold text-lg tracking-tight hover:text-amber-600 transition-colors">Zaman Kitchens</a>
            <div class="text-[10px] font-bold text-slate-400 tracking-[0.2em] uppercase">Control Center</div>
        </div>
    </div>
    
    <div class="flex items-center gap-10">
        <nav class="hidden lg:flex items-center gap-8 text-[13px] font-bold text-slate-500">
            <a href="dashboard.php" class="hover:text-amber-500 transition <?php echo $current_page == 'dashboard.php' ? 'text-amber-600' : ''; ?>">Dashboard</a>
            <a href="orders.php" class="hover:text-amber-500 transition <?php echo $current_page == 'orders.php' ? 'text-amber-600' : ''; ?>">Orders</a>
            <a href="slides.php" class="hover:text-amber-500 transition <?php echo $current_page == 'slides.php' ? 'text-amber-600' : ''; ?>">Slider</a>
            <a href="categories.php" class="hover:text-amber-500 transition <?php echo $current_page == 'categories.php' ? 'text-amber-600' : ''; ?>">Categories</a>
            <a href="products.php" class="hover:text-amber-500 transition <?php echo $current_page == 'products.php' ? 'text-amber-600' : ''; ?>">Products</a>
            <a href="reports.php" class="hover:text-amber-500 transition <?php echo $current_page == 'reports.php' ? 'text-amber-600' : ''; ?>">Reports</a>
        </nav>
        
        <div class="flex items-center gap-5">
            <a href="../" target="_blank" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-xs font-bold flex items-center gap-2 text-slate-600">
                <span>View Site</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
            <a href="logout.php" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition text-xs font-bold shadow-lg shadow-slate-900/10">Logout</a>
        </div>
    </div>
</div>
