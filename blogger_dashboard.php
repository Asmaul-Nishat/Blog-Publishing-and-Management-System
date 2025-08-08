<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Blogger Dashboard - MyBlog</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --font-family: 'Merriweather', serif;
      --black: #000000;
      --white: #f0f0f0;
      --gray: #dddddd;
      --input-border: #999999;
      --button-hover-bg: #cb9191;
      --label-text-default: #666666;
      --error-text: red;
      --radius: 12px;
      --transition: 0.3s ease;
      --max-width: 1200px;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: var(--font-family);
      background: var(--white);
      color: var(--black);
      min-height: 100vh;
      display: flex;
      flex-direction: row;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    button {
      cursor: pointer;
    }
    /* Sidebar */
    .sidebar {
      width: 250px;
      background: var(--white);
      border-right: 1px solid var(--gray);
      padding: 1.5rem 1rem;
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      transition: left 0.3s ease;
      z-index: 1000;
      left: 0;
    }
    .sidebar.closed {
      left: -260px;
    }
    .sidebar .logo {
      font-size: 1.8rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--gray);
      color: var(--button-hover-bg);
    }
    .sidebar nav {
      display: flex;
      flex-direction: column;
    }
    .sidebar nav a {
      display: flex;
      align-items: center;
      padding: 0.8rem 1rem;
      margin-bottom: 0.3rem;
      font-weight: 500;
      border-radius: var(--radius);
      transition: background var(--transition);
    }
    .sidebar nav a span.icon {
      margin-right: 0.8rem;
      font-size: 1.1rem;
      width: 20px;
      display: inline-block;
      text-align: center;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background: var(--button-hover-bg);
      color: var(--white);
    }
    /* Main content */
    main.main-content {
      margin-left: 250px;
      padding: 2rem;
      flex-grow: 1;
      max-width: calc(100% - 250px);
      transition: margin-left 0.3s ease;
    }
    main.main-content.full-width {
      margin-left: 0;
      max-width: 100%;
    }
    h1, h2, h3 {
      margin-bottom: 1rem;
      font-weight: 700;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }
    th, td {
      padding: 0.75rem 1rem;
      border: 1px solid var(--gray);
      text-align: left;
    }
    th {
      background: var(--white);
      color: var(--black);
    }
    tbody tr:hover {
      background: #f8f8f8;
    }
    .actions button {
      margin-right: 0.5rem;
      padding: 0.3rem 0.6rem;
      border: 1px solid var(--black);
      background: none;
      font-size: 0.9rem;
      border-radius: var(--radius);
      color: var(--black);
      transition: var(--transition);
    }
    .actions button:hover {
      background: var(--button-hover-bg);
      color: var(--white);
      border-color: var(--button-hover-bg);
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    form.settings-form label {
      font-weight: 600;
      display: block;
      margin: 1rem 0 0.3rem;
    }
    form.settings-form input,
    form.settings-form textarea,
    form.settings-form select {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid var(--input-border);
      border-radius: var(--radius);
      background: var(--white);
      color: var(--black);
    }
    form.settings-form button {
      margin-top: 1rem;
      padding: 0.6rem 1.2rem;
      background: var(--button-hover-bg);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      font-weight: 600;
      transition: var(--transition);
    }
    form.settings-form button:hover {
      background: var(--black);
    }
    /* Sidebar toggle button for small screens */
    #sidebar-toggle {
      display: none;
      position: fixed;
      top: 1rem;
      left: 1rem;
      background: var(--button-hover-bg);
      color: var(--white);
      border: none;
      font-size: 1.8rem;
      border-radius: var(--radius);
      cursor: pointer;
      z-index: 1100;
      width: 40px;
      height: 40px;
      line-height: 36px;
      text-align: center;
      padding: 0;
      user-select: none;
    }
    /* Responsive */
    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }
      #sidebar-toggle {
        display: block;
      }
      .sidebar {
        width: 250px;
        height: 100vh;
        flex-direction: column;
        position: fixed;
        top: 0;
        left: -260px;
        background: var(--white);
        border-right: 1px solid var(--gray);
        padding-top: 3rem;
        transition: left 0.3s ease;
      }
      .sidebar.open {
        left: 0;
      }
      .sidebar nav {
        flex-direction: column;
        overflow-y: auto;
        height: calc(100vh - 3rem);
      }
      main.main-content {
        margin-left: 0;
        padding: 1rem;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

  <button id="sidebar-toggle" aria-label="Toggle sidebar navigation">☰</button>

  <aside class="sidebar" role="navigation" aria-label="Blogger dashboard navigation">
    <a href="/index.html" class="logo">Blogg</a>

    <nav>
      <a href="#" class="active" data-tab="dashboard" aria-current="page"><span class="icon">🏠</span> Dashboard</a>
      <a href="#" data-tab="posts"><span class="icon">📝</span> Manage Posts</a>
      <a href="#" data-tab="profile"><span class="icon">👤</span> Profile</a>
      <a href="#" data-tab="delete-account"><span class="icon">🗑️</span> Delete Account</a>
      <a href="logout.php"><span class="icon">🔓</span> Logout</a>
    </nav>
  </aside>

  <main class="main-content" role="main">
    <!-- Dashboard -->
    <section id="dashboard" class="tab-content active" tabindex="0">
      <h1>Welcome, <span id="usernameDisplay">User</span>!</h1>
      <p>Here is an overview of your blogging activity.</p>
      <table aria-label="Summary of your blogs">
        <tbody>
          <tr><th>Total Posts</th><td>5</td></tr>
          <tr><th>Published</th><td>4</td></tr>
          <tr><th>Drafts</th><td>1</td></tr>
          <tr><th>Comments Received</th><td>23</td></tr>
        </tbody>
      </table>
    </section>

    <!-- Manage Posts -->
    <section id="posts" class="tab-content" tabindex="0">
      <h1>Manage Your Posts</h1>
      <input type="text" id="search-posts" placeholder="Search posts..." aria-label="Search your posts" />
      <table>
        <thead>
          <tr><th>ID</th><th>Title</th><th>Status</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody id="posts-tbody">
          <!-- Posts will be inserted by JS -->
        </tbody>
      </table>
    </section>

    <!-- Profile -->
    <section id="profile" class="tab-content" tabindex="0">
      <h1>Your Profile</h1>
      <form class="settings-form" id="profile-form" novalidate>
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" required />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="user@example.com" />

        <label for="password">New Password</label>
        <input type="password" id="password" name="password" placeholder="Leave blank to keep current" />

        <button type="submit">Update Profile</button>
      </form>
    </section>

    <!-- Delete Account -->
    <section id="delete-account" class="tab-content" tabindex="0">
      <h1>Delete Account</h1>
      <p><strong>Warning:</strong> This action cannot be undone. All your data will be lost.</p>
      <br><br><button id="delete-account-btn" style="background-color:#d9534f; color:white; border:none; padding:0.8rem 1.2rem; border-radius:12px; cursor:pointer;">Delete My Account</button>
    </section>
  </main>

  <script>
    // Demo username (replace with actual session username)
    const username = "Jane Doe";

    // Display username in dashboard and profile full name input
    document.getElementById('usernameDisplay').textContent = username;
    document.getElementById('fullname').value = username;

    // Sample posts by this user
    const userPosts = [
      { id: 101, title: "My Trip to Bali", status: "Published", date: "2025-06-20" },
      { id: 102, title: "Delicious Vegan Recipes", status: "Draft", date: "2025-06-22" },
      { id: 103, title: "Tech Trends 2025", status: "Published", date: "2025-06-23" },
      { id: 104, title: "Meditation for Beginners", status: "Published", date: "2025-06-24" },
      { id: 105, title: "Learning JavaScript", status: "Published", date: "2025-06-25" }
    ];

    // Render posts in Manage Posts table
    function renderPosts() {
      const tbody = document.getElementById('posts-tbody');
      tbody.innerHTML = '';
      userPosts.forEach(post => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${post.id}</td>
          <td>${post.title}</td>
          <td>${post.status}</td>
          <td>${post.date}</td>
          <td class="actions">
            <button type="button" onclick="viewPost(${post.id})">View</button>
            <button type="button" onclick="editPost(${post.id})">Edit</button>
            <button type="button" onclick="deletePost(${post.id})" style="color:#d9534f;">Delete</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    renderPosts();

    // Tab navigation
    const tabs = document.querySelectorAll('.sidebar nav a');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', e => {
        e.preventDefault();
        const target = tab.getAttribute('data-tab');
        if (!target) return;

        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        tabContents.forEach(tc => {
          if (tc.id === target) {
            tc.classList.add('active');
            tc.focus();
          } else {
            tc.classList.remove('active');
          }
        });
      });
    });

    // Sidebar toggle for small screens
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });

    // Search posts filter
    const searchInput = document.getElementById('search-posts');
    searchInput.addEventListener('input', e => {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('#posts tbody tr');
      rows.forEach(row => {
        const title = row.children[1].textContent.toLowerCase();
        row.style.display = title.includes(searchTerm) ? '' : 'none';
      });
    });

    // Dummy handlers for buttons
    function viewPost(id) {
      alert('View post ID: ' + id);
    }
    function editPost(id) {
      alert('Edit post ID: ' + id);
    }
    function deletePost(id) {
      if (confirm('Are you sure you want to delete post ID: ' + id + '?')) {
        alert('Post deleted (dummy)');
        // Here you would do actual delete via server call
      }
    }

    // Profile update submit handler (dummy)
    document.getElementById('profile-form').addEventListener('submit', e => {
      e.preventDefault();
      alert('Profile update submitted (dummy)');
      // Add your ajax or form submit here
    });

    // Delete account button handler
    document.getElementById('delete-account-btn').addEventListener('click', () => {
      if (confirm('This action will permanently delete your account. Are you sure?')) {
        alert('Account deletion requested (dummy)');
        // Call server-side delete here, then logout
      }
    });
  </script>

</body>
</html>
