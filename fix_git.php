<?php
/**
 * OCE Git Repair Tool - Use this to fix cPanel "The system cannot deploy" error.
 * This script resets the server-side repository to match the remote HEAD.
 */

$repo_path = __DIR__ . "/../repositories/zamankitchens"; // Adjust if needed

echo "<h2>OCE Git Repair Tool</h2>";
echo "Attempting to clean repository at: $repo_path <br><hr>";

if (!is_dir($repo_path)) {
    die("❌ Error: Repository not found at $repo_path");
}

// Change directory to the repository
chdir($repo_path);

echo "Running: git fetch --all... <br>";
$out1 = shell_exec("git fetch --all 2>&1");
echo "<pre>$out1</pre>";

echo "Running: git reset --hard origin/main... <br>";
$out2 = shell_exec("git reset --hard origin/main 2>&1");
echo "<pre>$out2</pre>";

echo "Running: git clean -fd... <br>";
$out3 = shell_exec("git clean -fd 2>&1");
echo "<pre>$out3</pre>";

echo "<br><b>✅ Done!</b> You should now be able to use 'Deploy HEAD Commit' in cPanel.";
?>
