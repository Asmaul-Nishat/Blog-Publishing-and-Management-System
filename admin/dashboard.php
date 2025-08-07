<?php
session_start();
include '../php/config.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ====== Widgets Data ======
$totalPosts = $conn->query("SELECT COUNT(*) AS count FROM posts")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$totalCategories = $conn->query("SELECT COUNT(*) AS count FROM categories")->fetch_assoc()['count'];
$totalComments = $conn->query("SELECT COUNT(*) AS count FROM comments")->fetch_assoc()['count'];

// ====== Recent Posts ======
$sqlRecent = "
    SELECT p.id, 
           p.title, 
           LEFT(p.content, 150) AS excerpt, 
           p.image, 
           p.created_at, 
           IFNULL(u.fullname, 'Admin') AS author
    FROM posts p
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
    LIMIT 6
";
$recentPosts = $conn->query($sqlRecent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Blogg</title>
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
    color: var(--black);
    background-color: var(--white);
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
  }
  /* Sidebar */
  .sidebar {
    width: 230px;
    background: var(--white);
    border-right: 1px solid var(--gray);
    padding: 20px;
    box-sizing: border-box;
    transition: transform 0.3s ease;
  }
  .sidebar.hide {
    transform: translateX(-250px);
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

  /* Hamburger Button */
  #sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    font-size: 1.8rem;
    background: var(--button-hover-bg);
    border: none;
    color: var(--black);
    padding: 8px 12px;
    border-radius: var(--radius);
    cursor: pointer;
    z-index: 1100;
    transition: background-color var(--transition);
  }
  #sidebar-toggle:hover {
    background-color: #a56f6f;
  }

  main.main-content {
    flex-grow: 1;
    padding: 2rem;
    max-width: var(--max-width);
    box-sizing: border-box;
    transition: margin-left 0.3s ease;
  }
  main.main-content.shifted {
    margin-left: 230px;
  }
  h1 {
    margin-bottom: 2rem;
  }

  /* Widgets */
  .dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
  }
  .widget {
    background: var(--gray);
    padding: 1.5rem;
    border-radius: var(--radius);
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    transition: transform var(--transition);
  }
  .widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  }
  .widget h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: var(--black);
  }
  .widget p {
    margin: 0.5rem 0 0;
    color: var(--label-text-default);
    font-weight: 600;
  }

  /* Blog Cards */
  .blog-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(280px,1fr));
    gap: 2rem;
  }
  .blog-card {
    background: var(--gray);
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    cursor: default;
    transition: transform var(--transition);
    position: relative;
  }
  .blog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }
  .blog-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
  }
  .blog-content {
    padding: 1rem 1.2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .blog-title {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--black);
  }
  .blog-title a {
    color: var(--black);
    text-decoration: none;
  }
  .blog-title a:hover {
    text-decoration: underline;
  }
  .blog-excerpt {
    font-size: 0.9rem;
    color: var(--label-text-default);
    flex-grow: 1;
  }
  .blog-meta {
    margin-top: 1rem;
    font-size: 0.8rem;
    color: var(--label-text-default);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.3rem;
  }

  /* Responsive */
  @media (max-width: 900px) {
    #sidebar-toggle {
      display: block;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1050;
      transform: translateX(-250px);
      box-shadow: 2px 0 12px rgba(0,0,0,0.15);
    }
    .sidebar.show {
      transform: translateX(0);
    }
    main.main-content {
      padding: 1rem;
      max-width: 100%;
      margin: 0;
    }
  }

  @media (max-width: 600px) {
    body {
      font-size: 14px;
    }
    .widget h3 {
      font-size: 1.5rem;
    }
    .blog-title {
      font-size: 1rem;
    }
    .blog-card img {
      height: 140px;
    }
  }

  @media (max-width: 400px) {
    body {
      font-size: 13px;
    }
    .widget h3 {
      font-size: 1.2rem;
    }
    .blog-title {
      font-size: 0.9rem;
    }
    .blog-card img {
      height: 120px;
    }
  }
</style>
</head>
<body>

  <!-- Hamburger button for small screens -->
  <button id="sidebar-toggle" aria-label="Toggle sidebar menu">☰</button>

  <aside class="sidebar">
    <a href="../index.php" class="logo">Blogg</a>
    <nav>
      <a href="dashboard.php" class="active"><span class="icon">🏠</span> Dashboard</a>
      <a href="manage-posts.php"><span class="icon">📝</span> Manage Posts</a>
      <a href="manage-users.php"><span class="icon">👤</span> Manage Users</a>
      <a href="categories.php"><span class="icon">📂</span> Categories</a>
      <a href="manage-comments.php"><span class="icon">💬</span> Comments</a>
      <a href="analytics-blog.php"><span class="icon">📈</span> Analytics</a>
      <a href="settings.php"><span class="icon">⚙️</span> Settings</a>
      <a href="logout.php"><span class="icon">🔓</span> Logout</a>
    </nav>
  </aside>

  <main class="main-content">
    <section id="dashboard" class="tab-content active">
      <h1>Dashboard Overview</h1>

      <!-- Widgets -->
      <div class="dashboard-widgets">
        <div class="widget"><h3><?= $totalPosts ?></h3><p>Total Posts</p></div>
        <div class="widget"><h3><?= $totalUsers ?></h3><p>Total Users</p></div>
        <div class="widget"><h3><?= $totalCategories ?></h3><p>Categories</p></div>
        <div class="widget"><h3><?= $totalComments ?></h3><p>Comments</p></div>
      </div>

      <!-- Recent Posts -->
      <div class="blog-cards">
        <?php if ($recentPosts->num_rows > 0): ?>
          <?php while ($row = $recentPosts->fetch_assoc()): ?>
            <article class="blog-card">
              <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" 
                   alt="<?= htmlspecialchars($row['title']) ?>">
              <div class="blog-content">
                <h2 class="blog-title">
                  <a href="../blog-view.php?id=<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['title']) ?>
                  </a>
                </h2>
                <p class="blog-excerpt"><?= htmlspecialchars($row['excerpt']) ?>...</p>
                <div class="blog-meta">
                  <span class="author"><?= htmlspecialchars($row['author']) ?></span>
                  <span class="upload-time">
                    <?= date("M d, Y H:i", strtotime($row['created_at'])) ?>
                  </span>
                </div>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No recent posts found.</p>
        <?php endif; ?>
      </div>

 <!-- See All Posts Button -->
  <div style="text-align: center; margin-top: 1.5rem;">
  <a href="manage-posts.php" 
     style="
       display: inline-block; 
       background-color: var(--button-hover-bg); 
       color: var(--black); 
       padding: 0.6rem 1.2rem; 
       border-radius: var(--radius);
       font-weight: 600;
       text-decoration: none;
       transition: background-color 0.3s ease;
     "
     onmouseover="this.style.backgroundColor='#a56f6f'"
     onmouseout="this.style.backgroundColor='var(--button-hover-bg)'"
     >
    See All Posts
     </a>
     </div>


    </section>
  </main>

<script>
  const sidebarToggleBtn = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('main.main-content');

  sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });

  // Optional: close sidebar when clicking outside on small screens
  mainContent.addEventListener('click', () => {
    if (window.innerWidth <= 900 && sidebar.classList.contains('show')) {
      sidebar.classList.remove('show');
    }
  });
</script>

</body>
</html>
