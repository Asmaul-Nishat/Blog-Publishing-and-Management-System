<?php
session_start();
require_once '../php/config.php';

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT comment FROM comments WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$commentData = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newComment = trim($_POST['comment']);
    $oldComment = $commentData['comment'];

    // Update comment
    $update = $conn->prepare("UPDATE comments SET comment=? WHERE id=?");
    $update->bind_param("si", $newComment, $id);
    $update->execute();

    // Save edit history
    $history = $conn->prepare("INSERT INTO comment_edits (comment_id, edited_by, old_comment, new_comment) VALUES (?, ?, ?, ?)");
    $history->bind_param("iiss", $id, $_SESSION['user_id'], $oldComment, $newComment);
    $history->execute();

    header("Location: manage-comments.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Comment</title>
<style>
  body {
    margin: 0;
    font-family: 'Merriweather', serif;
    background: #f0f0f0;
    color: #000;
    display: flex;
  }
  .sidebar {
    width: 230px;
    background: #fff;
    border-right: 1px solid #ddd;
    padding: 20px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
  }
  .sidebar .logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #cb9191;
    text-align: center;
    margin-bottom: 2rem;
    text-decoration: none;
    display: block;
  }
  .sidebar nav a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #000;
    font-weight: 600;
    margin-bottom: 8px;
    border-radius: 10px;
  }
  .sidebar nav a:hover {
    background: #cb9191;
    color: #000;
  }
  .main-content {
    flex: 1;
    margin-left: 230px;
    padding: 40px;
  }
  .edit-container {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    max-width: 600px;
    margin: 0 auto;
  }
  .edit-container h2 {
    margin-bottom: 20px;
    color: #cb9191;
    text-align: center;
  }
  textarea {
    width: 100%;
    height: 120px;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
    resize: vertical;
    margin-bottom: 20px;
  }
  button {
    background: #cb9191;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    display: block;
    margin: 0 auto;
  }
  button:hover {
    background: #b87b7b;
  }
</style>
</head>
<body>
<aside class="sidebar">
  <a href="../index.php" class="logo">Blogg</a>
  <nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage-posts.php">📝 Manage Posts</a>
    <a href="manage-users.php">👤 Manage Users</a>
    <a href="categories.php">📂 Categories</a>
    <a href="manage-comments.php" class="active">💬 Comments</a>
    <a href="analytics-blog.php">📈 Analytics</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="logout.php">🔓 Logout</a>
  </nav>
</aside>

<div class="main-content">
  <div class="edit-container">
    <h2>Edit Comment</h2>
    <form method="post">
      <textarea name="comment"><?= htmlspecialchars($commentData['comment']) ?></textarea>
      <button type="submit">Save Changes</button>
    </form>
  </div>
</div>
</body>
</html>
