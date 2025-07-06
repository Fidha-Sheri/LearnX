<?php
session_start();
require 'config.php';

// ✅ Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ✅ Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting user!";
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit;
}

// ✅ Fetch only non-admin users
$query = "SELECT id, fullname, email, role FROM users WHERE role != 'admin'";
$users = $conn->query($query);

// ✅ Fetch uploaded notes
$notesQuery = "SELECT id, description, filepath, uploaded_by, uploaded_at FROM semester_notes ORDER BY uploaded_at DESC";
$notes = $conn->query($notesQuery);

// ✅ Fetch messages
$messagesQuery = "SELECT id, name, email, phone, message, response FROM contact_messages ORDER BY created_at DESC";
$messages = $conn->query($messagesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #12c2b9;
            --secondary: #dbfffe;
            --black: #141414;
            --blue: #1148e2;
            --white: #fff;
            --box-shadow: 0 .5rem 1rem rgba(0,0,0,0.1);
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--secondary);
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            background: var(--white);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-radius: 10px;
            text-align: center;
        }

        h2 { color: var(--black); }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn:hover {
            background: var(--blue);
        }

        .content {
            display: none;
        }

        .active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid var(--primary-color);
            padding: 12px;
            text-align: left;
        }

        th { background-color: var(--primary-color); color: var(--white); }

        tr:nth-child(even) { background-color: var(--secondary); }

        .form-container {
            max-width: 500px;
            margin: auto;
            text-align: left;
            padding: 20px;
            background: var(--white);
            box-shadow: var(--box-shadow);
            border-radius: 10px;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
        }

        .upload-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .upload-btn:hover { background: var(--blue); }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <h2>Welcome, Admin</h2>

        <div class="btn-container">
            <button class="btn" onclick="showSection('users')">Manage Users</button>
            <button class="btn" onclick="showSection('upload')">Upload Notes</button>
            <button class="btn" onclick="showSection('messages')">View Messages</button>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <!-- Users List Section -->
        <div id="users" class="content active">
            <h3>Users List</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="admin_dashboard.php?delete=<?php echo $user['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this user?');" 
                               style="color: red; text-decoration: none; font-weight: bold;">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Upload Notes Section -->
        <div id="upload" class="content">
            <h3>Upload Study Materials</h3>
            <div class="form-container">
                <form action="upload_notes.php" method="post" enctype="multipart/form-data">
                    <label for="description">Description:</label>
                    <input type="text" name="description" required placeholder="Enter description">

                    <label for="semester">Select Semester:</label>
                    <select name="semester" required>
                        <option value="SEM 1">Semester 1</option>
                        <option value="SEM 2">Semester 2</option>
                        <option value="SEM 3">Semester 3</option>
                        <option value="SEM 4">Semester 4</option>
                        <option value="SEM 5">Semester 5</option>
                        <option value="SEM 6">Semester 6</option>
                    </select>

                    <label for="file">Upload File:</label>
                    <input type="file" name="file" required>

                    <button type="submit" class="upload-btn">Upload Notes</button>
                </form>
            </div>
        </div>

        <!-- Messages Section -->
        <div id="messages" class="content">
            <h3>Messages</h3>
            <a href="admin_messages.php">View Messages</a>
        </div>

        <br>
        <a href="logout.php" class="btn">Logout</a>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content').forEach(sec => sec.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }
    </script>

</body>
</html>
