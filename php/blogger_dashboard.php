<?php
session_start();

// --- DB Config ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'new-blog-database');  // Update to your DB name
define('DB_USER', 'root');
define('DB_PASS', '');

// Connect PDO
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed.');
}

// --- Authentication ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'blogger') {
    http_response_code(401);
    exit('Unauthorized');
}

$userId = $_SESSION['user_id'];

// --- Helper for JSON response ---
function jsonResponse($status, $message = '') {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// --- Handle AJAX POST requests ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$email) {
            jsonResponse('error', 'Username and Email are required.');
        }

        try {
            if ($password) {
                $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $email, $hashedPwd, $userId]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $userId]);
            }
            jsonResponse('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            jsonResponse('error', 'Failed to update profile.');
        }
    }

    if ($action === 'delete_post') {
        $postId = (int)($_POST['post_id'] ?? 0);
        if ($postId <= 0) {
            jsonResponse('error', 'Invalid post ID.');
        }
        // Verify ownership
        $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        if ($stmt->fetch()) {
            $delStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $delStmt->execute([$postId]);
            jsonResponse('success', 'Post deleted successfully.');
        } else {
            jsonResponse('error', 'Post not found or unauthorized.');
        }
    }

    if ($action === 'delete_account') {
        try {
            // Delete posts
            $pdo->prepare("DELETE FROM posts WHERE user_id = ?")->execute([$userId]);
            // Delete user
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
            session_destroy();
            jsonResponse('success', 'Account deleted successfully.');
        } catch (Exception $e) {
            jsonResponse('error', 'Failed to delete account.');
        }
    }

    jsonResponse('error', 'Unknown action.');
}

// --- Fetch user info ---
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userInfo) {
    http_response_code(404);
    exit('User not found.');
}

// --- Fetch user posts ---
$stmt = $pdo->prepare("SELECT id, title, status, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$userPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Now you can use $userInfo and $userPosts to render your page or API output.
