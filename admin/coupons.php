<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$stmt = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC");
$coupons = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Coupons</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="dashboard.php" class="nav-btn"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="categories.php" class="nav-btn"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="products.php" class="nav-btn"><i class="fa-solid fa-box"></i> Products</a>
            <a href="orders.php" class="nav-btn"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
            <a href="users.php" class="nav-btn"><i class="fa-solid fa-user"></i> Users</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding: 40px 0;">
    <div class="section-heading">
        <h2>Manage Coupons</h2>
        <a href="add_coupon.php" class="primary-btn" style="text-decoration:none;">+ Add Coupon</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if (empty($coupons)): ?>
        <div class="empty">No coupons yet.</div>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Discount</th>
                <th>Min Order</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $c): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($c['code']); ?></strong></td>
                <td><?php echo $c['discount_percent']; ?>%</td>
                <td>₹<?php echo number_format($c['min_order_amount'], 0); ?></td>
                <td><?php echo $c['expiry_date'] ? date("d M Y", strtotime($c['expiry_date'])) : '—'; ?></td>
                <td>
                    <?php if ($c['status'] === 'active'): ?>
                        <span style="color:#218838; font-weight:700;">Active</span>
                    <?php else: ?>
                        <span style="color:#e74c3c; font-weight:700;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <a href="edit_coupon.php?id=<?php echo $c['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                        <a href="delete_coupon.php?id=<?php echo $c['id']; ?>" onclick="return confirm('Delete this coupon?');" class="btn-delete"><i class="fa-solid fa-trash"></i> Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

</body>
</html>