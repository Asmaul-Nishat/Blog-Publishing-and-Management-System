<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Login.html');
    exit;
}

$host = "localhost";
$dbname = "new-blog-database";
$dbUser = "root";
$dbPass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
    exit;
}

// Sanitize POST data
$usernameEmail = trim($_POST['usernameEmail'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (!$usernameEmail || !$password || !$role) {
    die("Please fill all fields. <a href='../Login.html'>Go back</a>");
    exit;
}

$valid_roles = ['admin', 'blogger', 'reader'];
if (!in_array($role, $valid_roles)) {
    die("Invalid role selected. <a href='../Login.html'>Go back</a>");
    exit;
}

// Fetch user by username/email and role
$stmt = $conn->prepare("SELECT * FROM users WHERE (username = :ue OR email = :ue) AND role = :role LIMIT 1");
$stmt->execute([
    ':ue' => $usernameEmail,
    ':role' => $role
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found or role mismatch. <a href='../Login.html'>Try again</a>");
    exit;
}

// Verify password
if (!password_verify($password, $user['password'])) {
    die("Incorrect password. <a href='../Login.html'>Try again</a>");
    exit;
}

// Regenerate session ID for security
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['fullname'] = $user['fullname'];

// Redirect based on role
if ($role === 'admin') {
    header("Location: Admin_dashboard.php");
} elseif ($role === 'blogger') {
    header("Location: blogger_dashboard.php");
} else { // reader or others
    header("Location: index.php");
}
exit;
