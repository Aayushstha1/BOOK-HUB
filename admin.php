<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch necessary data for dashboard
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM orders";
$stmt_total_orders = $pdo->query($sql_total_orders);
$total_orders = $stmt_total_orders->fetch(PDO::FETCH_ASSOC)['total_orders'];

$sql_total_books = "SELECT COUNT(*) as total_books FROM books";
$stmt_total_books = $pdo->query($sql_total_books);
$total_books = $stmt_total_books->fetch(PDO::FETCH_ASSOC)['total_books'];

$sql_total_customers = "SELECT COUNT(*) as total_customers FROM users WHERE user_type = 'customer'";
$stmt_total_customers = $pdo->query($sql_total_customers);
$total_customers = $stmt_total_customers->fetch(PDO::FETCH_ASSOC)['total_customers'];

$sql_total_revenue = "SELECT SUM(total_amount) as total_revenue FROM orders";
$stmt_total_revenue = $pdo->query($sql_total_revenue);
$total_revenue = $stmt_total_revenue->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

// Fetch all orders
$sql_orders = "SELECT o.*, u.username, GROUP_CONCAT(b.title SEPARATOR ', ') as order_items 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               JOIN order_items oi ON o.id = oi.order_id 
               JOIN books b ON oi.book_id = b.id
               GROUP BY o.id 
               ORDER BY o.created_at DESC";
$stmt_orders = $pdo->query($sql_orders);
$orders = $stmt_orders->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #007bff;
        }
        .navbar a {
            color: #fff;
        }
        .card {
            border-radius: 10px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .table thead {
            background-color: #f1f1f1;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
        <nav class="sidebar p-3 flex-column">
            <a href="#" class="text-decoration-none text-white mb-4 fs-4 d-flex align-items-center">
                <i class='bx bxs-book fs-3 me-2'></i> <span>BookHub Admin</span>
            </a>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="admin_dashboard.php" class="nav-link text-white active">
                        <i class='bx bxs-dashboard'></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="orders.php" class="nav-link text-white">
                        <i class='bx bxs-box'></i> Orders
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="books.php" class="nav-link text-white">
                        <i class='bx bxs-book'></i> Books
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="customers.php" class="nav-link text-white">
                        <i class='bx bxs-user'></i> Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">
                        <i class='bx bxs-log-out-circle'></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- MAIN CONTENT -->
        <div class="w-100">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light px-4 shadow-sm">
                <a class="navbar-brand text-white" href="#">BookHub Admin</a>
                <div class="collapse navbar-collapse">
                    <form class="d-flex ms-auto">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">Search</button>
                    </form>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="container mt-4">
                <h1 class="mb-4">Admin Dashboard</h1>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Total Orders</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2 class="display-4 mb-0"><?php echo $total_orders; ?></h2>
                                <p class="text-muted mt-2">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Total Books</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2 class="display-4 mb-0"><?php echo $total_books; ?></h2>
                                <p class="text-muted mt-2">Books Available</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Total Customers</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2 class="display-4 mb-0"><?php echo $total_customers; ?></h2>
                                <p class="text-muted mt-2">Customers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Total Revenue</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2 class="display-4 mb-0">$<?php echo number_format($total_revenue, 2); ?></h2>
                                <p class="text-muted mt-2">Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">All Orders</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Order Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_items']); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><?php echo ucfirst($order['status']); ?></td>
                                        <td><?php echo $order['created_at']; ?></td>
                                        <td>
                                            <form action="update_order.php" method="post">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>