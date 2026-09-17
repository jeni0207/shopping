<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$stmt = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Messages</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="dashboard.php" class="nav-btn"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding: 40px 0;">
    <h2 style="margin-bottom:20px;">Contact Messages</h2>

    <?php if (empty($messages)): ?>
        <div class="empty">No messages yet.</div>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
            <div style="background:#fff; border:1px solid #eee; border-radius:14px; padding:20px; margin-bottom:15px;">
                <div style="display:flex; justify-content:space-between;">
                    <strong><?php echo htmlspecialchars($m['name']); ?></strong>
                    <span class="muted"><?php echo date("d M Y, h:i A", strtotime($m['created_at'])); ?></span>
                </div>
                <p class="muted" style="margin:5px 0;"><?php echo htmlspecialchars($m['email']); ?></p>
                <p style="font-weight:700; margin-top:10px;"><?php echo htmlspecialchars($m['subject']); ?></p>
                <p style="margin-top:5px;"><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</body>
</html>