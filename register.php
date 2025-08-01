<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    /* keep your same CSS */

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
      object-fit: contain; /* show full image content */
    }

    .form-side {
      flex: 1;
      background-color: var(--white);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 3rem 2rem;
      position: relative; /* added for close btn positioning */
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

    .form-group select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg width='10' height='7' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23000' stroke-width='2' fill='none' fill-rule='evenodd'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 10px;
      height: 100%;;
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

    .error {
      color: red;
      font-size: 0.85rem;
      margin-top: -1rem;
    }

    button {
      width: 100%;
      background-color: #cb9191;
      padding: 0.9rem;
      font-size: 1rem;
      background: var(--black);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #cb9191;
      color:#000;
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
    .footer a:hover{
      color:#d19393;
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
      <img src="https://images.pexels.com/photos/399161/pexels-photo-399161.jpeg" alt="Register Banner" />
    </div>

    <div class="form-side">
      <div class="close-btn" title="Go back to home page" onclick="window.location.href='index.php'">&times;</div>

      <div class="card">
        <h1>Create Account</h1>

        <!-- Display messages -->
        <?php if (isset($_SESSION['error'])): ?>
          <p style="color:red;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
          <p style="color:green;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <!-- Send to PHP backend -->
        <form action="php/register.php" method="POST">
          <div class="form-group">
            <input type="text" name="fullname" placeholder=" " required />
            <label for="fullname">Full Name</label>
          </div>

          <div class="form-group">
            <input type="text" name="username" placeholder=" " required />
            <label for="username">Username</label>
          </div>

          <div class="form-group">
            <input type="email" name="email" placeholder=" " required />
            <label for="email">Email</label>
          </div>

          <div class="form-group password-group">
            <input type="password" name="password" id="password" placeholder=" " required minlength="6" />
            <label for="password">Password</label>
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
          </div>

          <div class="form-group password-group">
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder=" " required />
            <label for="confirmPassword">Confirm Password</label>
            <span class="toggle-password" onclick="togglePassword('confirmPassword', this)">👁️</span>
            <div class="error" id="passError"></div>
          </div>

          <div class="form-group">
            <select name="role" required>
              <option value="" disabled selected hidden></option>
              <option value="admin">Admin</option>
              <option value="reader">Reader</option>
              <option value="blogger">Blogger</option>
            </select>
            <label for="role">Select Role</label>
          </div>

          <button type="submit">Register</button>
        </form>

        <div class="footer">
          Already have an account? <a href="login.php">Login</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const passError = document.getElementById("passError");

    document.querySelector("form").addEventListener("submit", function (e) {
      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        passError.textContent = "Passwords do not match.";
      }
    });

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
