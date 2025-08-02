<?php
require_once 'php/config.php'; // Your DB connection using mysqli

// Validate blog ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid blog ID.");
}

$id = intval($_GET['id']);

// Prepare and execute query to fetch post and category name
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.status = 'published'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Blog not found.");
}

$post = $result->fetch_assoc();
$stmt->close();

// Update views count if possible
$updateStmt = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
if ($updateStmt) {
    $updateStmt->bind_param("i", $id);
    $updateStmt->execute();
    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($post['title'] ?? 'Blog Post') ?> - MyBlog</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .blog-detail {
        max-width: 800px;
        margin: 100px auto 50px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 12px;
        font-family: 'Merriweather', serif;
    }
    .blog-detail h1 { font-size: 2rem; margin-bottom: 10px; }
    .meta { color: gray; font-size: 0.9rem; margin-bottom: 20px; }
    .blog-detail img { max-width: 100%; border-radius: 10px; margin: 20px 0; }
    .content { line-height: 1.7; font-size: 1rem; }
    .back-link { display: inline-block; margin-top: 20px; color: #cb9191; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <main class="blog-detail">
    <article>
      <h1><?= htmlspecialchars($post['title']) ?></h1>
      <p class="meta">
        By <?= htmlspecialchars($post['author'] ?? 'Unknown Author') ?> | 
        <?= date('M d, Y', strtotime($post['created_at'])) ?> | 
        Category: <?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?>
      </p>
      <?php if (!empty($post['image'])): ?>
        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" 
             alt="<?= htmlspecialchars($post['title']) ?>">
      <?php endif; ?>
      <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
      <a href="index.php" class="back-link">&larr; Back to Home</a>
    </article>
  </main>

</body>
</html>
