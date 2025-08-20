<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $username = trim($_POST['usernameEmail']);
    $password = $_POST['password'];
    $role = strtolower(trim($_POST['role'])); 


    $stmt = $conn->prepare("SELECT id, fullname, username, password, role 
                            FROM users 
                            WHERE (username=? OR email=?) AND role=? LIMIT 1");
    $stmt->bind_param("sss", $username, $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
           
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = strtolower($user['role']);

         
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../Admin/dashboard.php");
            } elseif ($_SESSION['role'] === 'blogger') {
                header("Location: ../blogger/blogger-dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        }
    }

   
    $_SESSION['error'] = "Invalid username, password, or role.";
    header("Location: ../login.php");
    exit();
}
