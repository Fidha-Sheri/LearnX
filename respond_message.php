<?php
session_start();
require 'config.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch message details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM contact_messages WHERE id = $id");
    $message = $result->fetch_assoc();
    if (!$message) {
        header("Location: admin_messages.php");
        exit;
    }
} else {
    header("Location: admin_messages.php");
    exit;
}

// Handle reply
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply = $_POST['reply'];
    $to = $message['email'];
    $subject = "Response from Admin";
    $headers = "From: admin@learnx.com";

    // Send email
    if (mail($to, $subject, $reply, $headers)) {
        // Store response in database
        $stmt = $conn->prepare("UPDATE contact_messages SET response = ? WHERE id = ?");
        $stmt->bind_param("si", $reply, $id);
        $stmt->execute();
        echo "<script>alert('Reply sent successfully!'); window.location.href='admin_messages.php';</script>";
    } else {
        echo "<script>alert('Failed to send reply.'); window.location.href='respond_message.php?id=$id';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <style>
        :root {
            --primary-color: #12c2b9;
            --secondary: #dbfffe;
            --black: #141414;
            --blue: #1148e2;
            --white: #fff;
            --box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--secondary);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 600px;
            background: var(--white);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            color: var(--black);
        }

        p {
            font-size: 16px;
            color: var(--black);
            margin: 10px 0;
            text-align: left;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
            resize: none;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background: var(--blue);
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: bold;
        }

        .back-btn:hover {
            color: var(--blue);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Reply to <?= htmlspecialchars($message['name']); ?></h1>
    <p><strong>Email:</strong> <?= htmlspecialchars($message['email']); ?></p>
    <p><strong>Message:</strong> <?= htmlspecialchars($message['message']); ?></p>

    <form method="POST">
        <textarea name="reply" rows="5" required placeholder="Type your reply here..."></textarea><br>
        <button type="submit" class="btn">Send Reply</button>
    </form>
    
    <a href="admin_messages.php" class="back-btn">Back to Messages</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
