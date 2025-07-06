<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch user details from session
$user_name = $_SESSION['user_name']; // Assuming username is stored in session
$user_email = $_SESSION['user_email']; // Assuming email is stored in session
$user_role = $_SESSION['user_role']; // User role (Admin/User)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
</header>

<div class="profile-container">
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
    <p><strong>Role:</strong> <?php echo ($user_role == 'admin') ? 'Administrator' : 'User'; ?></p>
    <a href="logout.php" class="btn">Logout</a>
</div>

<style>
    .profile-container {
        width: 50%;
        margin: auto;
        padding: 20px;
        background: #f9f9f9;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: red;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>

</body>
</html>
