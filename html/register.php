<?php
$servername = "db";
$username = "root";
$password = "root_password";
$dbname = "moneyguru";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = !empty($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
    $address = !empty($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
    $password = htmlspecialchars(trim($_POST['password']));

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {
        $sql = "INSERT INTO Users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $message = "Statement preparation failed: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $address, $password);

            if (mysqli_stmt_execute($stmt)) {
                $message = "User successfully added!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MoneyGuru</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1790/1790213.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    <!-- Register Form -->
    <div class="form-container">
        <h2>Create an Account</h2>
        <form method="POST" action="register.php">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number (Optional)">
            <textarea name="address" placeholder="Address (Optional)"></textarea>
            <input type="password" name="password" placeholder="Password (min. 8 characters)" required>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Logindoc here</a></p>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>MoneyGuru by Ganpat Patel, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar</p>
        <p>Project Goal: A web app to track money lending and borrowing transactions</p>
    </div>

</body>
</html>
