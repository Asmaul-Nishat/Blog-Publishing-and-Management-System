<?php
session_start();
include '../php/config.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage-users.php");
    exit;
}

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage-users.php?msg=deleted");
} else {
    header("Location: manage-users.php?msg=error");
}
exit;
