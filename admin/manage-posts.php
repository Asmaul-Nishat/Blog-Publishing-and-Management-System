<?php
session_start();
include '../php/config.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Pagination settings
$postsPerPage = 6;
$totalPostsResult = $conn->query("SELECT COUNT(*) AS count FROM posts");
$totalPosts = $totalPostsResult->fetch_assoc()['count'];
$totalPages = ceil($totalPosts / $postsPerPage);

$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPages) $currentPage = $totalPages;

$offset = ($currentPage - 1) * $postsPerPage;

// Fetch posts for current page
$sqlPosts = "
    SELECT p.id, p.title, LEFT(p.content, 150) AS excerpt, p.image, p.created_at, IFNULL(u.fullname, 'Admin') AS author
    FROM posts p
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
    LIMIT $offset, $postsPerPage
";
$posts = $conn->query($sqlPosts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Posts - Blogg Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
<style>
  :root {
    --font-family: 'Merriweather', serif;
    --black: #000000;
    --white: #f0f0f0;
    --gray: #dddddd;
    --input-border: #999999;
    --button-hover-bg: #cb9191;
    --label-text-default: #666666;
    --error-text: red;
    --radius: 12px;
    --transition: 0.3s ease;
    --max-width: 1200px;
  }
  body {
    margin: 0;
    font-family: var(--font-family);
    background: var(--white);
    color: var(--black);
    min-height: 100vh;
    display: flex;
  }
  .sidebar {
    width: 230px;
    background: var(--white);
    border-right: 1px solid var(--gray);
    padding: 20px;
    box-sizing: border-box;
  }
  .sidebar .logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--button-hover-bg);
    text-align: center;
    margin-bottom: 2rem;
    text-decoration: none;
    user-select: none;
    font-family: var(--font-family);
    cursor: pointer;
    display: block;
  }
  .sidebar nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    margin-bottom: 10px;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 600;
    color: var(--black);
    transition: background-color var(--transition), color var(--transition);
  }
  .sidebar nav a:hover,
  .sidebar nav a.active {
    background: var(--button-hover-bg);
    color: var(--black);
  }
  .sidebar nav .icon {
    font-size: 1.2rem;
  }
  main.main-content {
    flex-grow: 1;
    padding: 2rem;
    max-width: var(--max-width);
    box-sizing: border-box;
  }
  h1 {
    margin-bottom: 2rem;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
  }
  th, td {
    border: 1px solid var(--gray);
    padding: 0.75rem 1rem;
    text-align: left;
  }
  th {
    background: var(--button-hover-bg);
    color: var(--black);
  }
  td img {
    width: 80px;
    height: 50px;
    object-fit: cover;
    border-radius: var(--radius);
  }
  .actions a {
    margin-right: 0.8rem;
    text-decoration: none;
    font-weight: 600;
    color: var(--button-hover-bg);
    transition: color 0.3s ease;
  }
  .actions a:hover {
    color: #a56f6f;
  }
  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    gap: 0.7rem;
  }
  .pagination a {
    padding: 0.5rem 0.9rem;
    border-radius: var(--radius);
    text-decoration: none;
    background: var(--gray);
    color: var(--black);
    font-weight: 600;
    transition: background-color var(--transition);
  }
  .pagination a.active,
  .pagination a:hover {
    background: var(--button-hover-bg);
    color: var(--black);
  }
</style>
</head>
<body>
  <aside class="sidebar">
    <a href="../index.php" class="logo">Blogg</a>
    <nav>
      <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <a href="manage-posts.php" class="active"><span class="icon">📝</span> Manage Posts</a>
      <a href="manage-users.php"><span class="icon">👤</span> Manage Users</a>
      <a href="categories.php"><span class="icon">📂</span> Categories</a>
      <a href="manage-comments.php"><span class="icon">💬</span> Comments</a>
      <a href="analytics-blog.php"><span class="icon">📈</span> Analytics</a>
      <a href="settings.php"><span class="icon">⚙️</span> Settings</a>
      <a href="logout.php"><span class="icon">🔓</span> Logout</a>
    </nav>
  </aside>

  <main class="main-content">
    <h1>Manage Posts</h1>

    <?php if ($posts->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Title</th>
          <th>Excerpt</th>
          <th>Author</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($post = $posts->fetch_assoc()): ?>
        <tr>
          <td><?= $post['id'] ?></td>
          <td><img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>"></td>
          <td><?= htmlspecialchars($post['title']) ?></td>
          <td><?= htmlspecialchars($post['excerpt']) ?>...</td>
          <td><?= htmlspecialchars($post['author']) ?></td>
          <td><?= date("M d, Y H:i", strtotime($post['created_at'])) ?></td>
          <td class="actions">
            <a href="edit-post.php?id=<?= $post['id'] ?>">Edit</a>
            <a href="delete-post.php?id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?');" style="color:red;">Delete</a>
            <br><a href="add-post.php"><span class="icon"></span> Add New Post</a>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
      <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?>">&laquo; Prev</a>
      <?php endif; ?>

      <?php for ($page = 1; $page <= $totalPages; $page++): ?>
        <a href="?page=<?= $page ?>" class="<?= $page == $currentPage ? 'active' : '' ?>"><?= $page ?></a>
      <?php endfor; ?>

      <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?= $currentPage + 1 ?>">Next &raquo;</a>
      <?php endif; ?>
    </div>

    <?php else: ?>
      <p>No posts found.</p>
    <?php endif; ?>
  </main>
</body>
</html>
