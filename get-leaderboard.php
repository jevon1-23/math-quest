<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache');

$allowed_skills = ['beginner', 'advance', 'expert', 'grand-master'];
$allowed_modes  = ['add','subtract','multiply','div','decimal','fractions',
                   'perimeter','rounding','algebra','area','factorization',
                   'percentage','simplifying-expressions','logarithms',
                   'pythagorean-theorem','trigonometry'];

$skill = $_GET['skill'] ?? '';
$mode  = $_GET['mode']  ?? '';

try {
    $pdo = getDB();

    if ($skill && $mode
        && in_array($skill, $allowed_skills, true)
        && in_array($mode,  $allowed_modes,  true)) {

        $stmt = $pdo->prepare(
            "SELECT username, score FROM leaderboard
             WHERE skill = ? AND mode = ?
             ORDER BY score DESC LIMIT 10"
        );
        $stmt->execute([$skill, $mode]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT username, score FROM leaderboard
             ORDER BY score DESC LIMIT 10"
        );
        $stmt->execute();
    }

    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    error_log("get-leaderboard.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
?>
