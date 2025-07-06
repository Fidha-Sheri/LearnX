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

// Fetch messages
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages</title>
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
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: var(--white);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            color: var(--black);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: var(--white);
            box-shadow: var(--box-shadow);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid var(--primary-color);
            padding: 12px;
            text-align: left;
        }

        th {
            background: var(--primary-color);
            color: var(--white);
        }

        tr:nth-child(even) {
            background: var(--secondary);
        }

        .reply-btn {
            display: inline-block;
            padding: 8px 15px;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .reply-btn:hover {
            background: var(--blue);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Contact Messages</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Response</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td><?= htmlspecialchars($row['message']); ?></td>
                <td><?= $row['response'] ? htmlspecialchars($row['response']) : "No response yet"; ?></td>
                <td><a href="respond_message.php?id=<?= $row['id']; ?>" class="reply-btn">Reply</a></td>
            </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>

<?php $conn->close(); ?>
