<?php

/**
 * OCE Composer Runner
 * Use this to run 'composer install' on shared hosting.
 */

$password = "oce_fix_2026"; // Security check

if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die("Unauthorized. Usage: composer_fix.php?pass=oce_fix_2026");
}

// Paths
$corePath = __DIR__ . '/repositories/zamankitchens';
$composerPhar = __DIR__ . '/composer.phar';

if (!file_exists($composerPhar)) {
    die("Error: composer.phar not found in " . __DIR__);
}

if (!is_dir($corePath)) {
    die("Error: Core directory not found at $corePath");
}

echo "<h2>OCE Composer Fix</h2>";
echo "Running 'php composer.phar install' in $corePath...<br><br>";

// Set memory limit and time limit for long process
ini_set('memory_limit', '512M');
set_time_limit(300);

// Command to run
$cmd = "cd $corePath && php $composerPhar install --no-dev 2>&1";

echo "Executing: $cmd <br><hr><pre>";
$output = shell_exec($cmd);
echo $output;
echo "</pre><hr>";

if (file_exists($corePath . '/vendor/autoload.php')) {
    echo "<b>✅ Success!</b> Vendor directory created and autoload.php is ready.";
} else {
    echo "<b>❌ Failed.</b> Please check the output above or contact support.";
}
