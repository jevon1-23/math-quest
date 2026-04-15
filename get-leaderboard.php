<?php
require_once 'config.php';

header('Content-Type: application/json');

$skill = $_GET['skill'] ?? '';
$level = $_GET['level'] ?? 1;

try {
    $pdo = getDB();
    
    // Get unique users from progress table
    $sql = "
        SELECT 
            u.username,
            MAX(up.score) as score
        FROM user_progress up
        JOIN users u ON u.id = up.user_id
        GROUP BY u.id, u.username
        ORDER BY score DESC
        LIMIT 10
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll();
    
    // Add rank numbers
    foreach ($results as $i => &$row) {
        $row['rank'] = $i + 1;
    }
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    echo json_encode([]);
}
?>