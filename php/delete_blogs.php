<?php
session_start();
require 'config.php'; // your DB connection file

// Check if user is logged in and is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die("Access denied. Admins only.");
}

// Check if blog ID is provided and is a valid number
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    die("Invalid blog ID.");
}

$blogId = intval($_GET['id']);

// Prepare and execute delete statement
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $blogId);

if ($stmt->execute()) {
    echo "Blog post deleted successfully.";
} else {
    http_response_code(500);
    echo "Error deleting blog post: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
