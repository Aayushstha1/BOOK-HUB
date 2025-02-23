<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $quantity = $_POST['quantity'];

    // Fetch book price
    $sql = "SELECT price FROM books WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $total_amount = $book['price'] * $quantity;

        // Start transaction
        $pdo->beginTransaction();

        try {
            // Insert order
            $sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $pdo->lastInsertId();

            // Insert order item
            $sql = "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$order_id, $book_id, $quantity, $book['price']]);

            // Commit transaction
            $pdo->commit();

            $_SESSION['success_message'] = "Order placed successfully!";
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $_SESSION['error_message'] = "Error placing order: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid book selected.";
    }

    header("Location: customer_dashboard.php");
    exit();
}

