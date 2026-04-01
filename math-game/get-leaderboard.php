<?php
require_once 'config.php';

$skill = $_GET['skill'] ?? '';
$mode  = $_GET['mode'] ?? '';

$query = "SELECT username, score FROM leaderboard";

if ($skill && $mode) {
    $query .= " WHERE skill=? AND mode=?";
    $stmt = $conn->prepare($query . " ORDER BY score DESC LIMIT 10");
    $stmt->bind_param("ss", $skill, $mode);
} else {
    $stmt = $conn->prepare($query . " ORDER BY score DESC LIMIT 10");
}

$stmt->execute();
$result = $stmt->get_result();

$players = [];

while ($row = $result->fetch_assoc()) {
    $players[] = $row;
}

echo json_encode($players);
?>