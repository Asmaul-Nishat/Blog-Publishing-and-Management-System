<?php
session_start();
require_once '../php/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["logo"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile);

        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'logo'");
        $dbPath = "uploads/" . $fileName;
        $stmt->bind_param("s", $dbPath);
        $stmt->execute();
    }

    $_SESSION['success'] = "Settings updated successfully!";
    header("Location: settings.php");
    exit();
}

// Fetch settings
$result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
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
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            max-width: 600px;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }
        .logo-preview {
            max-height: 80px;
            margin-top: 5px;
            display: block;
        }
        .toggle-switch { display: flex; align-items: center; }
        .toggle-switch input { width: auto; margin-right: 10px; }
        button {
            background: #cb9191;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
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
    <a href="manage-comments.php">💬 Comments</a>
    <a href="analytics-blog.php">📈 Analytics</a>
    <a href="settings.php" class="active">⚙️ Settings</a>
    <a href="logout.php">🔓 Logout</a>
  </nav>
</aside>

<main class="main-content">
    <h1>Settings</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-msg"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="site_title">Site Title</label>
            <input type="text" name="site_title" value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="site_description">Site Description</label>
            <textarea name="site_description"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="contact_email">Contact Email</label>
            <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="logo">Logo</label>
            <input type="file" name="logo">
            <?php if (!empty($settings['logo'])): ?>
                <img src="../<?= htmlspecialchars($settings['logo']) ?>" class="logo-preview">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="footer_text">Footer Text</label>
            <input type="text" name="footer_text" value="<?= htmlspecialchars($settings['footer_text'] ?? '') ?>">
        </div>

        <div class="form-group toggle-switch">
            <input type="checkbox" name="enable_comments" value="1" <?= (!empty($settings['enable_comments']) && $settings['enable_comments'] == '1') ? 'checked' : '' ?>>
            <label for="enable_comments">Enable Comments</label>
        </div>

        <div class="form-group">
            <label for="social_links">Social Links (JSON)</label>
            <textarea name="social_links"><?= htmlspecialchars($settings['social_links'] ?? '') ?></textarea>
        </div>

        <button type="submit">Save Settings</button>
    </form>
</main>

</body>
</html>
