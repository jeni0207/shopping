<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($userId && $action === 'block') {
        $stmt = $conn->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
        $stmt->execute([$userId]);
    }
    if ($userId && $action === 'unblock') {
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->execute([$userId]);
    }

    header("Location: users.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
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
            <a href="coupons.php" class="nav-btn"><i class="fa-solid fa-ticket"></i> Coupons</a>
            <a href="products.php" class="nav-btn"><i class="fa-solid fa-box"></i> Products</a>
            <a href="orders.php" class="nav-btn"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding: 40px 0;">
    <h2 style="margin-bottom:20px;">Registered Customers</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="empty">No registered customers yet.</div>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo date("d M Y", strtotime($u['created_at'])); ?></td>
                <td>
                    <?php if ($u['status'] === 'blocked'): ?>
                        <span style="color:#e74c3c; font-weight:700;">Blocked</span>
                    <?php else: ?>
                        <span style="color:#218838; font-weight:700;">Active</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions" style="flex-wrap:wrap;">
                        <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>

                        <form method="POST" action="users.php" style="display:inline;" onsubmit="return confirm('<?php echo $u['status'] === 'blocked' ? 'Unblock' : 'Block'; ?> this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <?php if ($u['status'] === 'blocked'): ?>
                                <input type="hidden" name="action" value="unblock">
                                <button type="submit" class="btn-edit"><i class="fa-solid fa-unlock"></i> Unblock</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="block">
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-ban"></i> Block</button>
                            <?php endif; ?>
                        </form>

                        <a href="delete_user.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Permanently delete this user and all their orders?');" class="btn-delete"><i class="fa-solid fa-trash"></i> Delete</a>
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