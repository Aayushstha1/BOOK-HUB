<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'delivery_boy') {
    header("Location: login.php");
    exit();
}

// Fetch orders that are ready for delivery (status: shipped)
$sql = "SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id WHERE orders.status = 'shipped' ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();
$order_count = count($orders); // To display the number of orders
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <!-- Container -->
    <div class="max-w-7xl mx-auto p-6">

        <!-- Heading Section -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-800">Delivery Dashboard</h2>
            <a href="logout.php" class="bg-red-500 text-white py-2 px-6 rounded-lg shadow-md hover:bg-red-600">Logout</a>
        </div>

        <!-- Orders Count Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-500 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all">
                <h3 class="text-white text-lg font-semibold">Total Orders Ready for Delivery</h3>
                <p class="text-white text-3xl font-bold mt-2"><?php echo $order_count; ?></p>
            </div>
        </div>

        <!-- Orders Table Section -->
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Orders Ready for Delivery</h3>

        <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-4">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Order ID</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Customer</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Book Title</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Quantity</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Order Date</th>
                        <th class="py-3 px-4 text-left text-gray-600 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="border-t hover:bg-gray-50 transition-all">
                            <td class="py-3 px-4 text-gray-700"><?php echo $order['id']; ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo $order['username']; ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo isset($order['book_title']) ? $order['book_title'] : 'N/A'; ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo isset($order['quantity']) ? $order['quantity'] : 'N/A'; ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></td>
                            <td class="py-3 px-4">
                                <form action="update_order.php" method="post">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-md shadow-md hover:bg-green-600 focus:outline-none transition-all">Mark as Delivered</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
