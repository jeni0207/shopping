<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$stmt = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="dashboard.php" class="nav-btn"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="products.php" class="nav-btn"><i class="fa-solid fa-box"></i> Products</a>
            <a href="coupons.php" class="nav-btn"><i class="fa-solid fa-ticket"></i> Coupons</a>
            <a href="orders.php" class="nav-btn"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
            <a href="users.php" class="nav-btn"><i class="fa-solid fa-user"></i> Users</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding: 40px 0;">
    <div class="section-heading">
        <h2>Manage Categories</h2>
        <a href="add_category.php" class="primary-btn" style="text-decoration:none;">+ Add Category</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="empty">No categories yet.</div>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><img src="<?php echo htmlspecialchars($c['image']); ?>" width="50" height="50" style="object-fit:cover; border-radius:8px;"></td>
                <td><?php echo htmlspecialchars($c['name']); ?></td>
                <td style="max-width:250px;"><?php echo htmlspecialchars($c['description']); ?></td>
                <td>
                    <?php if ($c['status'] === 'active'): ?>
                        <span style="color:#218838; font-weight:700;">Active</span>
                    <?php else: ?>
                        <span style="color:#e74c3c; font-weight:700;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <a href="edit_category.php?id=<?php echo $c['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                        <a href="delete_category.php?id=<?php echo $c['id']; ?>" onclick="return confirm('Delete this category?');" class="btn-delete"><i class="fa-solid fa-trash"></i> Delete</a>
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