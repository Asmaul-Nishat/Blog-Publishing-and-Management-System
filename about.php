<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Blog Posting Website</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --black: #000000;
      --white: #f0f0f0;
      --gray: #dddddd;
      --input-border: #999999;
      --radius: 10px;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --primary-color: #cb9191; /* Muted pinkish-red */
      --label-text: #666666;
      --error-text: red;
      --text-color: #222222;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Merriweather', serif;
      background-color: var(--white);
      color: var(--text-color);
      min-height: 100vh;
      padding-top: 70px; /* space for fixed navbar */
    }

    /* Navbar styles */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: var(--white);
      box-shadow: var(--shadow);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 2rem;
      z-index: 1000;
      border-bottom: 1px solid var(--gray);
    }

    .logo {
      font-weight: 700;
      font-size: 1.5rem;
      color: var(--primary-color);
      font-family: 'Merriweather', serif;
      cursor: pointer;
      user-select: none;
      text-decoration: none;
    }

    .nav-links {
      display: flex;
      gap: 2rem;
      font-size: 1rem;
      font-weight: 600;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--black);
      transition: color 0.3s ease;
      padding: 0.3rem 0.5rem;
      border-radius: var(--radius);
    }

    .nav-links a:hover,
    .nav-links a:focus {
      color: var(--primary-color);
      background-color: rgba(203, 145, 145, 0.15);
    }

    /* Container styles */
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

    .about-image {
      display: block;
      max-width: 100%;
      height: auto;
      border-radius: var(--radius);
      margin: 0 auto 2.5rem auto;
      box-shadow: 0 4px 15px rgba(203, 145, 145, 0.4);
    }

    p {
      font-size: 1.125rem;
      line-height: 1.7;
      margin-bottom: 1.75rem;
      text-align: justify;
    }

    strong {
      color: var(--primary-color);
    }

    ul {
      list-style-type: disc;
      padding-left: 1.5rem;
      margin-bottom: 1.75rem;
      color: var(--label-text);
    }

    ul li {
      margin-bottom: 0.7rem;
      font-size: 1.1rem;
    }

    .highlight {
      background-color: var(--primary-color);
      color: var(--white);
      padding: 0.15rem 0.5rem;
      border-radius: 4px;
      font-weight: 600;
    }

    a {
      color: var(--primary-color);
      text-decoration: none;
      border-bottom: 1px solid var(--primary-color);
      transition: color 0.3s ease, border-color 0.3s ease;
    }

    a:hover {
      color: #bd7f7f;
      border-color: #a66969;
    }

    .footer {
      margin-top: 3rem;
      text-align: center;
      font-size: 0.9rem;
      color: var(--gray);
      font-style: italic;
    }

    /* Responsive */
    @media (max-width: 700px) {
      nav {
        padding: 0.6rem 1rem;
      }

      .nav-links {
        gap: 1rem;
        font-size: 0.9rem;
      }

      .container {
        padding: 2rem 1.5rem;
      }

      h1 {
        font-size: 2rem;
      }

      p, ul li {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

  <nav>
    <a href="index.html" class="logo">Blogg</a>
    <div class="nav-links">
      <a href="index.html">Home</a>
      <a href="create.html">Write Blog</a>
      <a href="login.html">Login</a>
    </div>
  </nav>

  <div class="container">
    <h1>About Us</h1>
    <hr />

    <img
      src="https://images.pexels.com/photos/374016/pexels-photo-374016.jpeg"
      alt="Blogging Community"
      class="about-image"
    />

    <p>Welcome to <strong class="highlight">Our Blog Posting Website</strong>, your go-to platform for reading, writing, and sharing insightful blogs on a variety of topics. We are passionate about creating a community where voices can be heard, stories can be shared, and knowledge can be exchanged freely.</p>

    <p>Our mission is to provide a <strong>user-friendly and engaging platform</strong> that allows anyone to:</p>
    <ul>
      <li>Read well-curated blog posts across diverse topics</li>
      <li>Post their own blogs with ease and style</li>
      <li>Interact with other readers and writers through comments and feedback</li>
      <li>Discover new perspectives and ideas every day</li>
    </ul>

    <p>We believe that everyone has a story to tell, and our website makes it simple and enjoyable to share your experiences or expertise with a wider audience. Whether you are an aspiring blogger or a casual reader, you’ll find our platform inviting and supportive.</p>

    <p>Thank you for visiting and being part of our growing community! If you have any questions or want to get involved, feel free to <a href="contact.html">contact us</a>.</p>

    <div class="footer">© 2025 Our Blog Posting Website. All rights reserved.</div>
  </div>

</body>
</html>
