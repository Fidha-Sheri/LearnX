<?php
session_start();
require 'config.php'; // Ensure correct database credentials

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Both email and password are required!'); window.location.href='index.html';</script>";
        exit;
    }

    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, 'users_db'); // Ensure correct database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check user in `users` table
    $query = "SELECT id, fullname, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php"); // Redirect admin to admin dashboard
                exit;
            } else {
                header("Location:  index.html#Study Material"); // Redirect user to study materials
                exit;
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('User not found! Please register first.'); window.location.href='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>window.location.href='index.html';</script>";
}
