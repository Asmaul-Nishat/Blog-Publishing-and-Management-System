<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$post_id || !in_array($action, ['like', 'unlike'])) {
    header('Location: index.php');
    exit;
}

if ($action === 'like') {
    // Insert like if not exists
    $stmt = $conn->prepare("INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $stmt->close();
} else if ($action === 'unlike') {
    // Remove like
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: blog-view.php?id=$post_id");
exit;
