<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$servername = "db"; 
$username = "root";
$password = "root_password"; 
$dbname = "moneyguru";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['userid'];

$sql_user = "SELECT name, email, phone, address FROM Users WHERE userid = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);

if ($stmt_user) {
    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    mysqli_stmt_close($stmt_user);
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

$sql_summary = "
    SELECT 
        SUM(CASE WHEN (userid_added = ? OR userid_for = ?) AND type = 'borrow' AND settle = 0 THEN amount ELSE 0 END) AS total_borrow,
        SUM(CASE WHEN (userid_added = ? OR userid_for = ?) AND type = 'lend' AND settle = 0 THEN amount ELSE 0 END) AS total_give
    FROM Transactions";

$stmt_summary = mysqli_prepare($conn, $sql_summary);

if ($stmt_summary) {
    mysqli_stmt_bind_param($stmt_summary, "iiii", $user_id, $user_id, $user_id, $user_id);
    mysqli_stmt_execute($stmt_summary);
    $result_summary = mysqli_stmt_get_result($stmt_summary);
    $summary = mysqli_fetch_assoc($result_summary);
    mysqli_stmt_close($stmt_summary);

    $total_borrow = $summary['total_borrow'] ?? 0;
    $total_give = $summary['total_give'] ?? 0;
    $total_owed_to_you = $summary['total_owed_to_you'] ?? 0;
    $total_owed_by_you = $summary['total_owed_by_you'] ?? 0;

    $total_you_owe = $total_borrow + $total_owed_by_you;
    $total_others_owe = $total_give + $total_owed_to_you;
    $final_state = $total_others_owe - $total_you_owe;
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

$sql_transactions = "
    SELECT 
        T.id, 
        T.userid_added, 
        T.userid_for, 
        T.amount, 
        T.type, 
        T.description, 
        T.date,
        T.settle,
        U1.name AS added_by_name, 
        U2.name AS for_name 
    FROM 
        Transactions T
    JOIN Users U1 ON T.userid_added = U1.userid
    JOIN Users U2 ON T.userid_for = U2.userid
    WHERE (T.userid_added = ? OR T.userid_for = ?) AND T.settle = 0
    ORDER BY T.date DESC";

$stmt_transactions = mysqli_prepare($conn, $sql_transactions);

if ($stmt_transactions) {
    mysqli_stmt_bind_param($stmt_transactions, "ii", $user_id, $user_id);
    mysqli_stmt_execute($stmt_transactions);
    $result_transactions = mysqli_stmt_get_result($stmt_transactions);
    $transactions = mysqli_fetch_all($result_transactions, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_transactions);
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

$sql_transactions = "
    SELECT 
        T.id, 
        T.userid_added, 
        T.userid_for, 
        T.amount, 
        T.type, 
        T.description, 
        T.date,
        T.settle,
        U1.name AS added_by_name, 
        U2.name AS for_name 
    FROM 
        Transactions T
    JOIN Users U1 ON T.userid_added = U1.userid
    JOIN Users U2 ON T.userid_for = U2.userid
    WHERE (T.userid_added = ? OR T.userid_for = ?) AND T.settle = 1
    ORDER BY T.date DESC";

$stmt_transactions = mysqli_prepare($conn, $sql_transactions);

if ($stmt_transactions) {
    mysqli_stmt_bind_param($stmt_transactions, "ii", $user_id, $user_id);
    mysqli_stmt_execute($stmt_transactions);
    $result_transactions = mysqli_stmt_get_result($stmt_transactions);
    $settletransactions = mysqli_fetch_all($result_transactions, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_transactions);
} else {
    die("Error preparing statement: " . mysqli_error($conn));
}

if (isset($_GET['settle'])) {
    $transaction_id = $_GET['settle'];
    $sql_settle = "UPDATE Transactions SET settle = 1 WHERE id = ?";
    $stmt_settle = mysqli_prepare($conn, $sql_settle);
    mysqli_stmt_bind_param($stmt_settle, "i", $transaction_id);
    mysqli_stmt_execute($stmt_settle);
    mysqli_stmt_close($stmt_settle);
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['unsettle'])) {
    $transaction_id = $_GET['unsettle'];
    $sql_unsettle = "UPDATE Transactions SET settle = 0 WHERE id = ?";
    $stmt_unsettle = mysqli_prepare($conn, $sql_unsettle);
    mysqli_stmt_bind_param($stmt_unsettle, "i", $transaction_id);
    mysqli_stmt_execute($stmt_unsettle);
    mysqli_stmt_close($stmt_unsettle);
    header("Location: dashboard.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MoneyGuru</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/1790/1790213.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transactions-table th, .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .transactions-table th {
            background-color: #007BFF;
            color: white;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF0000;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #CC0000;
        }

        h2 {
            margin-top: 0;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            margin: 0 10px;
            padding: 20px;
            text-align: center;
            background-color: #f4f4f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 1.2em;
            color: #333;
        }

        .container .positive {
            color: green;
        }

        .container .negative {
            color: red;
        }

        .user-info-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .user-info {
            width: 80%;
        }

        .button-container {
            width: 20%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .settle, .unsettle {
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            display: inline-block;
            margin-top: 5px;
        }

        .settle {
            background-color: #28a745; /* Green for settle */
            color: white;
        }

        .settle:hover {
            background-color: #218838; /* Darker green for hover */
        }

        .unsettle {
            background-color: #ffc107; /* Yellow for unsettle */
            color: black;
        }

        .unsettle:hover {
            background-color: #e0a800; /* Darker yellow for hover */
        }

        .delete-btn {
            padding: 10px 20px;
            background-color: #FF4C4C;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        .delete-btn:hover {
            background-color: #D94C4C;
        }


    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
    <a href="index.php" style="text-decoration: none;color: inherit;"><h1>💸 MoneyGuru 💰</h1></a>
        <p>Your Personal Money Lending and Borrowing Tracker</p>
    </div>

    <!-- Dashboard -->
    <div class="container">
    
    <?php if (isset($_GET['error']) && $_GET['error'] == 'unauthorized'): ?>
        <p style="color: red;">You are not authorized to delete this transaction.</p>
    <?php endif; ?>

    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="user-info-container">
        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></p>
        </div>

        <!-- New Button Container -->
        <div class="button-container">
            <a href="add_transaction.php" class="button">Add Transaction</a>
            <a href="modify_register.php" class="button">Edit Profile</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Borrowed</h3>
                <p class="negative">€<?php echo number_format($total_you_owe, 2); ?></p>
            </div>
            <div class="card">
                <h3>Total Lent</h3>
                <p class="positive">€<?php echo number_format($total_others_owe, 2); ?></p>
            </div>
            <div class="card">
                <h3>Final State</h3>
                <p class="<?php echo $final_state >= 0 ? 'positive' : 'negative'; ?>">
                    €<?php echo number_format($final_state, 2); ?>
                </p>
            </div>
        </div>

        <h3>Your Unsettled Transactions</h3>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Added By</th>
                    <th>For</th>
                    <th>Settle Change</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['added_by_name']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['for_name']); ?></td>
                            <td><a href="dashboard.php?settle=<?php echo $transaction['id']; ?>" class="button settle">Settle</a></td>
                            <td>
                                <?php if ($transaction['userid_added'] == $user_id): ?>
                                    <form action="delete_transaction.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <button class="delete-btn" disabled style="background-color: #ddd; color: #aaa; cursor: not-allowed;">Delete</button>
                                <?php endif; ?>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>

        <h3>Your Settled Transactions</h3>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Added By</th>
                    <th>For</th>
                    <th>Settle Change</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($settletransactions)): ?>
                    <?php foreach ($settletransactions as $settletransactions): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($settletransactions['date']); ?></td>
                            <td><?php echo htmlspecialchars($settletransactions['description'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($settletransactions['amount']); ?></td>
                            <td><?php echo htmlspecialchars($settletransactions['type']); ?></td>
                            <td><?php echo htmlspecialchars($settletransactions['added_by_name']); ?></td>
                            <td><?php echo htmlspecialchars($settletransactions['for_name']); ?></td>
                            <td><a href="dashboard.php?unsettle=<?php echo $settletransactions['id']; ?>" class="button unsettle">Unsettle</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>MoneyGuru by Ganpat Patel, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar</p>
        <p>Project Goal: A web app to track money lending and borrowing transactions</p>
    </div>

</body>
</html>
