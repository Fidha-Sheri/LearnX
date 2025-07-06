<?php
session_start();
require 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied! Only admins can upload notes.";
    header("Location: login.php");
    exit;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $semester = intval($_POST['semester']);
    $uploaded_by = $_SESSION['user_id'];

    // File upload settings
    $target_dir = "uploads/";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name; // Prevent duplicate names
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ["pdf", "doc", "docx", "png", "jpg", "jpeg"];

    // Check file type
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['error'] = "Invalid file type! Only PDF, Word, or image files are allowed.";
        header("Location: upload_notes.php");
        exit;
    }

    // Move file to uploads folder
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Insert into database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO semester_notes (description, filepath, uploaded_by, uploaded_at, semester) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->bind_param("ssii", $description, $target_file, $uploaded_by, $semester);

        if ($stmt->execute()) {
            $_SESSION['success'] = "File uploaded successfully!";
        } else {
            $_SESSION['error'] = "Database error: Could not save file details.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error'] = "File upload failed!";
    }

    header("Location: upload_notes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root { 
            --primary-color:#12c2b9;
            --secondary:#dbfffe;
            --black:#141414;
            --blue:#1148e2;
            --white:#fff;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--secondary);
            color: var(--black);
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: var(--primary-color);
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
        }


        .upload-container {
            width: 50%;
            margin: 20px auto;
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
            font-size: 16px;
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
        }

        .upload-btn {
            background: var(--blue);
            color: var(--white);
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            font-size: 16px;
        }

        .upload-btn:hover {
            background: var(--primary-color);
        }
    </style>
</head>
<body>

<header class="header">
    LearnX
</header>

<div class="upload-container">
    <?php
    if (isset($_SESSION['success'])) {
        echo "<p class='success-message'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>

    <h1 class="heading">Upload Study Notes</h1>

    <form action="upload_notes.php" method="POST" enctype="multipart/form-data">
        <label for="description">Description:</label>
        <input type="text" name="description" required>

        <label for="semester">Select Semester:</label>
        <select name="semester" required>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
        </select>

        <label for="file">Upload File:</label>
        <input type="file" name="file" required>

        <button type="submit" class="upload-btn">Upload</button>
    </form>
</div>

</body>
</html>
