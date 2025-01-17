<?php
session_start();

if (!isset($_SESSION['userid'])) {
    echo json_encode([]); // Return an empty result if the user is not logged in
    exit();
}

// Database connection
$servername = "db";  
$username = "root";   
$password = "root_password";  
$dbname = "moneyguru";  

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Get the search term from the query parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$current_userid = $_SESSION['userid'];

// If search is empty, return an empty list
if (empty($search)) {
    echo json_encode([]); // Return an empty list
    exit();
}

// Query to fetch users excluding the current user and matching the search term
$sql = "SELECT userid, CONCAT(name, '(', email, ')') AS display_name 
        FROM users 
        WHERE userid != ? AND name LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
$search_term = '%' . $search . '%';
mysqli_stmt_bind_param($stmt, "is", $current_userid, $search_term);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Return the results as JSON
echo json_encode($users);
?>
