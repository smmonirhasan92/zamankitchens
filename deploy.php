<?php
// Simple Deployment Script
echo "<pre>Starting Deployment...\n";

// Ensure we are in the right directory
$path = "/home/zamankitchens/public_html";
chdir($path);

// Run git pull (if exec is allowed)
$output = shell_exec("git pull origin main 2>&1");
echo "Git Pull Output:\n$output\n";

echo "Deployment Finished!</pre>";
?>
