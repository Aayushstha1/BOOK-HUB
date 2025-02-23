<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header("Location: customer_dashboard.php");
    exit();
}

// Check if the order belongs to the user and is pending
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    // Start transaction
    $pdo->beginTransaction();

    try {
        // Delete order items
        $sql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);

        // Delete order
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id]);

        // Commit transaction
        $pdo->commit();

        $_SESSION['success_message'] = "Order deleted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error deleting order: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid order or order cannot be deleted.";
}

header("Location: customer_dashboard.php");
exit();

