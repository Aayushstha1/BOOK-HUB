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

// Fetch order details
$sql = "SELECT o.*, oi.book_id, oi.quantity, b.title, b.price 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN books b ON oi.book_id = b.id 
        WHERE o.id = ? AND o.user_id = ? AND o.status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error_message'] = "Invalid order or order cannot be edited.";
    header("Location: customer_dashboard.php");
    exit();
}

// Fetch all books
$sql = "SELECT * FROM books";
$stmt = $pdo->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_book_id = $_POST['book_id'];
    $new_quantity = $_POST['quantity'];

    // Fetch new book price
    $sql = "SELECT price FROM books WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_book_id]);
    $new_book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($new_book) {
        $new_total_amount = $new_book['price'] * $new_quantity;

        // Start transaction
        $pdo->beginTransaction();

        try {
            // Update order
            $sql = "UPDATE orders SET total_amount = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_total_amount, $order_id]);

            // Update order item
            $sql = "UPDATE order_items SET book_id = ?, quantity = ?, price = ? WHERE order_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_book_id, $new_quantity, $new_book['price'], $order_id]);

            // Commit transaction
            $pdo->commit();

            $_SESSION['success_message'] = "Order updated successfully!";
            header("Location: customer_dashboard.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $error_message = "Error updating order: " . $e->getMessage();
        }
    } else {
        $error_message = "Invalid book selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Edit Order</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="edit_order.php?id=<?php echo $order_id; ?>" method="post" class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="book_id" class="form-label">Select Book:</label>
                    <select name="book_id" id="book_id" required class="form-select">
                        <?php foreach ($books as $book): ?>
                            <option value="<?php echo $book['id']; ?>" <?php echo $book['id'] == $order['book_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($book['title']); ?> - $<?php echo number_format($book['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required min="1" value="<?php echo $order['quantity']; ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update Order</button>
                <a href="customer_dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

