<?php
session_start();
include '../php/config.php';

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ===== Handle Add Category =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        // Check for duplicates
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "Category already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
            $msg = "Category added successfully!";
        }
        $check->close();
    }
}

// ===== Handle Delete Category =====
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: categories.php?success=Category deleted successfully");
    exit;
}

// ===== Fetch All Categories =====
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Categories - Blogg</title>
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
  .success-msg {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
  }
  .error-msg {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
  }
  .category-form {
    margin-bottom: 20px;
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .category-form input[type="text"] {
    width: 70%;
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
  }
  .category-form button {
    background: #cb9191;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
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
  th {
    background: #cb9191;
  }
  .actions a {
    text-decoration: none;
    margin-right: 10px;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
  }
  .edit-btn {
    background: #ffc107;
    color: #000;
  }
  .delete-btn {
    background: #dc3545;
    color: #fff;
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
    <a href="categories.php" class="active">📂 Categories</a>
    <a href="manage-comments.php">💬 Comments</a>
    <a href="analytics-blog.php">📈 Analytics</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="logout.php">🔓 Logout</a>
  </nav>
</aside>

<main class="main-content">
  <h1>Manage Categories</h1>

  <?php if (!empty($msg)): ?>
    <div class="<?= strpos($msg, 'successfully') !== false ? 'success-msg' : 'error-msg' ?>">
      <?= htmlspecialchars($msg) ?>
    </div>
  <?php elseif (isset($_GET['success'])): ?>
    <div class="success-msg"><?= htmlspecialchars($_GET['success']) ?></div>
  <?php endif; ?>

  <form method="POST" class="category-form">
    <input type="text" name="name" placeholder="New Category Name" required />
    <button type="submit" name="add_category">Add Category</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td class="actions">
            <a href="edit-category.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
            <a href="categories.php?delete=<?= $row['id'] ?>"
               onclick="return confirm('Are you sure you want to delete this category?')"
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
