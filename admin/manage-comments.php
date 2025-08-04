<?php
session_start();
include '../php/config.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

// ===== Handle Approve/Disapprove =====
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $status = (int) $_GET['status'];
    $newStatus = $status === 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE comments SET status=? WHERE id=?");
    $stmt->bind_param("ii", $newStatus, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage-comments.php?success=Comment status updated successfully");
    exit;
}

// ===== Handle Delete =====
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM comments WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage-comments.php?success=Comment deleted successfully");
    exit;
}

// ===== Fetch Comments =====
$sql = "SELECT c.id, c.comment_text, c.status, c.created_at,
               u.username AS user_name,
               p.title AS post_title
        FROM comments c
        JOIN users u ON c.user_id = u.id
        JOIN posts p ON c.post_id = p.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Comments - Blogg</title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
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
    overflow-y: auto;
  }
  .logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #cb9191;
    text-align: center;
    margin-bottom: 2rem;
    text-decoration: none;
    display: block;
  }
  nav a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #000;
    font-weight: 600;
    margin-bottom: 8px;
    border-radius: 10px;
  }
  nav a:hover,
  nav a.active { background: #cb9191; color: #000; }
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
  }
  .success-msg {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
  }
  th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }
  th { background: #cb9191; }
  .actions a {
    text-decoration: none;
    margin-right: 10px;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .approve-btn { background: #28a745; color: #fff; }
  .delete-btn { background: #dc3545; color: #fff; }
  .approve-btn:hover { background: #218838; }
  .delete-btn:hover { background: #c82333; }

  /* Responsive table */
  @media (max-width: 768px) {
    table, thead, tbody, th, td, tr { display: block; width: 100%; }
    thead { display: none; }
    tr {
      background: #fff;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px;
    }
    td {
      padding: 8px 10px;
      text-align: right;
      position: relative;
      font-size: 0.95rem;
    }
    td::before {
      content: attr(data-label);
      position: absolute;
      left: 10px;
      font-weight: 600;
      color: #444;
    }
    .actions { text-align: center; padding-top: 10px; }
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
    <a href="manage-categories.php">📂 Categories</a>
    <a href="manage-comments.php" class="active">💬 Comments</a>
    <a href="analytics-blog.php">📈 Analytics</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="logout.php">🔓 Logout</a>
  </nav>
</aside>

<main class="main-content">
  <h1>Manage Comments</h1>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-msg"><?= htmlspecialchars($_GET['success']) ?></div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Post</th>
        <th>Comment</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td data-label="ID"><?= $row['id'] ?></td>
          <td data-label="User"><?= htmlspecialchars($row['user_name']) ?></td>
          <td data-label="Post"><?= htmlspecialchars($row['post_title']) ?></td>
          <td data-label="Comment"><?= htmlspecialchars($row['comment_text']) ?></td>
          <td data-label="Status"><?= $row['status'] ? 'Approved' : 'Pending' ?></td>
          <td data-label="Created"><?= $row['created_at'] ?></td>
          <td data-label="Actions" class="actions">
            <a href="manage-comments.php?toggle=<?= $row['id'] ?>&status=<?= $row['status'] ?>" 
               class="approve-btn">
               <?= $row['status'] ? 'Disapprove' : 'Approve' ?>
            </a>
            <a href="manage-comments.php?delete=<?= $row['id'] ?>" 
               onclick="return confirm('Delete this comment?')"
               class="delete-btn">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

<script>
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.querySelector('.sidebar');
  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });
</script>
</body>
</html>
