<?php
// Include database connection
include 'php/config.php'; // Adjust path if needed

// Sanitize function
function clean_input($data) {
    return htmlspecialchars(trim($data));
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title'] ?? '');
    $author = clean_input($_POST['author'] ?? '');
    $categoryName = clean_input($_POST['category'] ?? '');
    $imageType = $_POST['image_type'] ?? 'url';
    $imageURL = '';

    // Validate required fields (simplified, add your own checks as needed)
    if (empty($title) || empty($author) || empty($categoryName)) {
        die("Title, Author, and Category are required.");
    }

    // Find category_id by category name
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $stmt->bind_result($category_id);
    if (!$stmt->fetch()) {
        // Category not found, optionally insert new category or set to null
        $category_id = null;
    }
    $stmt->close();

    // Handle image input
    if ($imageType === 'url') {
        $imageURL = clean_input($_POST['image_url'] ?? '');
    } elseif ($imageType === 'file') {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['image_file']['name']));
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $imageURL = $targetFilePath; // save relative path including folder
            } else {
                die("Error uploading image file.");
            }
        }
    }

    // Content and status
    $content = clean_input($_POST['content'] ?? '');
    $status = 'published';  // default to published
    $user_id = 0;  // Use 0 or some dummy integer if no user system

    // Insert post into DB
    $insertSql = "INSERT INTO posts (user_id, title, content, image, category_id, status, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("isssis", $user_id, $title, $content, $imageURL, $category_id, $status);

    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to homepage to show all blogs
        header("Location: index.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
} else {
    // Reject non-POST requests
    die("Invalid request.");
}
?>
