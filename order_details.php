<?php
require_once "includes/auth.php";
require_once "config/db.php";
requireLogin();
checkIfBlocked($conn);

$orderId = $_GET['id'] ?? null;
if (!$orderId) { header("Location: orders.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, currentUserId()]);
$order = $stmt->fetch();

if (!$order) { header("Location: orders.php"); exit; }

$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$paymentLabels = ['cod' => 'Cash on Delivery', 'upi' => 'UPI', 'card' => 'Credit/Debit Card'];
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0; max-width: 700px;">

    <a href="orders.php" class="text-btn">← Back to Orders</a>

    <h1 style="margin: 15px 0 5px;">Order #<?php echo $order['id']; ?></h1>
    <p class="muted">Placed on <?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?></p>

    <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px; margin-top:20px;">

        <h3 style="margin-bottom:10px;">Items</h3>
        <?php foreach ($items as $item): ?>
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px;">
                <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                <span>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
            </div>
        <?php endforeach; ?>

        <div class="cart-total" style="margin-top:15px;">
            <span>Total</span>
            <span>₹<?php echo number_format($order['total_amount'], 0); ?></span>
        </div>

        <hr style="border:none; border-top:1px solid #eee; margin: 15px 0;">

        <p style="font-size:14px;"><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
        <p style="font-size:14px;"><strong>Payment Method:</strong> <?php echo $paymentLabels[$order['payment_method']] ?? strtoupper($order['payment_method']); ?></p>
        <p style="font-size:14px;"><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?></p>
        <p style="font-size:14px;"><strong>Contact:</strong> <?php echo htmlspecialchars($order['email']); ?>, <?php echo htmlspecialchars($order['phone']); ?></p>

    </div>

</section>

<?php include "includes/footer.php"; ?>