<?php
session_start();
require 'config.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $uploaded_by = $_SESSION['user_id'];

    if (!empty($_FILES['file']['name'])) {
        $file_name = basename($_FILES["file"]["name"]);
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_path = "uploads/" . $file_name;

        // Move file to 'uploads' folder
        if (move_uploaded_file($file_tmp, $file_path)) {
            $query = "INSERT INTO semester_notes (title, description, file_path, uploaded_by) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $title, $description, $file_path, $uploaded_by);
            if ($stmt->execute()) {
                echo "<script>alert('Note uploaded successfully!'); window.location.href='admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Error uploading note!');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to move uploaded file!');</script>";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Semester Notes</title>
    <style>
        body { font-family: Arial, sans-serif; background: #dbfffe; padding: 20px; text-align: center; }
        form { background: white; padding: 20px; max-width: 400px; margin: auto; box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.1); border-radius: 8px; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; }
        button { background: #12c2b9; color: white; padding: 10px; border: none; cursor: pointer; }
        button:hover { background: #1148e2; }
    </style>
</head>
<body>
    <h2>Upload Semester Notes</h2>
    <form action="add_notes.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Enter title" required>
        <textarea name="description" placeholder="Enter description" required></textarea>
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
    <br>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
