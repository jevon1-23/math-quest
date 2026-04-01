<?php
echo "<h1>📁 Files on Math Quest Server</h1>";
echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>";

$files = scandir(__DIR__);
sort($files);

echo "<h2>Total files: " . count($files) . "</h2>";
echo "<ul>";
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        $filepath = __DIR__ . '/' . $file;
        $size = is_file($filepath) ? filesize($filepath) : 0;
        $type = is_dir($filepath) ? '📁' : '📄';
        $size_str = $size > 0 ? " (" . round($size/1024, 1) . " KB)" : "";
        echo "<li>$type $file$size_str</li>";
    }
}
echo "</ul>";

// Check if settings_fixed.php exists
if(file_exists('settings_fixed.php')) {
    echo "<p style='color:green'>✅ settings_fixed.php exists!</p>";
} else {
    echo "<p style='color:red'>❌ settings_fixed.php NOT found!</p>";
}

// Check if login.php exists
if(file_exists('login.php')) {
    echo "<p style='color:green'>✅ login.php exists!</p>";
} else {
    echo "<p style='color:red'>❌ login.php NOT found!</p>";
}
?>