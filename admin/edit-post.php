<?php
session_start();
include '../php/config.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-posts.php");
    exit;
}

$postId = (int)$_GET['id'];

// Fetch post data
$postResult = $conn->query("SELECT * FROM posts WHERE id = $postId");
if ($postResult->num_rows !== 1) {
    header("Location: manage-posts.php");
    exit;
}
$post = $postResult->fetch_assoc();

// Fetch categories
$categoriesResult = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categoryId = (int)($_POST['category'] ?? 0);

    // Validate
    if ($title === '') {
        $errors[] = "Title cannot be empty.";
    }
    if ($content === '') {
        $errors[] = "Content cannot be empty.";
    }
    if ($categoryId <= 0) {
        $errors[] = "Please select a category.";
    }

    // Handle image upload if any
    $imageName = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('post_') . '.' . $ext;
            $uploadDir = '../uploads/';
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $errors[] = "Failed to upload image.";
            } else {
                // Optionally delete old image file here if you want
                if ($post['image'] && file_exists($uploadDir . $post['image'])) {
                    @unlink($uploadDir . $post['image']);
                }
            }
        }
    }

    if (empty($errors)) {
        // Update post
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $title, $content, $categoryId, $imageName, $postId);
        if ($stmt->execute()) {
            $success = true;
            // Refresh post data
            $postResult = $conn->query("SELECT * FROM posts WHERE id = $postId");
            $post = $postResult->fetch_assoc();
        } else {
            $errors[] = "Failed to update the post. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Post - Blogg Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
<style>
  /* (Use your existing CSS styles from previous pages here for consistency) */
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
  form {
    max-width: 700px;
  }
  label {
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
    color: var(--black);
  }
  input[type="text"],
  select,
  textarea {
    width: 100%;
    padding: 0.5rem 0.7rem;
    border: 1px solid var(--input-border);
    border-radius: var(--radius);
    margin-bottom: 1.2rem;
    font-family: var(--font-family);
    font-size: 1rem;
  }
  textarea {
    min-height: 160px;
    resize: vertical;
  }
  input[type="file"] {
    margin-bottom: 1.2rem;
  }
  button {
    background: var(--button-hover-bg);
    border: none;
    padding: 0.7rem 1.4rem;
    border-radius: var(--radius);
    color: var(--black);
    font-weight: 700;
    cursor: pointer;
    transition: background-color var(--transition);
  }
  button:hover {
    background-color: #a56f6f;
  }
  .error {
    color: var(--error-text);
    margin-bottom: 1rem;
  }
  .success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 0.7rem 1rem;
    border-radius: var(--radius);
    margin-bottom: 1rem;
  }
  img.current-image {
    max-width: 250px;
    margin-bottom: 1rem;
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    <h1>Edit Post</h1>

    <?php if (!empty($errors)): ?>
      <div class="error"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success">Post updated successfully!</div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
      <label for="title">Title</label>
      <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>

      <label for="content">Content</label>
      <textarea name="content" id="content" required><?= htmlspecialchars($post['content']) ?></textarea>

      <label for="category">Category</label>
      <select name="category" id="category" required>
        <option value="">-- Select Category --</option>
        <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
          <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $post['category_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>

      <label>Current Image</label>
      <?php if ($post['image'] && file_exists('../uploads/' . $post['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Current Image" class="current-image" />
      <?php else: ?>
        <p>No image uploaded.</p>
      <?php endif; ?>

      <label for="image">Change Image (optional)</label>
      <input type="file" name="image" id="image" accept="image/*">

      <button type="submit">Update Post</button>
    </form>
  </main>

</body>
</html>
