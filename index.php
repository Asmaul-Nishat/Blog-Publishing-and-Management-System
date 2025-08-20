<?php
session_start();
include 'php/config.php';


$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

$filterCategory = $_GET['category'] ?? 'all';
$searchKeyword = $_GET['search'] ?? '';

$searchSQL = '';
$params = [];
$types = '';

if ($searchKeyword) {
    $searchSQL = " AND (p.title LIKE ? OR p.content LIKE ?)";
    $params[] = '%' . $searchKeyword . '%';
    $params[] = '%' . $searchKeyword . '%';
    $types .= 'ss';
}

if ($filterCategory !== 'all') {
    $stmtCat = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmtCat->bind_param("s", $filterCategory);
    $stmtCat->execute();
    $stmtCat->bind_result($category_id);
    if ($stmtCat->fetch()) {
        $stmtCat->close();
        $sql = "SELECT p.id, p.title, p.content, p.image, c.name AS category_name, p.created_at
                FROM posts p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.category_id = ?" . $searchSQL . "
                ORDER BY p.created_at DESC";
        $stmt = $conn->prepare($sql);
        $params = array_merge([$category_id], $params);
        $types = 'i' . $types;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = null;
        $stmtCat->close();
    }
} else {
    $sql = "SELECT p.id, p.title, p.content, p.image, c.name AS category_name, p.created_at
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published'" . $searchSQL . "
            ORDER BY p.created_at DESC";
    if ($searchKeyword) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Blog Home - MyBlog</title>
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
      flex-direction: column;
      align-items: center;
    }
    .container {
      max-width: var(--max-width);
      width: 90%;
      margin: 2rem auto;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 2rem;
      width: 100%;
      max-width: none;
      background-color: var(--white);
      box-shadow: var(--shadow);
      position: fixed;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--gray);
    }
    nav .logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: #cb9191;
      user-select: none;
      font-family: 'Merriweather', serif;
      cursor: pointer;
      text-decoration: none;
    }
    nav .nav-links {
      display: flex;
      gap: 2rem;
      font-size: 1rem;
      font-weight: 600;
    }
    nav .nav-links a {
      text-decoration: none;
      color: var(--black);
      transition: color 0.3s ease;
      padding: 0.3rem 0.5rem;
      border-radius: var(--radius);
    }
    nav .nav-links a:hover {
      color: #cb9191;
      background-color: rgba(203, 145, 145, 0.15);
    }
    .hero {
      width: 100%;
      margin-bottom: 2rem;
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      position: relative;
      margin-top: 70px; /* for fixed navbar space */
    }
    .hero img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      filter: brightness(85%);
      transition: filter 0.3s ease;
    }
    .hero:hover img {
      filter: brightness(100%);
    }
    .hero-text {
      position: absolute;
      bottom: 2rem;
      left: 2rem;
      color: var(--black);
      background: rgba(240, 240, 240, 0.85);
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      max-width: 600px;
      font-weight: 700;
      font-size: 2rem;
      cursor: pointer;
      user-select: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Search Form */
.search-form {
  margin-bottom: 1rem;
  display: flex;
  gap: 0.5rem; /* space between input and button */
}

.search-form input[type="text"] {
  flex: 1;
  padding: 0.7rem;
  border-radius: 12px;
  border: 1px solid #999;
  font-size: 0.85rem;
  margin: 0; /* remove vertical margin */
  box-sizing: border-box;
}

.search-form button {
  padding: 0.7rem 1rem; /* match input height */
  border-radius: 12px;
  border: none;
  background: #cb9191;
  color: #000;
  cursor: pointer;
  font-weight: 700;
  transition: background-color 0.3s ease;
}

.search-form button:hover {
  background-color: #b37070;
}



    .categories {
      margin-top: 2rem;
      margin-bottom: 2rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--gray);
      user-select: none;
      display: flex;
      gap: 0.7rem;
      flex-wrap: wrap;
    }
    .categories button {
      background: var(--white);
      border: 1px solid var(--gray);
      padding: 0.7rem 1rem;
      border-radius: 20px;
      cursor: pointer;
      font-weight: 600;
      color: var(--black);
      transition: background-color var(--transition), border-color var(--transition);
    }
    .categories button:hover,
    .categories button.active {
      background: var(--button-hover-bg);
      border-color: var(--button-hover-bg);
      color: var(--black);
    }
    .blog-feed {
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
   
    .post-actions {
      margin-top: 12px;
      display: flex;
      gap: 10px;
    }
    .post-actions a {
      text-decoration: none;
      padding: 6px 12px;
      border-radius: var(--radius);
      background-color: #cb9191;
      color: var(--black);
      font-weight: 600;
      font-size: 0.9rem;
      user-select: none;
      transition: background-color 0.3s ease;
    }
    .post-actions a:hover {
      background-color: #b37070;
      color: #000;
    }
    footer {
      width: 100%;
      background: var(--white);
      padding: 2rem 0;
      text-align: center;
      font-size: 0.9rem;
      color: var(--label-text-default);
      user-select: none;
      border-top: 1px solid var(--gray);
      margin-top: 3rem;
    }
    footer a {
      color: var(--black);
      text-decoration: none;
      font-weight: 600;
    }
    footer a:hover {
      color: var(--button-hover-bg);
      text-decoration: underline;
    }
    @media (max-width: 900px) {
      .blog-feed {
        grid-template-columns: 1fr;
      }
      .categories {
        justify-content: center;
      }
    }

    .btn-like { cursor: pointer; background: none; border: none; color: #cb9191; font-weight: 600; }
    .btn-like.liked { color: #333; }
  
.post-actions a,
.post-actions .btn-like {
  text-decoration: none;
  padding: 6px 12px;
  border-radius: var(--radius);
  background-color: #cb9191;
  color: var(--black);
  font-weight: 600;
  font-size: 0.9rem;
  user-select: none;
  transition: background-color 0.3s ease;
  cursor: pointer;
  border: none;
}
.post-actions a:hover,
.post-actions .btn-like:hover {
  background-color: #b37070;
  color: #000;
}

  </style>
</head>
<body>

<nav>
  <a href="index.php" class="logo">Blogg</a>
  <div class="nav-links">
    <a href="create.php">Write Blog</a>
    <a href="login.php">Login</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </div>
</nav>
 
<div class="container">
  
  <section class="hero">
    <img src="uploads/jess-bailey-cU7wLFRyWWw-unsplash.jpg" alt="Featured Blog" />
    <div class="hero-text">Explore the Wonders</div>
  </section>
  

<form method="GET" action="index.php" class="search-form">
  <input type="text" name="search" placeholder="Search blogs..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
  <button type="submit">Search</button>
</form>

  <section class="categories">
    <button class="active" data-category="all">All</button>
    <?php foreach ($categories as $cat): ?>
      <button data-category="<?= htmlspecialchars($cat['name']) ?>">
        <?= htmlspecialchars($cat['name']) ?>
      </button>
    <?php endforeach; ?>
  </section>

  <section class="blog-feed" id="blogFeed">
  <?php
  if ($result && $result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
      $excerpt = substr(strip_tags($post['content']), 0, 150);
      if (strlen($post['content']) > 150) $excerpt .= '...';
      $img = $post['image'] ? (preg_match('/^https?:\/\//i', $post['image']) ? htmlspecialchars($post['image']) : 'uploads/' . htmlspecialchars($post['image'])) : 'image/default-blog.jpg';

      $likesQuery = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE post_id = " . (int)$post['id']);
      $likesTotal = $likesQuery->fetch_assoc()['total'] ?? 0;

      $userLiked = false;
      if (isset($_SESSION['user_id'])) {
          $uid = $_SESSION['user_id'];
          $likedCheck = $conn->query("SELECT id FROM likes WHERE user_id = $uid AND post_id = " . (int)$post['id']);
          $userLiked = $likedCheck->num_rows > 0;
      }

      echo '<article class="blog-card">';
      echo '<img src="' . $img . '" alt="Image for ' . htmlspecialchars($post['title']) . '" />';
      echo '<div class="blog-content">';
      echo '<h2 class="blog-title"><a href="blog-view.php?id=' . (int)$post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h2>';
      echo '<p class="blog-excerpt">' . htmlspecialchars($excerpt) . '</p>';
      echo '<div class="blog-meta">';
      echo '<span class="author">' . htmlspecialchars($post['category_name'] ?? 'Uncategorized') . '</span>';
      echo '<span class="upload-time">' . date('F j, Y, g:i a', strtotime($post['created_at'])) . '</span>';
      echo '</div>';

      echo '<div class="post-actions">';
      if (isset($_SESSION['user_id'])) {
        echo '<button class="btn-like ' . ($userLiked ? 'liked' : '') . '" data-id="' . (int)$post['id'] . '">';
        echo ($userLiked ? '👎 Unlike' : '👍 Like') . ' (<span>' . $likesTotal . '</span>)</button>';
      } else {
        echo '<a href="login.php">👍 Like (' . $likesTotal . ')</a>';
      }
      echo '<a href="blog-view.php?id=' . (int)$post['id'] . '#comments" class="btn-comment">💬 Comment</a>';
      echo '<a href="#" onclick="sharePost(' . (int)$post['id'] . '); return false;" class="btn-share">🔗 Share</a>';
      echo '</div>';

      echo '</div>';
      echo '</article>';
    }
  } else {
    echo '<p>No blogs found.</p>';
  }
  ?>
  </section>
</div>

<footer>
  <p>&copy; 2025 Blogg. All rights reserved. | <a href="#">Privacy Policy</a></p>
</footer>

<script>
document.querySelectorAll('.categories button').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.categories button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const category = btn.getAttribute('data-category');
    window.location.href = category === 'all' ? 'index.php' : 'index.php?category=' + encodeURIComponent(category);
  });
});

document.querySelectorAll('.btn-like').forEach(button => {
  button.addEventListener('click', () => {
    const postId = button.getAttribute('data-id');
    const isLiked = button.classList.contains('liked');
    fetch('like.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ post_id: postId, action: isLiked ? 'unlike' : 'like' })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        button.classList.toggle('liked');
        button.innerHTML = (data.action === 'liked' ? '👎 Unlike' : '👍 Like') + ' (<span>' + data.likeCount + '</span>)';
      } else {
        alert(data.message || 'Error');
      }
    });
  });
});

function sharePost(postId) {
  const url = window.location.origin + '/blog-view.php?id=' + postId;
  if (navigator.share) {
    navigator.share({ title: 'Check out this blog post', url })
      .catch(console.error);
  } else {
    navigator.clipboard.writeText(url).then(() => alert('Post URL copied!'));
  }
}
</script>

</body>
</html>
