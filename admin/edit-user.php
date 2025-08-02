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

// Fetch existing user data
$sql = "SELECT id, username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage-users.php");
    exit;
}

$user = $result->fetch_assoc();
$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($email) || empty($role)) {
        $error = "All fields are required!";
    } else {
        $updateSql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssi", $username, $email, $role, $id);
        if ($updateStmt->execute()) {
            $success = "User updated successfully!";
            $user['username'] = $username;
            $user['email'] = $email;
            $user['role'] = $role;
        } else {
            $error = "Failed to update user!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit User - Admin</title>
<style>
  :root {
    --font-family: 'Merriweather', serif;
    --black: #000;
    --white: #f0f0f0;
    --gray: #ddd;
    --button-hover-bg: #cb9191;
    --radius: 8px;
    --transition: 0.3s ease;
  }
  body {
    font-family: var(--font-family);
    margin: 0;
    padding: 20px;
    background: var(--white);
    color: var(--black);
  }
  .form-container {
    max-width: 500px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    margin-bottom: 20px;
  }
  label {
    display: block;
    margin-top: 10px;
    font-weight: 600;
  }
  input, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid var(--gray);
    border-radius: var(--radius);
  }
  .btn {
    display: inline-block;
    background: var(--button-hover-bg);
    color: #fff;
    padding: 10px 20px;
    margin-top: 15px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    font-weight: bold;
    transition: background-color var(--transition);
  }
  .btn:hover {
    background-color: #a56f6f;
  }
  .message {
    text-align: center;
    margin-top: 10px;
    color: green;
    font-weight: bold;
  }
  .error {
    color: red;
  }
  @media (max-width: 480px) {
    .form-container {
      width: 90%;
      padding: 15px;
    }
  }
</style>
</head>
<body>

<div class="form-container">
  <h1>Edit User</h1>
  <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
  <?php if ($success): ?><p class="message"><?= $success ?></p><?php endif; ?>

  <form method="POST">
    <label>Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Role:</label>
    <select name="role" required>
      <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
      <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit" class="btn">Update User</button>
  </form>
</div>

</body>
</html>
