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
$comment = trim($_POST['comment'] ?? '');

if (!$post_id || $comment === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$post_id = intval($post_id);

// ✅ Fetch username first
$userStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$username = $userResult->fetch_assoc()['username'];
$userStmt->close();

// ✅ Insert comment
$stmt = $conn->prepare("INSERT INTO comments (user_id, username, post_id, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $user_id, $username, $post_id, $comment);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode([
        'success' => true,
        'username' => $username,
        'comment' => htmlspecialchars($comment),
        'created_at' => date('M d, Y H:i')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed']);
}
?>
