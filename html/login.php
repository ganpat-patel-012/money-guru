<?php
session_start();

if (isset($_SESSION['userid'])) {
    header("Location: dashboard.php");
    exit();
}

$servername = "db";  // Docker MySQL service name
$username = "root";   // Database username
$password = "root_password";  // MySQL root password
$dbname = "moneyguru";  // Database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    $sql = "SELECT userid, password FROM Users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($password === $row['password']) {
                $_SESSION['userid'] = $row['userid'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "No account found with that email. Please register.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1790/1790213.png" type="image/x-icon">
    <title>Login - MoneyGuru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
        }

        .header {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 20px;
            margin-bottom: 30px;
        }

        .footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }

        .form-container input, .form-container textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container input[type="submit"] {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
    <a href="index.php" style="text-decoration: none;color: inherit;"><h1>💸 MoneyGuru 💰</h1></a>
        <p>Your Personal Money Lending and Borrowing Tracker</p>
    </div>

    <!-- Login Form -->
    <div class="form-container">
        <h2>Login to Your Account</h2>
        <?php if (!empty($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>MoneyGuru by Ganpat Patel, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar</p>
        <p>Project Goal: A web app to track money lending and borrowing transactions</p>
    </div>
</body>
</html>
