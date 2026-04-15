<?php
// download_database.php
$dbFile = '/tmp/mathquest.db';
if (file_exists($dbFile)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="mathquest.db"');
    readfile($dbFile);
    exit;
}
echo "Database not found yet. Play some games first!";
?>