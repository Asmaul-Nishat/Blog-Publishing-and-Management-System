<?php
session_start();
require_once '../php/config.php';

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM comments WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage-comments.php");
exit;
