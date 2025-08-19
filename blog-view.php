<?php
session_start();
require_once 'php/config.php';

// Validate blog ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid blog ID.");
}

$id = intval($_GET['id']);

// Fetch post & category
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name, 
       IFNULL(u.username, 'Admin') AS author
FROM posts p 
LEFT JOIN categories c ON p.category_id = c.id 
LEFT JOIN users u ON p.user_id = u.id
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

// Update views count
$updateStmt = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$updateStmt->bind_param("i", $id);
$updateStmt->execute();
$updateStmt->close();

// Likes count
$likeCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id = ?");
$likeCountStmt->bind_param("i", $id);
$likeCountStmt->execute();
$likeResult = $likeCountStmt->get_result();
$likeCount = $likeResult->fetch_assoc()['total'] ?? 0;
$likeCountStmt->close();

// User liked check
$userLiked = false;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $checkStmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $checkStmt->bind_param("ii", $uid, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $userLiked = $checkResult->num_rows > 0;
    $checkStmt->close();
}

// Fetch usernames of likers (limit 5)
$likedUsers = [];
$usersStmt = $conn->prepare("
    SELECT u.username 
    FROM likes l
    JOIN users u ON l.user_id = u.id
    WHERE l.post_id = ?
    LIMIT 5
");
$usersStmt->bind_param("i", $id);
$usersStmt->execute();
$usersResult = $usersStmt->get_result();
while ($row = $usersResult->fetch_assoc()) {
    $likedUsers[] = $row['username'];
}
$usersStmt->close();

// Fetch comments for this post
$comments = [];
$commentStmt = $conn->prepare("
    SELECT c.*, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ? AND c.is_hidden = 0
    ORDER BY c.created_at DESC
");
$commentStmt->bind_param("i", $id);
$commentStmt->execute();
$commentsResult = $commentStmt->get_result();
while ($comment = $commentsResult->fetch_assoc()) {
    $comments[] = $comment;
}
$commentStmt->close();

// Fetch average rating for this post
$ratingAvgStmt = $conn->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
    FROM ratings 
    WHERE post_id = ?
");
$ratingAvgStmt->bind_param("i", $id);
$ratingAvgStmt->execute();
$ratingAvgResult = $ratingAvgStmt->get_result()->fetch_assoc();
$avgVal = $ratingAvgResult['avg_rating'] ?? 0;
$averageRating = $avgVal ? round((float)$avgVal, 1) : 0;
$totalRatings = $ratingAvgResult['total_ratings'] ?? 0;
$ratingAvgStmt->close();

// Fetch user rating if logged in
$userRating = 0;
if (isset($_SESSION['user_id'])) {
    $userRatingStmt = $conn->prepare("
        SELECT rating FROM ratings WHERE user_id = ? AND post_id = ?
    ");
    $userRatingStmt->bind_param("ii", $_SESSION['user_id'], $id);
    $userRatingStmt->execute();
    $userRatingResult = $userRatingStmt->get_result();
    if ($userRatingResult->num_rows > 0) {
        $userRating = (int)$userRatingResult->fetch_assoc()['rating'];
    }
    $userRatingStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($post['title']) ?> - MyBlog</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --font-family: 'Merriweather', serif;
      --black: #000000;
      --white: #f0f0f0;
      --gray: #dddddd;
      --button-hover-bg: #cb9191;
      --radius: 12px;
      --transition: 0.3s ease;
    }

    body {
      font-family: var(--font-family);
      background-color: var(--white);
      color: var(--black);
      margin: 0;
      padding: 0;
    }

    .blog-detail {
      max-width: 800px;
      margin: 100px auto 50px;
      padding: 20px;
      background: var(--white);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-radius: var(--radius);
    }

    .blog-detail h1 {
      font-size: 2rem;
      margin-bottom: 10px;
    }

    .meta {
      color: gray;
      font-size: 0.9rem;
      margin-bottom: 20px;
    }

    .blog-detail img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      border-radius: 10px;
      margin: 20px 0;
    }

    .content {
      line-height: 1.7;
      font-size: 1rem;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: var(--button-hover-bg);
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }

    .like-btn {
      background-color: var(--button-hover-bg);
      color: var(--black);
      border: none;
      padding: 10px 15px;
      border-radius: var(--radius);
      cursor: pointer;
      font-weight: 600;
      transition: background-color var(--transition);
    }
    .like-btn:hover { background-color: #b37070; }
    .like-btn.liked { background-color: #333; color: white; }

    .liked-users {
      font-size: 0.9rem;
      color: gray;
      margin-top: 8px;
    }

    .comment-section { margin-top: 40px; }
    .comment { border-bottom: 1px solid #eee; padding: 10px 0; }
    .comment .username { font-weight: bold; }

    textarea {
      width: 100%;
      padding: 10px;
      border-radius: var(--radius);
      border: 1px solid var(--gray);
      margin-bottom: 10px;
      font-family: var(--font-family);
      font-size: 1rem;
    }

    button[type="submit"] {
      background-color: var(--button-hover-bg);
      border: none;
      padding: 8px 16px;
      border-radius: var(--radius);
      font-weight: 600;
      cursor: pointer;
      transition: background-color var(--transition);
    }
    button[type="submit"]:hover { background-color: #b37070; }

    .rating-stars { cursor: pointer; font-size: 1.4rem; color: gold; }
    .rating-stars .star { padding: 0 3px; }
    .star.filled { color: orange; }

    @media (max-width: 768px) {
      .blog-detail { margin: 80px 15px 30px; padding: 15px; }
      .blog-detail img { height: 250px; }
    }
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
      <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
    <?php endif; ?>
    <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

    <!-- Like Button -->
<!-- Like Button -->
<button id="likeBtn" class="like-btn <?= isset($_SESSION['user_id']) && $userLiked ? 'liked' : '' ?>">
  <?= isset($_SESSION['user_id']) && $userLiked ? 'Unlike' : 'Like' ?> (<span id="likeCount"><?= $likeCount ?></span>)
</button>

<?php if (!empty($likedUsers)): ?>
  <p class="liked-users">❤️ Liked by <?= implode(", ", $likedUsers) ?><?= ($likeCount > count($likedUsers)) ? " and others" : "" ?></p>
<?php endif; ?>

    <!-- Rating Section -->
    <div class="rating-section" style="margin-top: 30px;">
  <strong>Average Rating:</strong> <span id="avgRating"><?= $averageRating ?></span> / 5 (<?= $totalRatings ?> ratings)
  <br>
  <div id="ratingStars" class="rating-stars" data-post-id="<?= $id ?>">
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <span class="star <?= ($i <= $userRating) ? 'filled' : '' ?>" data-value="<?= $i ?>">&#9733;</span>
    <?php endfor; ?>
  </div>
  <small>Click a star to rate</small>
</div>

    <!-- Comment Section -->
    <div class="comment-section">
  <h3>Comments (<?= count($comments) ?>)</h3>

  <!-- Comment Form (always visible) -->
  <form id="commentForm" method="POST" style="margin-bottom: 20px;" action="<?= isset($_SESSION['user_id']) ? 'submit_comment.php' : 'login.php' ?>">
    <textarea name="comment" id="commentText" rows="3" required placeholder="Write your comment here..."></textarea>
    <input type="hidden" name="post_id" value="<?= $id ?>">
    <button type="submit">Submit Comment</button>
  </form>

  <!-- Comment List -->
  <div id="commentsList">
    <?php foreach ($comments as $comment): ?>
      <div class="comment">
        <span class="username"><?= htmlspecialchars($comment['username']) ?></span>
        <small><?= date('M d, Y H:i', strtotime($comment['created_at'])) ?></small>
        <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>


    <a href="index.php" class="back-link">&larr; Back to Home</a>
  </article>
</main>

<script>
  document.getElementById('likeBtn').addEventListener('click', function () {
    <?php if (!isset($_SESSION['user_id'])): ?>
      // If not logged in, redirect to login
      window.location.href = 'login.php';
    <?php else: ?>
      // If logged in, send like request
      fetch('like_post.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'post_id=<?= $post['id'] ?>'
      })
      .then(response => response.text())
      .then(data => {
        // Optionally update like count or reload page
        location.reload();
      })
      .catch(error => console.error('Error:', error));
    <?php endif; ?>
  });
document.addEventListener("DOMContentLoaded", () => {
  const likeBtn = document.getElementById('likeBtn');
  const likeCountSpan = document.getElementById('likeCount');
  if(likeBtn) {
    likeBtn.addEventListener('click', () => {
      fetch('like.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          post_id: <?= $id ?>,
          action: likeBtn.classList.contains('liked') ? 'unlike' : 'like'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          likeBtn.classList.toggle('liked');
          likeBtn.textContent = (likeBtn.classList.contains('liked') ? 'Unlike' : 'Like') + ` (${data.likeCount})`;
          likeCountSpan.textContent = data.likeCount;
        } else {
          alert(data.message || 'Error processing like.');
        }
      });
    });
  }
  
//  rating php
document.querySelectorAll('#ratingStars .star').forEach(function(star) {
    star.addEventListener('click', function () {
      const value = this.getAttribute('data-value');
      const postId = document.getElementById('ratingStars').getAttribute('data-post-id');

      <?php if (!isset($_SESSION['user_id'])): ?>
        // Redirect to login if not logged in
        window.location.href = 'login.php';
      <?php else: ?>
        // Send AJAX request to submit rating
        fetch('rate_post.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'rating=' + value + '&post_id=' + postId
        })
        .then(response => response.text())
        .then(data => {
          // Optionally update UI
          location.reload(); // or update avgRating without reload
        })
        .catch(error => console.error('Error:', error));
      <?php endif; ?>
    });
  });
  //  php for comment 
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

  document.getElementById('commentForm').addEventListener('submit', function(e) {
    const commentText = document.getElementById('commentText').value.trim();

    if (commentText === '') {
      e.preventDefault();
      alert('Please write a comment before submitting.');
      return;
    }

    if (!isLoggedIn) {
      e.preventDefault();
      // Redirect to login page
      window.location.href = 'login.php';
    }
  });
  const ratingStars = document.getElementById('ratingStars');
  if (ratingStars) {
    ratingStars.addEventListener('click', (e) => {
      if (e.target.classList.contains('star')) {
        const ratingValue = e.target.getAttribute('data-value');
        fetch('rate.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            post_id: <?= $id ?>,
            rating: ratingValue
          })
        })
        .then(res => res.json())
        .then(data => {
          if(data.success){
            [...ratingStars.children].forEach((star, idx) => {
              star.classList.toggle('filled', idx < ratingValue);
            });
            document.getElementById('avgRating').textContent = data.averageRating;
          } else {
            alert(data.message || 'Failed to rate.');
          }
        });
      }
    });
  }

  const commentForm = document.getElementById('commentForm');
  if (commentForm) {
    commentForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const commentText = document.getElementById('commentText').value.trim();
      if(!commentText) return alert("Comment cannot be empty.");
      fetch('comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          post_id: <?= $id ?>,
          comment: commentText
        })
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
          const commentsList = document.getElementById('commentsList');
          const newComment = document.createElement('div');
          newComment.className = 'comment';
          newComment.innerHTML = `<span class="username">${data.username}</span> <small>${data.created_at}</small><p>${data.comment}</p>`;
          commentsList.prepend(newComment);
          commentForm.reset();
        } else {
          alert(data.message || 'Failed to submit comment.');
        }
      });
    });
  }
});
</script>
</body>
</html>
