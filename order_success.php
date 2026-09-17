<?php
require_once "includes/auth.php";
require_once "config/db.php";
requireLogin();
checkIfBlocked($conn);

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, currentUserId()]);
$order = $stmt->fetch();

if (!$order) { header("Location: index.php"); exit; }

$itemStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$paymentLabels = ['cod' => 'Cash on Delivery', 'upi' => 'UPI', 'card' => 'Credit/Debit Card'];
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 60px 0; max-width: 700px; text-align:center;">

    <i class="fa-solid fa-circle-check" style="font-size:60px; color:#218838;"></i>
    <h1 style="margin: 15px 0 5px;">Order Placed Successfully!</h1>
    <p class="muted">Thank you, <?php echo htmlspecialchars($order['customer_name']); ?>. Your order has been received.</p>

    <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px; margin-top:30px; text-align:left;">

        <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <span><strong>Order ID:</strong> #<?php echo $order['id']; ?></span>
            <span><strong>Date:</strong> <?php echo date("d M Y", strtotime($order['created_at'])); ?></span>
        </div>

        <hr style="border:none; border-top:1px solid #eee; margin-bottom:15px;">

        <h3 style="margin-bottom:10px;">Items Ordered</h3>
        <?php foreach ($items as $item): ?>
            <div style="display:flex; justify-content:space-between; padding:6px 0; font-size:14px;">
                <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                <span>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
            </div>
        <?php endforeach; ?>

        <div class="cart-total" style="margin-top:15px;">
            <span>Total</span>
            <span>₹<?php echo number_format($order['total_amount'], 0); ?></span>
        </div>

        <hr style="border:none; border-top:1px solid #eee; margin: 15px 0;">
        <?php if (!empty($order['coupon_code'])): ?>
    <p style="font-size:14px; color:#218838;"><strong>Coupon Applied:</strong> <?php echo htmlspecialchars($order['coupon_code']); ?> (Saved ₹<?php echo number_format($order['discount_amount'], 0); ?>)</p>
<?php endif; ?>

        <p style="font-size:14px;"><strong>Payment Method:</strong> <?php echo $paymentLabels[$order['payment_method']] ?? strtoupper($order['payment_method']); ?></p>
        <p style="font-size:14px;"><strong>Shipping to:</strong> <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?></p>

    </div>

    <div style="display:flex; gap:15px; margin-top:30px; justify-content:center; flex-wrap:wrap;">
        <a href="index.php" class="secondary-btn" style="text-decoration:none;">Continue Shopping</a>
        <a href="receipt.php?id=<?php echo $order['id']; ?>" class="secondary-btn" style="text-decoration:none;"><i class="fa-solid fa-download"></i> Download Receipt</a>
        <a href="orders.php" class="primary-btn" style="text-decoration:none;">View Order History</a>
    </div>

</section>

<?php include "includes/footer.php"; ?>