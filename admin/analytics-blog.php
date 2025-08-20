<?php
session_start();
require_once '../php/config.php';

// Fetch Top 5 Posts by Views
$sqlViews = "SELECT id, title, views FROM posts ORDER BY views DESC LIMIT 5";
$resultViews = $conn->query($sqlViews);


$sqlLikes = "SELECT posts.id, posts.title, COUNT(likes.id) AS total_likes
             FROM posts
             LEFT JOIN likes ON posts.id = likes.post_id
             GROUP BY posts.id
             ORDER BY total_likes DESC
             LIMIT 5";
$resultLikes = $conn->query($sqlLikes);


$sqlComments = "SELECT posts.id, posts.title, COUNT(comments.id) AS total_comments
                FROM posts
                LEFT JOIN comments ON posts.id = comments.post_id
                GROUP BY posts.id
                ORDER BY total_comments DESC
                LIMIT 5";
$resultComments = $conn->query($sqlComments);


$sqlRatings = "SELECT posts.id, posts.title, AVG(ratings.rating) AS avg_rating
               FROM posts
               LEFT JOIN ratings ON posts.id = ratings.post_id
               GROUP BY posts.id
               ORDER BY avg_rating DESC
               LIMIT 5";
$resultRatings = $conn->query($sqlRatings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Analytics</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Merriweather&display=swap');

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
      min-height: 100vh;
    }
    header.main-header {
      background: #cb9191;
      padding: 15px 20px;
      border-radius: 10px;
      color: #000;
      font-weight: 700;
      font-size: 1.5rem;
      margin-bottom: 20px;
    }
    /* Tables and typography */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #cb9191;
      color: #000;
    }
    caption {
      caption-side: top;
      font-size: 1.2em;
      font-weight: bold;
      margin: 15px 0;
      color: #cb9191;
    }
    #analytics-date {
      display: block;
      margin: 0 auto 30px auto;
      font-size: 16px;
      padding: 8px 10px;
      width: 200px;
      border: 1px solid #ccc;
      border-radius: 4px;
      text-align: center;
    }
    #chart-container {
      width: 80%;
      max-width: 700px;
      margin: 0 auto 50px auto;
      background: white;
      padding: 20px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      border-radius: 8px;
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
    <a href="analytics-blog.php" class="active">📈 Analytics</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="logout.php">🔓 Logout</a>
  </nav>
</aside>

<div class="main-content">
  <header class="main-header">Admin Analytics Dashboard</header>

  <!-- Date Picker for Daily Analytics -->
  <label for="analytics-date" style="display:block; text-align:center; margin-bottom:8px; font-weight:600;">
    Select Date for Daily Analytics
  </label>
  <input type="date" id="analytics-date" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" />

  <div id="chart-container">
    <canvas id="dailyChart"></canvas>
  </div>

  <!-- Top 5 Posts by Views -->
  <table>
    <caption>Top 5 Posts by Views</caption>
    <thead>
      <tr><th>Post ID</th><th>Title</th><th>Views</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $resultViews->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['views']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Top 5 Posts by Likes -->
  <table>
    <caption>Top 5 Posts by Likes</caption>
    <thead>
      <tr><th>Post ID</th><th>Title</th><th>Likes</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $resultLikes->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['total_likes']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Top 5 Posts by Comments -->
  <table>
    <caption>Top 5 Posts by Comments</caption>
    <thead>
      <tr><th>Post ID</th><th>Title</th><th>Comments</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $resultComments->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['total_comments']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Top 5 Posts by Average Ratings -->
  <table>
    <caption>Top 5 Posts by Average Ratings</caption>
    <thead>
      <tr><th>Post ID</th><th>Title</th><th>Average Rating</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $resultRatings->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= number_format((float)$row['avg_rating'], 2) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('dailyChart').getContext('2d');
  let dailyChart;

  async function fetchDailyAnalytics(date) {
    const response = await fetch(`fetch_daily_analytics.php?date=${date}`);
    const data = await response.json();
    return data;
  }

  function renderChart(data, date) {
    if (dailyChart) dailyChart.destroy();

    dailyChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Views', 'Likes', 'Comments'],
        datasets: [{
          label: `Analytics for ${date}`,
          data: [data.views, data.likes, data.comments],
          backgroundColor: ['#d0a3a3ff', '#e2aeaeff', '#cb9191'],
          borderRadius: 5,
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  }

  const dateInput = document.getElementById('analytics-date');

  // Initial chart on page load
  fetchDailyAnalytics(dateInput.value).then(data => renderChart(data, dateInput.value));

  // On date change update chart
  dateInput.addEventListener('change', () => {
    const selectedDate = dateInput.value;
    fetchDailyAnalytics(selectedDate).then(data => renderChart(data, selectedDate));
  });
</script>

</body>
</html>
