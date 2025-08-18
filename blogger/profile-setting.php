<?php
session_start();
include '../php/config.php';

// Ensure only blogger can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'blogger') {
    header("Location: ../login.php");
    exit;
}

// Fetch current user info
$userId = $_SESSION['user_id'];
$userSql = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userSql->bind_param("i", $userId);
$userSql->execute();
$userResult = $userSql->get_result();
$user = $userResult->fetch_assoc();

// Handle profile update
// Handle profile update
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $imgName = time() . '_' . $_FILES['profile_image']['name'];
        $imgTmp = $_FILES['profile_image']['tmp_name'];
        $imgPath = "../uploads/" . $imgName;
        move_uploaded_file($imgTmp, $imgPath);
    } else {
        $imgName = $user['profile_image'];
    }

    $updateSql = $conn->prepare("UPDATE users SET fullname=?, username=?, email=?, profile_image=? WHERE id=?");
    $updateSql->bind_param("ssssi", $fullname, $username, $email, $imgName, $userId);

    if ($updateSql->execute()) {
        $success = "Profile updated successfully!";
        // Refresh user data
        $user['fullname'] = $fullname;
        $user['username'] = $username;
        $user['email'] = $email;
        $user['profile_image'] = $imgName;
    } else {
        $error = "Failed to update profile.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile Settings - Blogg</title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
<style>
/* --- Use your dashboard CSS here --- */

  :root {
    --font-family: 'Merriweather', serif;
    --black: #000000;
    --white: #f0f0f0;
    --gray: #dddddd;
    --input-border: #fdfdfdff;
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
/* Profile form */
.profile-form {
    max-width: 600px;
    margin: 0 auto;
    background: #dbb8b8ff;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
}
.profile-form h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    
}
.profile-form label {
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 650;
}
.profile-form input[type="text"],
.profile-form input[type="email"],
.profile-form textarea {
    width: 100%;
    padding: 0.6rem 0.8rem;
    border: 1px solid var(--input-border);
    border-radius: var(--radius);
    margin-bottom: 1rem;
    box-sizing: border-box;
}
.profile-form textarea {
    resize: vertical;
}
.profile-form input[type="file"] {
    margin-bottom: 1rem;
}
.profile-form button {
    background: white;
    color: black;
    padding: 0.7rem 1.2rem;
    border: none;
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.profile-form button:hover {
    background: #c19494ff;
}
.success-msg {
    color: green;
    text-align: center;
    margin-bottom: 1rem;
}
.error-msg {
    color: red;
    text-align: center;
    margin-bottom: 1rem;
}
.profile-img {
    display: block;
    max-width: 150px;
    margin: 0 auto 1rem auto;
    border-radius: 50%;
    object-fit: cover;
}
</style>
</head>
<body>

<button id="sidebar-toggle" aria-label="Toggle sidebar menu">☰</button>

<aside class="sidebar">
    <a href="../index.php" class="logo">Blogg</a>
    <nav>
      <a href="blogger-dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <a href="manage-posts.php"><span class="icon">📝</span> Manage Posts</a>
      <a href="manage-comments.php"><span class="icon">💬</span> Comments</a>
      <a href="profile-setting.php" class="active"><span class="icon">⚙️</span> Profile Settings</a>
      <a href="logout.php"><span class="icon">🔓</span> Logout</a>
    </nav>
</aside>

<main class="main-content">
    <section id="profile-setting">
        <h1>Profile Settings</h1>

        <form class="profile-form" action="" method="POST" enctype="multipart/form-data">
            <?php if($success): ?><p class="success-msg"><?= $success ?></p><?php endif; ?>
            <?php if($error): ?><p class="error-msg"><?= $error ?></p><?php endif; ?>

            <?php if($user['profile_image']): ?>
                <img src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-img">
            <?php endif; ?>

            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="profile_image">Profile Image</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>
    </section>
</main>

<script>
const sidebarToggleBtn = document.getElementById('sidebar-toggle');
const sidebar = document.querySelector('.sidebar');
sidebarToggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
</script>

</body>
</html>
