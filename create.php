<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Blog - MyBlog</title>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Merriweather', serif;
      margin: 0;
      background: #fff;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 12px;
      background: #f9ecec;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    h1 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: bold;
      margin: 1rem 0 0.3rem;
    }

    input[type="text"],
    select,
    textarea,
    input[type="file"] {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      border-radius: 10px;
      border: 1px solid #ccc;
      resize: vertical;
    }

    button {
      margin-top: 1.5rem;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border: none;
      border-radius: 25px;
      background: #c8d9eb;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #f0d9da;
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .preview {
      margin-top: 3rem;
    }

    .blog-card {
      background: #f0d9da;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
    }

    .blog-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .blog-content {
      padding: 1rem;
    }

    .blog-title {
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
    }

    .blog-excerpt {
      font-size: 0.95rem;
      color: #333;
    }

    .blog-full-content {
      margin-top: 1rem;
      font-size: 0.95rem;
      color: #444;
      line-height: 1.5;
    }

    .blog-meta {
      margin-top: 1rem;
      font-size: 0.85rem;
      color: #444;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .blog-meta div {
      display: flex;
      align-items: center;
      gap: 0.3rem;
      font-weight: bold;
    }

    .upload-time {
      margin-left: auto;
      font-style: italic;
    }

    @media (max-width: 600px) {
      .form-actions {
        flex-direction: column;
      }

      .upload-time {
        margin-left: 0;
        margin-top: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Create New Blog</h1>
    <form id="blogForm" action="post_blog.php" method="POST" enctype="multipart/form-data">
      <label for="title">Blog Title</label>
      <input type="text" name="title" id="title" required />

      <label for="author">Author Name</label>
      <input type="text" name="author" id="author" required />

      <label for="category">Category</label>
      <select name="category" id="category" required>
        <option value="">--Select--</option>
        <option>Travel</option>
        <option>Food</option>
        <option>Technology</option>
        <option>Health</option>
        <option>Education</option>
      </select>

      <label for="imageType">Image Source</label>
      <select id="imageType" name="image_type">
        <option value="url">Use Image URL</option>
        <option value="file">Upload from Device</option>
      </select>

      <div id="imageURLInput">
        <label for="imageURL">Image URL</label>
        <input type="text" name="image_url" id="imageURL" />
      </div>

      <div id="imageFileInput" style="display: none;">
        <label for="imageFile">Choose Image File</label>
        <input type="file" name="image_file" id="imageFile" accept="image/*" />
      </div>

      <label for="excerpt">Short Excerpt</label>
      <textarea name="excerpt" id="excerpt" rows="3" required></textarea>

      <label for="content">Full Content</label>
      <textarea name="content" id="content" rows="6" required></textarea>

      <div class="form-actions">
        <button type="button" onclick="previewBlog()">Preview Blog</button>
        <button type="submit">Post Blog</button>
      </div>
    </form>

    <!-- Preview Section -->
    <div class="preview" id="previewSection" style="display:none;">
      <h2>Preview</h2>
      <div class="blog-card">
        <img id="previewImage" src="" alt="Preview Image" />
        <div class="blog-content">
          <h2 class="blog-title" id="previewTitle"></h2>
          <p class="blog-excerpt" id="previewExcerpt"></p>
          <div class="blog-full-content" id="previewContent"></div>
          <div class="blog-meta">
            <div>👤 <span id="previewAuthor"></span></div>
            <div>❤️ <span>0</span></div>
            <div>💬 <span>0</span></div>
            <div>🔗 <span>Share</span></div>
            <div class="upload-time" id="previewTime"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const imageType = document.getElementById('imageType');
    const imageURLInput = document.getElementById('imageURLInput');
    const imageFileInput = document.getElementById('imageFileInput');

    imageType.addEventListener('change', function () {
      if (imageType.value === 'url') {
        imageURLInput.style.display = 'block';
        imageFileInput.style.display = 'none';
      } else {
        imageURLInput.style.display = 'none';
        imageFileInput.style.display = 'block';
      }
    });

    function previewBlog() {
      const title = document.getElementById('title').value;
      const author = document.getElementById('author').value;
      const category = document.getElementById('category').value;
      const excerpt = document.getElementById('excerpt').value;
      const content = document.getElementById('content').value;
      const previewImage = document.getElementById('previewImage');
      const uploadTime = new Date().toLocaleString();

      document.getElementById('previewTitle').textContent = `${title} (${category})`;
      document.getElementById('previewExcerpt').textContent = excerpt;
      document.getElementById('previewContent').textContent = content;
      document.getElementById('previewTime').textContent = uploadTime;
      document.getElementById('previewAuthor').textContent = author;

      if (imageType.value === 'url') {
        const url = document.getElementById('imageURL').value.trim();
        if (!url) return alert("Please enter an image URL.");
        previewImage.src = url;
      } else {
        const file = document.getElementById('imageFile').files[0];
        if (!file) return alert("Please choose a file.");
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }

      document.getElementById('previewSection').style.display = 'block';
      document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
    }
  </script>
</body>
</html>
