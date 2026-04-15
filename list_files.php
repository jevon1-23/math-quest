<?php
// list_files.php - List all PHP files
$files = glob("*.php");
echo "<h1>Files on Server</h1>";
echo "<ul>";
foreach ($files as $file) {
    echo "<li>$file</li>";
}
echo "</ul>";
?>