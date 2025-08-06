<?php
session_start();
require_once '../php/config.php';

// Total posts
$totalPosts = $conn->query("SELECT COUNT(*) AS count FROM posts")->fetch_assoc()['count'];

// Total users
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

// Total comments
$totalComments = $conn->query("SELECT COUNT(*) AS count FROM comments")->fetch_assoc()['count'];

// Total likes
$totalLikes = $conn->query("SELECT COUNT(*) AS count FROM likes")->fetch_assoc()['count'];

// Total views (sum of views column in posts)
$totalViews = $conn->query("SELECT SUM(views) AS total FROM posts")->fetch_assoc()['total'] ?? 0;

// Top 5 popular posts by views
$popularPosts = $conn->query("SELECT id, title, views FROM posts ORDER BY views DESC LIMIT 5");

// Top 5 active users by number of posts
$activeUsers = $conn->query("
    SELECT u.id, u.username, COUNT(p.id) AS post_count 
    FROM users u 
    LEFT JOIN posts p ON u.id = p.user_id 
    GROUP BY u.id 
    ORDER BY post_count DESC 
    LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Analytics Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f9f9f9;
            color: #333;
        }
        h1 {
            color: #cb9191;
            text-align: center;
            margin-bottom: 30px;
        }
        .metrics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }
        .metric-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 18%;
            text-align: center;
        }
        .metric-box h2 {
            font-size: 2.5rem;
            margin: 0;
            color: #cb9191;
        }
        .metric-box p {
            margin: 5px 0 0;
            font-weight: bold;
            font-size: 1.1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #cb9191;
            color: black;
        }
        tbody tr:hover {
            background: #f0f0f0;
        }
        .section-title {
            margin-top: 40px;
            font-size: 1.5rem;
            color: #cb9191;
        }
    </style>
</head>
<body>

    <h1>Admin Analytics Dashboard</h1>

    <div class="metrics">
        <div class="metric-box">
            <h2><?= $totalPosts ?></h2>
            <p>Total Posts</p>
        </div>
        <div class="metric-box">
            <h2><?= $totalUsers ?></h2>
            <p>Total Users</p>
        </div>
        <div class="metric-box">
            <h2><?= $totalComments ?></h2>
            <p>Total Comments</p>
        </div>
        <div class="metric-box">
            <h2><?= $totalLikes ?></h2>
            <p>Total Likes</p>
        </div>
        <div class="metric-box">
            <h2><?= $totalViews ?></h2>
            <p>Total Views</p>
        </div>
    </div>

    <div>
        <h2 class="section-title">Top 5 Popular Posts (by Views)</h2>
        <table>
            <thead>
                <tr><th>Post ID</th><th>Title</th><th>Views</th></tr>
            </thead>
            <tbody>
                <?php while($post = $popularPosts->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['id']) ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars($post['views']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div>
        <h2 class="section-title">Top 5 Active Users (by Posts)</h2>
        <table>
            <thead>
                <tr><th>User ID</th><th>Username</th><th>Number of Posts</th></tr>
            </thead>
            <tbody>
                <?php while($user = $activeUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['post_count']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
