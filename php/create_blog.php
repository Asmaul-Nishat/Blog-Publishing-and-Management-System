<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$content = $_POST['content'];
$category_id = $_POST['category_id'];

$image = $_FILES['image'];
$imagePath = "../uploads/" . basename($image['name']);
move_uploaded_file($image['tmp_name'], $imagePath);

$sql = "INSERT INTO posts (user_id, title, content, category_id, image_path)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issis", $user_id, $title, $content, $category_id, $imagePath);
$stmt->execute();

echo "Blog posted successfully.";
?>
