<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = strtolower(trim($_POST['role'])); // Admin/Blogger/Reader
    
    // Validate
    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../register.php");
        exit();
    }

    // Check if username or email exists
    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists.";
        header("Location: ../register.php");
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert
    $insert = $conn->prepare("INSERT INTO users (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("sssss", $fullname, $username, $email, $hashedPassword, $role);

    if ($insert->execute()) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: ../login.php");
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: ../register.php");
    }
}
