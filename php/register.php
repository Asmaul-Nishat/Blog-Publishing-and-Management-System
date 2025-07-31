<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Register.html');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$dbname = "new-blog-database";
$dbUser = "root";
$dbPass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$role = strtolower($_POST['role'] ?? '');

$errors = [];

if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
    $errors[] = "All fields are required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}
if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
$stmt->execute(['username' => $username, 'email' => $email]);
if ($stmt->rowCount() > 0) {
    $errors[] = "Username or email already exists.";
}

if (!empty($errors)) {
    // Convert errors array to JavaScript alert and redirect back
    $errorMessage = implode("\\n", $errors);
    echo "<script>
        alert('$errorMessage');
        window.location.href = '../Register.html';
    </script>";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insert = $conn->prepare("INSERT INTO users (fullname, username, email, password, role) 
                          VALUES (:fullname, :username, :email, :password, :role)");
$insert->execute([
    'fullname' => $fullname,
    'username' => $username,
    'email' => $email,
    'password' => $hashedPassword,
    'role' => $role
]);

header("Location: ../php/login.php?success=1");
exit;
?>
