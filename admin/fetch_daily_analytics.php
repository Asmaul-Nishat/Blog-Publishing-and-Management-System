<?php
require_once '../php/config.php';

$date = $_GET['date'] ?? date('Y-m-d');


$sqlViews = "SELECT SUM(views) AS total_views FROM posts WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($sqlViews);
$stmt->bind_param("s", $date);
$stmt->execute();
$resViews = $stmt->get_result()->fetch_assoc();

// Likes that day
$sqlLikes = "SELECT COUNT(*) AS total_likes FROM likes WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($sqlLikes);
$stmt->bind_param("s", $date);
$stmt->execute();
$resLikes = $stmt->get_result()->fetch_assoc();

// Comments that day
$sqlComments = "SELECT COUNT(*) AS total_comments FROM comments WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($sqlComments);
$stmt->bind_param("s", $date);
$stmt->execute();
$resComments = $stmt->get_result()->fetch_assoc();

// Return JSON
echo json_encode([
  'views' => (int) ($resViews['total_views'] ?? 0),
  'likes' => (int) ($resLikes['total_likes'] ?? 0),
  'comments' => (int) ($resComments['total_comments'] ?? 0)
]);
