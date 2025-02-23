<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        
        // Debug information
        error_log("User logged in - ID: " . $user['id'] . ", Type: " . $user['user_type']);

        // Redirect to the appropriate dashboard
        switch ($user['user_type']) {
            case 'customer':
                header("Location: customer_dashboard.php");
                break;
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'delivery_boy':
                header("Location: delivery_dashboard.php");
                break;
            default:
                error_log("Unknown user type: " . $user['user_type']);
                $error = "Invalid user type";
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            font-size: 1.1rem;
            margin-bottom: 8px;
            display: block;
            color: #444;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .input-group input:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .btn-login {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #45a049;
        }
        .error-message {
            color: #f44336;
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .form-footer {
            text-align: center;
            margin-top: 20px;
        }
        .form-footer a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Login to BookHUB</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
