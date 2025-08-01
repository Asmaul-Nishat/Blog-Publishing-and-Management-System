<?php
session_start();

// --- DB Connection ---
// config.php without echo debug lines
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'new-blog-database';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables and error messages
$name = $email = $subject = $message = "";
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$successMsg = "";
$hasError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate name
    if (empty(trim($_POST["name"]))) {
        $nameErr = "Full Name is required.";
        $hasError = true;
    } else {
        $name = htmlspecialchars(trim($_POST["name"]));
    }

    // Sanitize and validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Email Address is required.";
        $hasError = true;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format.";
        $hasError = true;
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Sanitize and validate subject
    if (empty(trim($_POST["subject"]))) {
        $subjectErr = "Subject is required.";
        $hasError = true;
    } else {
        $subject = htmlspecialchars(trim($_POST["subject"]));
    }

    // Sanitize and validate message
    if (empty(trim($_POST["message"]))) {
        $messageErr = "Message is required.";
        $hasError = true;
    } else {
        $message = htmlspecialchars(trim($_POST["message"]));
    }

    // If no errors, insert into DB
    if (!$hasError) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $successMsg = "Thank you, your message has been sent successfully.";
            // Clear fields after success
            $name = $email = $subject = $message = "";
        } else {
            die("Failed to save your message. Please try again.");
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
  <title>Contact Us - Blog Posting Website</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    /* CSS same as your previous code */
    :root {
      --black: #000000;
      --white: #f0f0f0;
      --gray: #dddddd;
      --input-border: #999999;
      --radius: 10px;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --primary-color: #cb9191;
      --label-text: #666666;
      --error-text: red;
      --text-color: #222222;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Merriweather', serif;
      background-color: var(--white);
      color: var(--text-color);
      min-height: 100vh;
      padding-top: 70px;
    }
    nav {
      position: fixed; top: 0; left: 0; width: 100%;
      background-color: var(--white);
      box-shadow: var(--shadow);
      display: flex; justify-content: space-between; align-items: center;
      padding: 0.8rem 2rem; z-index: 1000;
      border-bottom: 1px solid var(--gray);
    }
    .logo {
      font-weight: 700; font-size: 1.5rem; color: var(--primary-color);
      font-family: 'Merriweather', serif;
      cursor: pointer; user-select: none; text-decoration: none;
    }
    .nav-links {
      display: flex; gap: 2rem; font-size: 1rem; font-weight: 600;
    }
    .nav-links a {
      text-decoration: none; color: var(--black);
      transition: color 0.3s ease;
      padding: 0.3rem 0.5rem; border-radius: var(--radius);
    }
    .nav-links a:hover, .nav-links a:focus {
      color: var(--primary-color);
      background-color: rgba(203, 145, 145, 0.15);
    }
    .container {
      max-width: 900px;
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 3rem 4rem;
      border: 1px solid var(--gray);
      margin: 0 auto 4rem auto;
    }
    h1 {
      font-size: 2.75rem;
      color: var(--primary-color);
      text-align: center;
      margin-bottom: 1rem;
      font-weight: 700;
    }
    hr {
      border: none;
      height: 3px;
      background-color: var(--primary-color);
      width: 100px;
      margin: 0 auto 2rem auto;
      border-radius: var(--radius);
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }
    label {
      font-weight: 600;
      color: var(--label-text);
      margin-bottom: 0.3rem;
      font-size: 1.1rem;
    }
    input[type="text"],
    input[type="email"],
    textarea {
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border: 1.5px solid var(--input-border);
      border-radius: var(--radius);
      font-family: 'Merriweather', serif;
      resize: vertical;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 5px rgba(203, 145, 145, 0.6);
    }
    textarea {
      min-height: 120px;
    }
    button {
      align-self: flex-start;
      background-color: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 0.8rem 2rem;
      font-size: 1.1rem;
      font-weight: 700;
      border-radius: var(--radius);
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover, button:focus {
      background-color: #b76f6f;
      outline: none;
    }
    .error {
      color: var(--error-text);
      font-size: 0.9rem;
      margin-top: -0.8rem;
      margin-bottom: 0.8rem;
    }
    .success-msg {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-weight: 600;
      text-align: center;
    }
    .footer {
      margin-top: 3rem;
      text-align: center;
      font-size: 0.9rem;
      color: var(--gray);
      font-style: italic;
    }
    @media (max-width: 700px) {
      nav { padding: 0.6rem 1rem; }
      .nav-links { gap: 1rem; font-size: 0.9rem; }
      .container { padding: 2rem 1.5rem; }
      h1 { font-size: 2rem; }
      label { font-size: 1rem; }
      input[type="text"], input[type="email"], textarea { font-size: 0.95rem; }
      button { font-size: 1rem; padding: 0.7rem 1.5rem; }
    }
  </style>
</head>
<body>

  <nav>
    <a href="index.php" class="logo">Blogg</a>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="create.php">Write Blog</a>
      <a href="login.php">Login</a>
    </div>
  </nav>

  <div class="container">
    <h1>Contact Us</h1>
    <hr />

    <?php if ($successMsg): ?>
      <div class="success-msg" role="alert"><?= $successMsg ?></div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
      <label for="name">Full Name</label>
      <input
        type="text"
        id="name"
        name="name"
        placeholder="Your full name"
        required
        value="<?= htmlspecialchars($name) ?>"
        aria-describedby="nameError"
        aria-invalid="<?= $nameErr ? 'true' : 'false' ?>"
      />
      <?php if ($nameErr): ?>
        <div id="nameError" class="error"><?= $nameErr ?></div>
      <?php endif; ?>

      <label for="email">Email Address</label>
      <input
        type="email"
        id="email"
        name="email"
        placeholder="you@example.com"
        required
        value="<?= htmlspecialchars($email) ?>"
        aria-describedby="emailError"
        aria-invalid="<?= $emailErr ? 'true' : 'false' ?>"
      />
      <?php if ($emailErr): ?>
        <div id="emailError" class="error"><?= $emailErr ?></div>
      <?php endif; ?>

      <label for="subject">Subject</label>
      <input
        type="text"
        id="subject"
        name="subject"
        placeholder="Subject of your message"
        required
        value="<?= htmlspecialchars($subject) ?>"
        aria-describedby="subjectError"
        aria-invalid="<?= $subjectErr ? 'true' : 'false' ?>"
      />
      <?php if ($subjectErr): ?>
        <div id="subjectError" class="error"><?= $subjectErr ?></div>
      <?php endif; ?>

      <label for="message">Message</label>
      <textarea
        id="message"
        name="message"
        placeholder="Write your message here..."
        required
        aria-describedby="messageError"
        aria-invalid="<?= $messageErr ? 'true' : 'false' ?>"
      ><?= htmlspecialchars($message) ?></textarea>
      <?php if ($messageErr): ?>
        <div id="messageError" class="error"><?= $messageErr ?></div>
      <?php endif; ?>

      <button type="submit">Send Message</button>
    </form>

    <div class="footer">© 2025 Our Blog Posting Website. All rights reserved.</div>
  </div>

</body>
</html>
