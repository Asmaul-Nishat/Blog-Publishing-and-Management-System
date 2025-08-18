<?php
session_start();
include '../php/config.php';

// Ensure only blogger can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'blogger') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-posts.php");
    exit;
}

$postId = (int)$_GET['id'];

// Get post to delete image
$postResult = $conn->query("SELECT image FROM posts WHERE id = $postId");
if ($postResult->num_rows !== 1) {
    header("Location: manage-posts.php");
    exit;
}
$post = $postResult->fetch_assoc();

// Delete image file if exists
if ($post['image'] && file_exists('../uploads/' . $post['image'])) {
    @unlink('../uploads/' . $post['image']);
}

// Delete post record
$conn->query("DELETE FROM posts WHERE id = $postId");

header("Location: manage-posts.php");
exit;
?>
