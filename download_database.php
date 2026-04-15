<?php
// download_database.php - Download your SQLite database file
require_once 'config.php';

// Password protection - CHANGE THIS PASSWORD!
$secret_password = 'mathquest2024';

if (!isset($_GET['secret']) || $_GET['secret'] !== $secret_password) {
    die("Access denied. Use: download_database.php?secret=mathquest2024");
}

$dbFile = __DIR__ . '/database/mathquest.db';

if (file_exists($dbFile)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="mathquest_backup.db"');
    header('Content-Length: ' . filesize($dbFile));
    readfile($dbFile);
    exit;
} else {
    die("Database file not found at: " . $dbFile);
}
?>