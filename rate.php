<?php
session_start();
require_once 'php/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
$rating = intval($_POST['rating'] ?? 0);

if (!$post_id || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$post_id = intval($post_id);

// Insert or update rating
$stmt = $conn->prepare("
    INSERT INTO ratings (user_id, username, post_id, rating) 
    VALUES (?, (SELECT username FROM users WHERE id=?), ?, ?)
    ON DUPLICATE KEY UPDATE rating = VALUES(rating)
");
$stmt->bind_param("iiii", $user_id, $user_id, $post_id, $rating);
$stmt->execute();
$stmt->close();

// Calculate new average rating
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$avg = round($result->fetch_assoc()['avg_rating'], 1) ?: 0;
$stmt->close();

echo json_encode(['success' => true, 'averageRating' => $avg]);
?>
