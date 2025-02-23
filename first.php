<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">BookHUB</h1>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:underline">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php" class="hover:underline">Dashboard</a></li>
                    <li><a href="logout.php" class="hover:underline">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="hover:underline">Login</a></li>
                    <li><a href="register.php" class="hover:underline">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-4">Welcome to BookHUB</h2>
        <p class="text-lg">Your one-stop solution for book ordering and delivery.</p>
    </div>
</body>
</html>

