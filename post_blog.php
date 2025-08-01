<?php
// Include database connection
include 'php/config.php'; // Adjust path if needed

function clean_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title'] ?? '');
    $author = clean_input($_POST['author'] ?? '');
    $categoryName = clean_input($_POST['category'] ?? '');
    $imageType = $_POST['image_type'] ?? 'url';
    $imageURL = '';

    if (empty($title) || empty($author) || empty($categoryName)) {
        die("Title, Author, and Category are required.");
    }

    // Find category_id by category name
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $stmt->bind_result($category_id);
    if (!$stmt->fetch()) {
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
                $imageURL = $fileName; // store filename only
            } else {
                die("Error uploading image file.");
            }
        }
    }

    $content = clean_input($_POST['content'] ?? '');
    $status = 'published';

    // **Set user_id to NULL explicitly**
    $user_id = null; // No logged in user

    // Prepare SQL
    $insertSql = "INSERT INTO posts (user_id, title, content, image, category_id, status, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $conn->prepare($insertSql);
    
    // Bind params with user_id as nullable
    // For nullable int, use "i" type but bind a PHP variable set to NULL.
    $stmt->bind_param("isssis", $user_id, $title, $content, $imageURL, $category_id, $status);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
?>
