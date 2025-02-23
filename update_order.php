<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'delivery_boy')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $order_id]);

    if ($_SESSION['user_type'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: delivery_dashboard.php");
    }
    exit();
}

