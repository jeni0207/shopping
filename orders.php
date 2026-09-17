<?php
require_once "includes/auth.php";
require_once "config/db.php";
requireLogin();
checkIfBlocked($conn);

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([currentUserId()]);
$orders = $stmt->fetchAll();

$paymentLabels = ['cod' => 'Cash on Delivery', 'upi' => 'UPI', 'card' => 'Credit/Debit Card'];
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0;">

    <h1 style="margin-bottom: 25px;">My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="empty">You haven't placed any orders yet. <a href="products.php">Start shopping</a></div>
    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            <?php
            $itemCount = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
            $itemCount->execute([$order['id']]);
            $count = $itemCount->fetchColumn();
            ?>
            <div style="background:#fff; border:1px solid #eee; border-radius:14px; padding:20px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">

                <div>
                    <div style="font-weight:700;">Order #<?php echo $order['id']; ?></div>
                    <div class="muted"><?php echo date("d M Y", strtotime($order['created_at'])); ?> • <?php echo $count; ?> item<?php echo $count != 1 ? 's' : ''; ?></div>
                </div>

                <div style="text-align:right;">
                    <div style="font-weight:700;">₹<?php echo number_format($order['total_amount'], 0); ?></div>
                    <div class="muted"><?php echo $paymentLabels[$order['payment_method']] ?? strtoupper($order['payment_method']); ?></div>
                </div>

                <div>
                    <span style="padding:6px 12px; border-radius:20px; font-size:12px; font-weight:700; background:
                        <?php
                        echo match($order['status']) {
                            'pending' => '#fff3cd; color:#856404',
                            'processing' => '#cce5ff; color:#004085',
                            'shipped' => '#d1ecf1; color:#0c5460',
                            'delivered' => '#d4edda; color:#155724',
                            'cancelled' => '#f8d7da; color:#721c24',
                            default => '#eee; color:#333'
                        };
                        ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>

                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="nav-btn" style="text-decoration:none;">View Details</a>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</section>

<?php include "includes/footer.php"; ?>