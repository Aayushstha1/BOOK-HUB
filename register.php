<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $error = "Username or email already exists.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $hashed_password, $email, $user_type])) {
            $_SESSION['success_message'] = "Registration successful. You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-form {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: 0 20px;
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
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .btn-register {
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
        .btn-register:hover {
            background-color: #45a049;
        }
        .error-message {
            color: #f44336;
            font-size: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .success-message {
            color: #4CAF50;
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
        <div class="register-form">
            <h2>Create Your Account</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="user_type">User Type</label>
                    <select id="user_type" name="user_type" required>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                        <option value="delivery_boy">Delivery Boy</option>
                    </select>
                </div>
                <button type="submit" class="btn-register">Register</button>
            </form>
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
