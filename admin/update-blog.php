<?php

include '../config.php';
session_start();


if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'blogger'])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_id = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $status = $_POST['status']; 

    // Basic validation
    if (empty($title) || empty($content) || empty($category_id)) {
        echo "All fields are required.";
        exit();
    }

    // Optional: Validate status
    if (!in_array($status, ['draft', 'published'])) {
        $status = 'draft';
    }

    // Update post
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssisi", $title, $content, $category_id, $status, $post_id);

    if ($stmt->execute()) {
        echo "Post updated successfully.";
    } else {
        echo "Error updating post: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
