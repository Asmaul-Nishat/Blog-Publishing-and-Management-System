<?php
include 'php/config.php';  // Your DB connection file

// Fetch posts joined with category name
$sql = "SELECT p.id, p.title, p.content, p.image, c.name AS category_name, p.created_at
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Blog Home - MyBlog</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    /* (Your existing CSS here, same as provided in your question) */
    /* ... */
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
    .categories {
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
      padding: 0.5rem 1rem;
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
  <section class="hero" tabindex="0" role="banner" aria-label="Featured blog post">
    <img src="image/jess-bailey-cU7wLFRyWWw-unsplash.jpg" alt="Featured Blog" />
    <div class="hero-text" tabindex="0" role="link" aria-label="Explore the Wonders of the Ocean blog post">Explore the Wonders</div>
  </section>

  <section class="categories" aria-label="Blog categories">
    <button class="active" type="button" data-category="all">All</button>
    <button type="button" data-category="Travel">Travel</button>
    <button type="button" data-category="Food">Food</button>
    <button type="button" data-category="Technology">Technology</button>
    <button type="button" data-category="Health">Health</button>
    <button type="button" data-category="Education">Education</button>
  </section>

  <br />

  <section class="blog-feed" aria-label="Latest blog posts" id="blogFeed">
  <?php
  if ($result && $result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
      // Prepare excerpt (150 chars max)
      $excerpt = substr(strip_tags($post['content']), 0, 150);
      if (strlen($post['content']) > 150) $excerpt .= '...';

      // Determine image src
      $imageSrc = $post['image'] ?? '';
      if ($imageSrc) {
        // Check if image is URL or local path
        if (preg_match('/^https?:\/\//i', $imageSrc)) {
          $img = htmlspecialchars($imageSrc);
        } else {
          $img = 'uploads/' . htmlspecialchars($imageSrc);
        }
      } else {
        $img = 'image/default-blog.jpg'; // default image if none set
      }

      echo '<article class="blog-card" tabindex="0" role="article" aria-label="' . htmlspecialchars($post['title']) . '">';
      echo '<img src="' . $img . '" alt="Image for ' . htmlspecialchars($post['title']) . '" />';
      echo '<div class="blog-content">';
      echo '<h2 class="blog-title"><a href="blog.php?id=' . (int)$post['id'] . '" aria-label="Read full blog post: ' . htmlspecialchars($post['title']) . '">' . htmlspecialchars($post['title']) . '</a></h2>';
      echo '<p class="blog-excerpt">' . htmlspecialchars($excerpt) . '</p>';
      echo '<div class="blog-meta">';
      echo '<span class="author" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="Category: ' . htmlspecialchars($post['category_name'] ?? 'Uncategorized') . '">' . htmlspecialchars($post['category_name'] ?? 'Uncategorized') . '</span>';
      echo '<span class="upload-time">' . date('F j, Y, g:i a', strtotime($post['created_at'])) . '</span>';
      echo '</div></div></article>';
    }
  } else {
    echo '<p>No blogs found.</p>';
  }
  ?>
  </section>
</div>

<footer>
  <p>
    &copy; 2025 MyBlog. All rights reserved. &nbsp;|&nbsp;
    <a href="#">Privacy Policy</a>
  </p>
</footer>

</body>
</html>
