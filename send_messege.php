<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? '';

    if ($receiver_id && $message) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $message]);

        $_SESSION['success_message'] = "Message sent successfully!";
    } else {
        $_SESSION['error_message'] = "Error sending message. Please try again.";
    }

    // Redirect back to the appropriate dashboard
    if ($_SESSION['user_type'] == 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['user_type'] == 'customer') {
        header("Location: customer_dashboard.php");
    } elseif ($_SESSION['user_type'] == 'delivery_boy') {
        header("Location: delivery_dashboard.php");
    }
    exit();
}

