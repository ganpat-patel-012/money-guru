<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
//Musa Rocks
$servername = "db";  
$username = "root";   
$password = "root_password";  
$dbname = "moneyguru";  

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT userid, CONCAT(name, ' (', email, ')') AS display_name FROM users WHERE userid != ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['userid']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = htmlspecialchars(trim($_POST['type']));
    $description = htmlspecialchars(trim($_POST['description']));
    $amount = htmlspecialchars(trim($_POST['amount']));
    $userid_for = htmlspecialchars(trim($_POST['userid_for']));

    if (empty($type) || empty($description) || empty($amount) || empty($userid_for)) {
        $error_message = "Please fill all the fields.";
    } else {
        $userid_added = $_SESSION['userid'];

        $sql = "INSERT INTO Transactions (type, description, amount, userid_added, userid_for) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            die("Statement preparation failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssdis", $type, $description, $amount, $userid_added, $userid_for);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Refresh: 2; url=dashboard.php");
            $success_message = "Transaction added successfully! Redirecting to your dashboard...";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction - MoneyGuru</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1790/1790213.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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

        .form-container input, .form-container textarea, .form-container select {
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

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .select2{
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
    <a href="index.php" style="text-decoration: none;color: inherit;"><h1>💸 MoneyGuru 💰</h1></a>
        <p>Your Personal Money Lending and Borrowing Tracker</p>
    </div>

    <!-- Add Transaction Form -->
    <div class="form-container">
        <h2>Add New Transaction</h2>

        <?php
        if (!empty($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        if (!empty($success_message)) {
            echo "<p class='success-message'>$success_message</p>";
        }
        ?>

        <form method="POST" action="add_transaction.php">
            <label for="type">Transaction Type:</label>
            <select name="type" required>
                <option value="lend">Lend</option>
                <option value="borrow">Borrow</option>
            </select>

            <label for="description">Description:</label>
            <textarea name="description" placeholder="Enter description" required></textarea>

            <label for="amount">Amount (in EUR):</label>
            <input type="number" name="amount" placeholder="Enter amount" required min="0" step="any">

            <label for="userid_for">User for Transaction:</label>
            <select name="userid_for" id="userid_for" class="select2" required>
                <!-- Options will be loaded dynamically via AJAX -->
            </select>

            <!-- Scripts -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#userid_for').select2({
                        placeholder: "Search for a user...",
                        allowClear: true,
                        ajax: {
                            url: 'fetch_user.php',
                            type: 'GET',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.map(function(user) {
                                        return {
                                            id: user.userid,
                                            text: user.display_name
                                        };
                                    })
                                };
                            },
                            cache: true
                        }
                    });
                });
            </script>
            
            <input type="submit" value="Add Transaction">
        </form>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>MoneyGuru by Ganpat Patel, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar</p>
        <p>Project Goal: A web app to track money lending and borrowing transactions</p>
    </div>

</body>
</html>
