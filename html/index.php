<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGuru - Home</title>
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

        .features {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }

        .feature {
            text-align: center;
            flex: 1 1 150px;
            max-width: 200px;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .feature i {
            font-size: 40px;
            color: #007BFF;
            margin-bottom: 15px;
        }

        .feature h3 {
            margin: 10px 0;
        }

        .feature p {
            color: #555;
            font-size: 0.9em;
        }

        .auth-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .auth-buttons .button {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 15px 25px;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .auth-buttons .button:hover {
            background-color: #0056b3;
        }

        .footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <div class="header">
    <a href="index.php" style="text-decoration: none;color: inherit;"><h1>💸 MoneyGuru 💰</h1></a>
        <p>Your Personal Money Lending and Borrowing Tracker</p>
    </div>

    <div class="features">
        <div class="feature">
            <i class="fas fa-tachometer-alt"></i>
            <h3>Dashboard</h3>
            <p>Manage your transactions: View, Edit, Delete, and get Insights all in one place.</p>
        </div>
        <div class="feature">
            <i class="fas fa-exchange-alt"></i>
            <h3>Add Transactions</h3>
            <p>Easily track money lending and borrowing transactions.</p>
        </div>
        <div class="feature">
            <i class="fas fa-check-circle"></i>
            <h3>Mark as Settled</h3>
            <p>Mark transactions as settled once the debts are cleared.</p>
        </div>
    </div>

    <div class="auth-buttons">
        <a href="register.php" class="button">Register</a>
        <a href="login.php" class="button">Login</a>
    </div>

    <div class="footer">
        <p>MoneyGuru by Ganpat Patel, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar</p>
        <p>Project Goal: A web app to track money lending and borrowing transactions</p>
    </div>

</body>
</html>
