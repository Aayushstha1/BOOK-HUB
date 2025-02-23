<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User clicked logout
    session_destroy();
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - BookHUB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .button-container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-logout {
            background-color: #f44336;
            color: white;
        }
        .btn-logout:hover {
            background-color: #d32f2f;
        }
        .btn-stay {
            background-color: #4CAF50;
            color: white;
        }
        .btn-stay:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="logout-box">
        <h2>Are you sure you want to logout?</h2>
        <div class="button-container">
            <!-- Stay Logged In Button -->
            <form action="logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-stay">Stay Logged In</button>
            </form>

            <!-- Logout Button -->
            <form action="logout.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-logout">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
