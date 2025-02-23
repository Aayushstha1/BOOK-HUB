<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch customer's information
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch available books
$sql = "SELECT * FROM books";
$stmt = $pdo->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer's orders
$sql = "SELECT o.*, GROUP_CONCAT(CONCAT(b.title, ' (', oi.quantity, ')') SEPARATOR ', ') as book_titles 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN books b ON oi.book_id = b.id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug logging
error_log("User ID: " . $user_id);
error_log("Orders query: " . $sql);
error_log("Orders result: " . print_r($orders, true));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">BookHUB</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Place an Order</h3>
                        <form action="place_order.php" method="post">
                            <div class="mb-3">
                                <label for="book_id" class="form-label">Select Book:</label>
                                <select name="book_id" id="book_id" required class="form-select">
                                    <?php foreach ($books as $book): ?>
                                        <option value="<?php echo $book['id']; ?>">
                                            <?php echo htmlspecialchars($book['title']); ?> - $<?php echo number_format($book['price'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" required min="1" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="card-title">Your Orders</h3>
                        <?php if (empty($orders)): ?>
                            <p>You haven't placed any orders yet.</p>
                        <?php else: ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Books</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['book_titles']); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><?php echo ucfirst($order['status']); ?></td>
                                            <td><?php echo $order['created_at']; ?></td>
                                            <td>
                                                <?php if ($order['status'] == 'pending'): ?>
                                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Messages section (unchanged) -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

