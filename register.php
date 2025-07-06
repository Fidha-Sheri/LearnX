<?php
// Database connection
define('DB_HOST', 'localhost'); // Change if needed
define('DB_USER', 'root'); // Your DB username
define('DB_PASS', ''); // Your DB password
define('DB_NAME', 'users'); // Your DB name

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('All fields are required!'); window.location.href='register.php';</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.php';</script>";
        exit;
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now login.'); window.location.href='index.html#login-form';</script>";
    } else {
        echo "<script>alert('Error registering user. Try again!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #12c2b9;
            --secondary: #dbfffe;
            --black: #141414;
            --white: #fff;
            --box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.1);
        }

        * {
            font-family: 'DM Sans', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none;
            border: none;
            text-decoration: none;
            transition: all .2s linear;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: var(--secondary);
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 350px;
            padding: 2rem;
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            text-align: center;
        }

        .heading {
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .register-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .register-form .box {
            width: 100%;
            font-size: 1rem;
            padding: 0.8rem;
            border: .1rem solid var(--primary-color);
            margin-bottom: 1.3rem;
            border-radius: .5rem;
            background: var(--white);
            text-align: center;
        }

        .register-form .box:focus {
            border-color: var(--black);
        }

        .register-form .select-container select {
            width: 100%;
            font-size: 1rem;
            padding: 0.8rem;
            border: .1rem solid var(--primary-color);
            margin-bottom: 1.3rem;
            border-radius: .5rem;
            background: var(--white);
            text-align: center;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .register-form .select-container select:focus {
            border-color: var(--black);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, 0.2);
        }

        .btn {
            margin-top: .4rem;
            width: 100%;
            padding: 0.8rem;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--white);
            background: var(--primary-color);
            border-radius: .5rem;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .btn:hover {
            background: var(--black);
        }

        .login-link {
            margin-top: 1rem;
            font-size: 1.2rem;
        }

        .login-link a {
            color: var(--primary-color);
            font-weight: 700;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h2 class="heading">Register</h2>
        <form action="register.php" method="POST" class="register-form">
            <input type="text" name="fullname" placeholder="Full Name" class="box" required>
            <input type="email" name="email" placeholder="Email" class="box" required>
            <input type="password" name="password" placeholder="Password" class="box" required>
            <div class="select-container">
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="index.html#login-form">Login here</a></p>
    </div>

</body>
</html>
