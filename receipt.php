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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - Order #<?php echo $order['id']; ?></title>
<style>
    * { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: Arial, sans-serif; color:#171717; padding: 40px; background:#fff; }
    .receipt-box { max-width: 700px; margin: auto; border: 1px solid #eee; border-radius: 12px; padding: 30px; }
    .header { display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #171717; padding-bottom: 15px; margin-bottom: 20px; }
    .logo { font-size: 24px; font-weight: 800; }
    .logo span { color: #ff5a3c; }
    .meta { display:flex; justify-content:space-between; margin-bottom: 20px; font-size: 14px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
    th { background: #171717; color: #fff; }
    .text-right { text-align: right; }
    .total-row { font-weight: 700; font-size: 16px; }
    .info p { font-size: 14px; margin-bottom: 6px; }
    .print-btn { display:block; margin: 25px auto 0; background:#ff5a3c; color:#fff; border:none; padding: 12px 24px; border-radius: 8px; font-size: 15px; cursor: pointer; }
    .print-btn:hover { background:#171717; }
    @media print {
        .print-btn { display: none; }
        body { padding: 0; }
        .receipt-box { border: none; }
    }
</style>
</head>
<body>

<div class="receipt-box">

    <div class="header">
        <div class="logo"><span>Shop</span>Ease</div>
        <div style="font-size:13px; color:#777;">Order Receipt</div>
    </div>

    <div class="meta">
        <div><strong>Order ID:</strong> #<?php echo $order['id']; ?></div>
        <div><strong>Date:</strong> <?php echo date("d M Y", strtotime($order['created_at'])); ?></div>
    </div>

    <div class="info">
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> - <?php echo htmlspecialchars($order['pincode']); ?></p>
    </div>

    <br>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td class="text-right"><?php echo $item['quantity']; ?></td>
                <td class="text-right">₹<?php echo number_format($item['price'], 0); ?></td>
                <td class="text-right">₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">₹<?php echo number_format($order['total_amount'], 0); ?></td>
            </tr>
        </tbody>
    </table>

    <p style="font-size:14px;"><strong>Payment Method:</strong> <?php echo $paymentLabels[$order['payment_method']] ?? strtoupper($order['payment_method']); ?></p>
    <p style="font-size:14px;"><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>

    <p style="text-align:center; margin-top:25px; color:#999; font-size:13px;">Thank you for shopping with ShopEase!</p>

    <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>

</div>

</body>
</html>