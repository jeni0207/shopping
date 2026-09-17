<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="categories.php" class="nav-btn"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="coupons.php" class="nav-btn"><i class="fa-solid fa-ticket"></i> Coupons</a>
            <a href="products.php" class="nav-btn"><i class="fa-solid fa-box"></i> Products</a>
            <a href="orders.php" class="nav-btn"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
            <a href="users.php" class="nav-btn"><i class="fa-solid fa-user"></i> Users</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding: 40px 0;">
    <h1 style="margin-bottom: 25px;">Dashboard</h1>
    <div class="benefits-grid" style="color:#171717; grid-template-columns: repeat(5, 1fr);">
        <div class="category-card"><i class="fa-solid fa-tags"></i><span>Categories: <?php echo $totalCategories; ?></span></div>
        <div class="category-card"><i class="fa-solid fa-box"></i><span>Products: <?php echo $totalProducts; ?></span></div>
        <div class="category-card"><i class="fa-solid fa-user"></i><span>Customers: <?php echo $totalUsers; ?></span></div>
        <div class="category-card"><i class="fa-solid fa-clipboard-list"></i><span>Orders: <?php echo $totalOrders; ?></span></div>
        <div class="category-card"><i class="fa-solid fa-indian-rupee-sign"></i><span>Revenue: ₹<?php echo number_format($totalRevenue, 0); ?></span></div>
    </div>
</section>

</body>
</html>