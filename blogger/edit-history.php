<?php
session_start();
require_once '../php/config.php';

$comment_id = intval($_GET['id']);

// Fetch edit history
$stmt = $conn->prepare("
    SELECT ce.*, u.username 
    FROM comment_edits ce
    JOIN users u ON ce.edited_by = u.id
    WHERE ce.comment_id = ?
    ORDER BY ce.edited_at DESC
");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit History</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Merriweather', serif;
            background: #f0f0f0;
            color: #000;
        }
        h1 {
            text-align: center;
            color: #cb9191;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #cb9191;
            color: #000;
        }
        tr:hover td {
            background: #f8f8f8;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #cb9191;
            color: #000;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-back:hover {
            background: #d89b9b;
        }
    </style>
</head>
<body>

    <h1>Comment Edit History</h1>

    <table>
        <thead>
            <tr>
                <th>Old Comment</th>
                <th>New Comment</th>
                <th>Edited By</th>
                <th>Edited At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['old_comment']) ?></td>
                        <td><?= htmlspecialchars($row['new_comment']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['edited_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No edit history found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="manage-comments.php" class="btn-back">⬅ Back to Comments</a>

</body>
</html>
