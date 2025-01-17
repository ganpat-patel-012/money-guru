<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];

$servername = "db";
$username = "root";
$password = "root_password";
$dbname = "moneyguru";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];

    $sql = "SELECT userid_added FROM Transactions WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $transaction_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $transaction = mysqli_fetch_assoc($result);

    if ($transaction && $transaction['userid_added'] == $user_id) {
        $delete_sql = "DELETE FROM Transactions WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $transaction_id);
        mysqli_stmt_execute($delete_stmt);

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: dashboard.php?error=unauthorized");
        exit();
    }
}

mysqli_close($conn);
?>
