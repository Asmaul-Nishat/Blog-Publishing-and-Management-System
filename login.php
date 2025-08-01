<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    /* --- Keep your same CSS here --- */

       :root {
      --black: #000;
      --white: #f0f0f0;
      --gray: #ddd;
      --input-border: #999;
      --radius: 10px;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Merriweather', serif;
      background-color: var(--white);
      color: var(--black);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      min-height: 100vh;
    }

    .container {
      display: flex;
      width: 100%;
      max-width: 1080px;
      min-height: 640px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      object-fit: contain;
    }

    .image-side {
      flex: 1;
      background-color: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .image-side img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .form-side {
      flex: 1;
      background-color: var(--white);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 3rem 2rem;
      position: relative;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 2rem;
      font-weight: bold;
      color: var(--gray);
      cursor: pointer;
      user-select: none;
      transition: color 0.3s ease;
      z-index: 10;
    }

    .close-btn:hover {
      color: var(--black);
    }

    .card {
      width: 100%;
      max-width: 400px;
    }

    h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
    }

    form {
      display: grid;
      gap: 1.5rem;
    }

    .form-group {
      position: relative;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 1rem 0.75rem;
      border: 1px solid var(--input-border);
      border-radius: var(--radius);
      background: transparent;
      font-size: 1rem;
      color: var(--black);
    }

    .form-group label {
      position: absolute;
      left: 0.75rem;
      top: 1rem;
      font-size: 1rem;
      background: var(--white);
      padding: 0 0.25rem;
      transition: all 0.3s;
      color: #666;
      pointer-events: none;
    }

    .form-group input:focus + label,
    .form-group input:not(:placeholder-shown) + label,
    .form-group select:focus + label,
    .form-group select:not([value=""]) + label {
      top: -0.6rem;
      left: 0.6rem;
      font-size: 0.75rem;
      color: var(--black);
    }

    .password-group {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 1rem;
      color: #666;
      user-select: none;
    }

    button {
      width: 100%;
      background-color: var(--black);
      padding: 0.9rem;
      font-size: 1rem;
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #cb9191;
      color: #000;
    }

    .footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
    }

    .footer a {
      color: var(--black);
      border-bottom: 1px solid var(--black);
      text-decoration: none;
      font-size: large;
    }

    .footer a:hover {
      color: #d19393;
      border-color: #0c0000;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      .image-side {
        height: 270px;
      }

      .form-side {
        padding: 2rem 1.5rem;
      }

      .card {
        max-width: 100%;
      }
    }

  </style>
</head>

<body>
  <div class="container">
    <div class="image-side">
      <img src="https://images.pexels.com/photos/399161/pexels-photo-399161.jpeg" alt="Login Banner" />
    </div>

    <div class="form-side">
      <div class="close-btn" onclick="window.location.href='index.html'" title="Go back to home">&times;</div>

      <div class="card">
        <h1>Login</h1>

        <!-- Show error/success messages -->
        <?php if (isset($_SESSION['error'])): ?>
          <p style="color:red;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
          <p style="color:green;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <!-- POST to backend PHP -->
        <form action="php/login.php" method="POST">
          <div class="form-group">
            <input type="text" id="usernameEmail" name="usernameEmail" placeholder=" " required />
            <label for="usernameEmail">Username or Email</label>
          </div>

          <div class="form-group password-group">
            <input type="password" id="password" name="password" placeholder=" " required />
            <label for="password">Password</label>
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
          </div>

          <div class="form-group">
            <select id="role" name="role" required>
              <option value="" disabled selected>Select Role</option>
              <option value="Admin">Admin</option>
              <option value="Blogger">Blogger</option>
              <option value="Reader">Reader</option>
            </select>
            <label for="role">Role</label>
          </div>

          <button type="submit">Login</button>
        </form>

        <div class="footer">
          Don't have an account? <a href="register.php">Register</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(id, el) {
      const input = document.getElementById(id);
      if (input.type === "password") {
        input.type = "text";
        el.textContent = "🙈";
      } else {
        input.type = "password";
        el.textContent = "👁️";
      }
    }
  </script>
</body>
</html>
