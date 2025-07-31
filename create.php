<?php
// Include DB connection
require_once 'db.php';

// Initialize variables and errors
$title = $author = $category = $imageType = $imageURL = $excerpt = $content = "";
$titleErr = $authorErr = $categoryErr = $imageErr = $excerptErr = $contentErr = "";
$successMsg = "";
$hasError = false;

// Function to sanitize input
function clean_input($data) {
    return htmlspecialchars(trim($data));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate title
    if (empty($_POST['title'])) {
        $titleErr = "Blog title is required.";
        $hasError = true;
    } else {
        $title = clean_input($_POST['title']);
    }

    // Validate author
    if (empty($_POST['author'])) {
        $authorErr = "Author name is required.";
        $hasError = true;
    } else {
        $author = clean_input($_POST['author']);
    }

    // Validate category
    if (empty($_POST['category'])) {
        $categoryErr = "Please select a category.";
        $hasError = true;
    } else {
        $category = clean_input($_POST['category']);
    }

    // Validate image type and image input
    $imageType = isset($_POST['image_type']) ? $_POST['image_type'] : 'url';

    if ($imageType === 'url') {
        if (empty($_POST['image_url'])) {
            $imageErr = "Image URL is required.";
            $hasError = true;
        } else {
            $imageURL = filter_var(trim($_POST['image_url']), FILTER_SANITIZE_URL);
            if (!filter_var($imageURL, FILTER_VALIDATE_URL)) {
                $imageErr = "Invalid image URL.";
                $hasError = true;
            }
        }
    } elseif ($imageType === 'file') {
        if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $imageErr = "Please upload an image file.";
            $hasError = true;
        } else {
            // Validate file type (basic check)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $_FILES['image_file']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                $imageErr = "Only JPG, PNG, GIF, and WEBP images are allowed.";
                $hasError = true;
            }
        }
    } else {
        $imageErr = "Invalid image type selection.";
        $hasError = true;
    }

    // Validate excerpt
    if (empty($_POST['excerpt'])) {
        $excerptErr = "Excerpt is required.";
        $hasError = true;
    } else {
        $excerpt = clean_input($_POST['excerpt']);
    }

    // Validate content
    if (empty($_POST['content'])) {
        $contentErr = "Content is required.";
        $hasError = true;
    } else {
        $content = clean_input($_POST['content']);
    }

    // If no errors, process form
    if (!$hasError) {
        // Process image upload if file
        if ($imageType === 'file') {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = basename($_FILES['image_file']['name']);
            $safeFileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
            $targetFilePath = $uploadDir . time() . '_' . $safeFileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $imageURL = $targetFilePath;
            } else {
                $imageErr = "Error uploading the image file.";
                $hasError = true;
            }
        }
    }

    // Insert into database if still no errors
    if (!$hasError) {
        // Find category id from categories table
        $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmtCat->execute([$category]);
        $catRow = $stmtCat->fetch();

        if (!$catRow) {
            $categoryErr = "Selected category is invalid.";
        } else {
            $category_id = $catRow['id'];

            // Insert post into posts table
            $sql = "INSERT INTO posts (user_id, title, content, image, category_id, status, created_at, updated_at)
                    VALUES (:user_id, :title, :content, :image, :category_id, 'published', NOW(), NOW())";

            $stmt = $pdo->prepare($sql);

            // For now, no logged-in user, so null user_id
            $user_id = null;

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_NULL);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':image', $imageURL);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $successMsg = "Blog post created successfully!";

                // Clear form data
                $title = $author = $category = $imageType = $imageURL = $excerpt = $content = "";
            } else {
                $imageErr = "Failed to save blog post to the database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Blog - MyBlog</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    /* Your existing styles here (same as your provided CSS) */
    /* ... */
    body {
      font-family: 'Merriweather', serif;
      margin: 0;
      background: #fff;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 12px;
      background: #f9ecec;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    h1 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: bold;
      margin: 1rem 0 0.3rem;
    }

    input[type="text"],
    select,
    textarea,
    input[type="file"] {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      border-radius: 10px;
      border: 1px solid #ccc;
      resize: vertical;
    }

    button {
      margin-top: 1.5rem;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border: none;
      border-radius: 25px;
      background: #c8d9eb;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #f0d9da;
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .error {
      color: red;
      font-size: 0.9rem;
      margin-top: 0.3rem;
    }

    .success-msg {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      font-weight: 600;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Create New Blog</h1>

    <?php if ($successMsg): ?>
      <div class="success-msg" role="alert"><?= $successMsg ?></div>
    <?php endif; ?>

    <form id="blogForm" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data" novalidate>
      <label for="title">Blog Title</label>
      <input type="text" name="title" id="title" required value="<?= htmlspecialchars($title) ?>" />
      <?php if ($titleErr): ?><div class="error"><?= $titleErr ?></div><?php endif; ?>

      <label for="author">Author Name</label>
      <input type="text" name="author" id="author" required value="<?= htmlspecialchars($author) ?>" />
      <?php if ($authorErr): ?><div class="error"><?= $authorErr ?></div><?php endif; ?>

      <label for="category">Category</label>
      <select name="category" id="category" required>
        <option value="">--Select--</option>
        <?php
        $categories = ['Travel', 'Food', 'Technology', 'Health', 'Education'];
        foreach ($categories as $cat) {
            $selected = ($category === $cat) ? "selected" : "";
            echo "<option value=\"$cat\" $selected>$cat</option>";
        }
        ?>
      </select>
      <?php if ($categoryErr): ?><div class="error"><?= $categoryErr ?></div><?php endif; ?>

      <label for="imageType">Image Source</label>
      <select id="imageType" name="image_type">
        <option value="url" <?= ($imageType === 'url' || !$imageType) ? "selected" : "" ?>>Use Image URL</option>
        <option value="file" <?= ($imageType === 'file') ? "selected" : "" ?>>Upload from Device</option>
      </select>

      <div id="imageURLInput" style="display: <?= ($imageType === 'file') ? 'none' : 'block' ?>;">
        <label for="imageURL">Image URL</label>
        <input type="text" name="image_url" id="imageURL" value="<?= htmlspecialchars($imageURL) ?>" />
      </div>

      <div id="imageFileInput" style="display: <?= ($imageType === 'file') ? 'block' : 'none' ?>;">
        <label for="imageFile">Choose Image File</label>
        <input type="file" name="image_file" id="imageFile" accept="image/*" />
      </div>
      <?php if ($imageErr): ?><div class="error"><?= $imageErr ?></div><?php endif; ?>

      <label for="excerpt">Short Excerpt</label>
      <textarea name="excerpt" id="excerpt" rows="3" required><?= htmlspecialchars($excerpt) ?></textarea>
      <?php if ($excerptErr): ?><div class="error"><?= $excerptErr ?></div><?php endif; ?>

      <label for="content">Full Content</label>
      <textarea name="content" id="content" rows="6" required><?= htmlspecialchars($content) ?></textarea>
      <?php if ($contentErr): ?><div class="error"><?= $contentErr ?></div><?php endif; ?>

      <div class="form-actions">
        <button type="submit">Post Blog</button>
      </div>
    </form>
  </div>

  <script>
    const imageType = document.getElementById('imageType');
    const imageURLInput = document.getElementById('imageURLInput');
    const imageFileInput = document.getElementById('imageFileInput');

    imageType.addEventListener('change', function () {
      if (imageType.value === 'url') {
        imageURLInput.style.display = 'block';
        imageFileInput.style.display = 'none';
      } else {
        imageURLInput.style.display = 'none';
        imageFileInput.style.display = 'block';
      }
    });
  </script>
</body>
</html>
