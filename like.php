<?php
require_once 'php/config.php';
session_start();

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required to like.']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? $_GET['post_id'] ?? 0);
$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
    exit;
}

// Check if user already liked
$stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0 && $action === 'unlike') {
  
    $delete = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $delete->bind_param("ii", $user_id, $post_id);
    $delete->execute();
    $delete->close();
} elseif ($action === 'like') {
   
    $insert = $conn->prepare("INSERT INTO likes (user_id, username, post_id) VALUES (?, ?, ?)");
    $insert->bind_param("isi", $user_id, $username, $post_id);
    $insert->execute();
    $insert->close();
}
$stmt->close();

// Get updated like count
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE post_id = ?");
$countStmt->bind_param("i", $post_id);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$countStmt->close();

echo json_encode(['success' => true, 'likeCount' => $countResult['total']]);
exit;
