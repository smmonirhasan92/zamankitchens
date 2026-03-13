<?php
$pageTitle = "Create Account";
require_once __DIR__ . '/includes/header.php';

if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href="profile.php";</script>';
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hash]);
                $success = "Account created successfully! You can now <a href='login.php' class='underline font-bold text-amber-700'>login here</a>.";
            }
        } catch (Exception $e) {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 relative overflow-hidden">
    <!-- Decorative Blurs -->
    <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-amber-400/20 rounded-full blur-3xl -z-10"></div>
    <div class="absolute top-1/3 right-1/4 -translate-y-1/2 w-80 h-80 bg-rose-400/10 rounded-full blur-3xl -z-10"></div>

    <div class="max-w-md w-full glass-card p-10 rounded-[2.5rem] shadow-2xl border border-white bg-white/70 backdrop-blur-xl relative z-10 my-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Join Zaman Kitchens</h2>
            <p class="text-slate-500 mt-2 font-medium leading-relaxed">Create a premium account to track your kitchen upgrades and wishlist instantly.</p>
        </div>

        <?php if($error): ?>
        <div class="bg-rose-50 text-rose-600 p-4 rounded-2xl mb-6 text-sm font-bold border border-rose-100 flex items-center gap-3 animate-fade-in">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if($success): ?>
        <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl mb-6 text-sm text-center border border-emerald-100 font-medium">
            <div class="text-4xl mb-4">🎉</div>
            <?php echo $success; ?>
        </div>
        <?php else: ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-5 py-4 bg-white/50 border border-white rounded-2xl outline-none focus:border-amber-400 focus:bg-white transition-all text-slate-900 font-medium shadow-sm placeholder-slate-300" placeholder="John Doe">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-5 py-4 bg-white/50 border border-white rounded-2xl outline-none focus:border-amber-400 focus:bg-white transition-all text-slate-900 font-medium shadow-sm placeholder-slate-300" placeholder="hello@example.com">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 pl-1">Password</label>
                <input type="password" name="password" required class="w-full px-5 py-4 bg-white/50 border border-white rounded-2xl outline-none focus:border-amber-400 focus:bg-white transition-all text-slate-900 font-medium shadow-sm placeholder-slate-300" placeholder="Create a strong password">
            </div>
            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 rounded-2xl transition-all hover:-translate-y-1 shadow-xl hover:shadow-2xl shadow-slate-900/20 mt-6 flex items-center justify-center gap-2">
                Create Account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>
        <?php endif; ?>

        <div class="mt-8 text-center text-sm text-slate-500 font-medium">
            Already have an account? <a href="login.php" class="text-amber-600 hover:text-amber-700 font-bold hover:underline transition">Log in</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
