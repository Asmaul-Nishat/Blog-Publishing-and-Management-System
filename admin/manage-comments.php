<?php
session_start();
require_once '../php/config.php';


if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $conn->query("
    SELECT c.id, c.comment, c.created_at, u.username, p.title AS post_title 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Comments</title>
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
    box-sizing: border-box;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
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
  .sidebar nav a:hover,
  .sidebar nav a.active {
    background: #cb9191;
    color: #000;
  }
  #menu-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    font-size: 1.8rem;
    background: #cb9191;
    color: #000;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    z-index: 1100;
  }
  .main-content {
    flex: 1;
    margin-left: 230px;
    padding: 20px;
    box-sizing: border-box;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }
  th {
    background: #cb9191;
    color: #000;
  }
  .actions a {
    text-decoration: none;
    margin-right: 10px;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    display: inline-block;
  }
  .edit-btn {
    background: #ffc107;
    color: #000;
  }
  .delete-btn {
    background: #dc3545;
    color: #fff;
  }
  .history-btn {
    background: #6c757d;
    color: #fff;
  }
  @media (max-width: 768px) {
    #menu-toggle { display: block; }
    .sidebar { 
      left: -250px;
      transition: left 0.3s ease; 
    }
    .sidebar.open { left: 0; }
    .main-content { margin-left: 0; }
  }
  </style>
</head>
<body>

<button id="menu-toggle">☰</button>

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
  <h2>Manage Comments</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Post</th>
      <th>Comment</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
    <?php foreach($comments as $c): ?>
      <tr>
        <td><?= $c['id'] ?></td>
        <td><?= htmlspecialchars($c['username']) ?></td>
        <td><?= htmlspecialchars($c['post_title']) ?></td>
        <td><?= htmlspecialchars($c['comment']) ?></td>
        <td><?= $c['created_at'] ?></td>
        <td class="actions">
          <a href="edit-comment.php?id=<?= $c['id'] ?>" class="edit-btn">Edit</a>
          <a href="delete-comment.php?id=<?= $c['id'] ?>" class="delete-btn" onclick="return confirm('Delete this comment?')">Delete</a>
          <a href="edit-history.php?id=<?= $c['id'] ?>" class="history-btn">History</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
  document.querySelector('.sidebar').classList.toggle('open');
});
</script>

</body>
</html>
