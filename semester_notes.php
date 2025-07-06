<?php
session_start();
require 'config.php';

if (!isset($_GET['semester']) || !is_numeric($_GET['semester'])) {
    die("Semester not specified.");
}

$semester = intval($_GET['semester']);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, description, filepath, uploaded_at FROM semester_notes WHERE semester = ?");
$stmt->bind_param("i", $semester);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester <?php echo $semester; ?> Notes</title>
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


        .notes-section {
            padding: 20px;
            text-align: center;
        }

        .heading {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .notes-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .note-item {
            background: var(--white);
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 15px;
            width: 300px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .note-item p {
            margin: 5px 0;
            font-size: 16px;
        }

        .download-btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background: var(--blue);
            color: var(--white);
            text-decoration: none;
            border-radius: 5px;
        }

        .download-btn:hover {
            background: var(--primary-color);
        }
    </style>
</head>
<body>

<header class="header">
    LearnX - Semester <?php echo $semester; ?> Notes
</header>

<section class="notes-section">
    <h1 class="heading">Study Materials for Semester <?php echo $semester; ?></h1>

    <div class="notes-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='note-item'>";
                echo "<p><strong>" . htmlspecialchars($row['description']) . "</strong></p>";
                echo "<p>Uploaded on: " . $row['uploaded_at'] . "</p>";
                echo "<a href='" . $row['filepath'] . "' target='_blank' class='download-btn'>Download</a>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: red;'>No notes available for this semester.</p>";
        }
        ?>
    </div>
</section>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
