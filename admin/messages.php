<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

if (isset($_GET['read'])) {
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$_GET['read']]);
    header("Location: messages.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: messages.php");
    exit;
}

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
    <h2 style="margin-bottom:25px;">Contact Messages</h2>

    <?php if (empty($messages)): ?>
        <div class="empty">No messages yet.</div>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
        <div style="background:#fff; border:1px solid #eee; border-radius:14px; padding:20px; margin-bottom:14px; <?php echo $m['status'] === 'unread' ? 'border-left:4px solid #ff5a3c;' : ''; ?>">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <strong><?php echo htmlspecialchars($m['name']); ?></strong>
                    <span style="color:#888; font-size:13px;"> — <?php echo htmlspecialchars($m['email']); ?></span>
                    <?php if ($m['status'] === 'unread'): ?>
                        <span style="background:#fff0ec; color:#ff5a3c; font-size:11px; padding:2px 8px; border-radius:10px; margin-left:8px;">NEW</span>
                    <?php endif; ?>
                </div>
                <small style="color:#999;"><?php echo date("d M Y, g:i A", strtotime($m['created_at'])); ?></small>
            </div>
            <p style="margin-top:10px; color:#444; line-height:1.6;"><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
            <div class="admin-actions" style="margin-top:12px;">
                <?php if ($m['status'] === 'unread'): ?>
                    <a href="messages.php?read=<?php echo $m['id']; ?>" class="btn-edit"><i class="fa-solid fa-envelope-open"></i> Mark Read</a>
                <?php endif; ?>
                <a href="messages.php?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this message?');" class="btn-delete"><i class="fa-solid fa-trash"></i> Delete</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</body>
</html>