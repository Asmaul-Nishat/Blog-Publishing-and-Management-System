<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}


if (!in_array($_SESSION['user_role'], ['blogger', 'admin'])) {
    http_response_code(403);
    echo "Access denied. You must be a blogger or admin to create a post.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);

    if (!$title || !$content || !$category_id) {
        echo "Please fill all required fields.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, category_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'draft', NOW(), NOW())");
    $stmt->bind_param("issi", $user_id, $title, $content, $category_id);

    if ($stmt->execute()) {
        echo "Post created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
