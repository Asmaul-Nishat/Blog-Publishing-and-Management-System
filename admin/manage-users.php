<?php
session_start();
include '../php/config.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all users
$sqlUsers = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$resultUsers = $conn->query($sqlUsers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Users - Blogg Admin</title>
<style>
  :root {
    --font-family: 'Merriweather', serif;
    --black: #000;
    --white: #f0f0f0;
    --gray: #ddd;
    --button-hover-bg: #cb9191;
    --radius: 8px;
    --transition: 0.3s ease;
  }
  body {
    font-family: var(--font-family);
    margin: 0; 
    padding: 20px;
    background: var(--white);
    color: var(--black);
  }
  h1 {
    text-align: center;
    margin-bottom: 20px;
  }
  .table-container {
    width: 100%;
    overflow-x: auto;
    background: #fff;
    border-radius: var(--radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
  }
  th, td {
    padding: 12px;
    border: 1px solid var(--gray);
    text-align: left;
    font-size: 15px;
  }
  th {
    background: var(--button-hover-bg);
    color: #fff;
  }
  tr:hover {
    background-color: #eee;
  }
  .actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }
  .btn {
    padding: 6px 10px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: background-color var(--transition);
  }
  .btn-edit {
    background-color: #6b8e23;
    color: white;
  }
  .btn-edit:hover {
    background-color: #55771c;
  }
  .btn-delete {
    background-color: #d9534f;
    color: white;
  }
  .btn-delete:hover {
    background-color: #b52b27;
  }

  /* Responsive */
  @media (max-width: 768px) {
    body {
      padding: 10px;
    }
    table {
      font-size: 14px;
    }
    th, td {
      padding: 10px;
    }
  }
  @media (max-width: 480px) {
    h1 {
      font-size: 20px;
    }
    .btn {
      font-size: 12px;
      padding: 5px 8px;
    }
    .actions {
      flex-direction: column;
      gap: 4px;
    }
  }
</style>
</head>
<body>

<h1>Manage Users</h1>

<div class="table-container">
<?php if ($resultUsers->num_rows > 0): ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Role</th>
      <th>Registered On</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($user = $resultUsers->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($user['id']) ?></td>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= htmlspecialchars($user['email']) ?></td>
      <td><?= htmlspecialchars($user['role']) ?></td>
      <td><?= date("M d, Y", strtotime($user['created_at'])) ?></td>
      <td class="actions">
        <a class="btn btn-delete" href="delete-user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
<p style="text-align:center;">No users found.</p>
<?php endif; ?>
</div>

</body>
</html>
