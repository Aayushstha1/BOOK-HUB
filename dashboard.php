<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_type = $_SESSION['user_type'];

// Fetch orders based on user type
if ($user_type == 'customer') {
    $sql = "SELECT * FROM orders WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
} elseif ($user_type == 'admin' || $user_type == 'delivery_boy') {
    $sql = "SELECT * FROM orders";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8">
        <h2 class="text-3xl font-bold mb-4">Dashboard</h2>
        <?php if ($user_type == 'customer'): ?>
            <h3 class="text-xl font-semibold mb-4">Place an Order</h3>
            <form action="place_order.php" method="post" class="mb-8">
                <div class="mb-4">
                    <label for="book_title" class="block mb-2">Book Title:</label>
                    <input type="text" id="book_title" name="book_title" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block mb-2">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required class="w-full px-3 py-2 border rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Place Order</button>
            </form>
        <?php endif; ?>

        <h3 class="text-xl font-semibold mb-4">Orders</h3>
        <table class="w-full bg-white shadow-md rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 text-left">Order ID</th>
                    <th class="py-2 px-4 text-left">Book Title</th>
                    <th class="py-2 px-4 text-left">Quantity</th>
                    <th class="py-2 px-4 text-left">Status</th>
                    <?php if ($user_type == 'admin' || $user_type == 'delivery_boy'): ?>
                        <th class="py-2 px-4 text-left">Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="py-2 px-4"><?php echo $order['id']; ?></td>
                        <td class="py-2 px-4"><?php echo $order['book_title']; ?></td>
                        <td class="py-2 px-4"><?php echo $order['quantity']; ?></td>
                        <td class="py-2 px-4"><?php echo $order['status']; ?></td>
                        <?php if ($user_type == 'admin' || $user_type == 'delivery_boy'): ?>
                            <td class="py-2 px-4">
                                <form action="update_order.php" method="post">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="px-2 py-1 border rounded">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 ml-2">Update</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

