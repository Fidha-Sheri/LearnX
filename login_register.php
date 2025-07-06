<?php
// Database connection
$host = "localhost"; // Change if needed
$user = "root"; // Change to your DB username
$pass = ""; // Change to your DB password
$dbname = "users_db"; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password
    $role = $_POST['role'];
    $created_at = date("Y-m-d H:i:s"); // Capture current timestamp

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "Email already registered.";
    } else {
        // Insert user data (ignoring `name`)
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $email, $password, $role, $created_at);

        if ($stmt->execute()) {
            // Redirect to home page after successful registration
            header("Location: index.html"); 
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkEmail->close();
}

$conn->close();
?>
