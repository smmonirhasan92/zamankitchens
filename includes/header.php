<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Premium Kitchen Accessories</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .logo-font { font-family: 'Playfair Display', serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Navbar -->
    <nav class="glass-nav sticky top-0 z-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="<?php echo SITE_URL; ?>" class="logo-font text-2xl font-bold text-gray-800">
                Zaman <span class="text-amber-600">Kitchens</span>
            </a>
            
            <div class="hidden md:flex space-x-8 font-medium">
                <a href="#" class="hover:text-amber-600 transition">Shop All</a>
                <a href="#" class="hover:text-amber-600 transition">Kitchen Sinks</a>
                <a href="#" class="hover:text-amber-600 transition">Faucets</a>
                <a href="#" class="hover:text-amber-600 transition">Accessories</a>
            </div>

            <div class="flex items-center space-x-5">
                <button class="hover:text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <a href="#" class="relative hover:text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="absolute -top-2 -right-2 bg-amber-600 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center">0</span>
                </a>
            </div>
        </div>
    </nav>
