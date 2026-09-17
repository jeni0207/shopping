<?php
require_once "includes/auth.php";
require_once "config/db.php";
requireLogin();
checkIfBlocked($conn);

$userId = currentUserId();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$cartItems = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $pid = (int)$product['id'];
        $qty = (int)($_SESSION['cart'][$pid] ?? 1);
        $lineSubtotal = $product['price'] * $qty;

        $cartItems[] = [
            'product_id' => $pid,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $qty,
            'subtotal' => $lineSubtotal
        ];

        $subtotal += $lineSubtotal;
    }
}

if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

// ============ COUPON HANDLING ============
$couponMessage = "";
$couponMessageType = "";
$discountAmount = 0;
$appliedCoupon = $_SESSION['applied_coupon'] ?? null;

// Apply coupon button clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['apply_coupon'])) {
    $couponCode = strtoupper(trim($_POST['coupon_code']));

    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active'");
    $stmt->execute([$couponCode]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        $couponMessage = "Invalid or inactive coupon code.";
        $couponMessageType = "error";
        unset($_SESSION['applied_coupon']);
    } elseif ($coupon['expiry_date'] && strtotime($coupon['expiry_date']) < strtotime(date('Y-m-d'))) {
        $couponMessage = "This coupon has expired.";
        $couponMessageType = "error";
        unset($_SESSION['applied_coupon']);
    } elseif ($subtotal < $coupon['min_order_amount']) {
        $couponMessage = "Minimum order of ₹" . number_format($coupon['min_order_amount'], 0) . " required for this coupon.";
        $couponMessageType = "error";
        unset($_SESSION['applied_coupon']);
    } else {
        $_SESSION['applied_coupon'] = [
            'code' => $coupon['code'],
            'discount_percent' => $coupon['discount_percent']
        ];
        $couponMessage = "Coupon applied! You saved " . $coupon['discount_percent'] . "%.";
        $couponMessageType = "success";
        $appliedCoupon = $_SESSION['applied_coupon'];
    }
}

// Remove coupon button clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    $appliedCoupon = null;
}

// Calculate discount if coupon is applied
if ($appliedCoupon) {
    $discountAmount = ($subtotal * $appliedCoupon['discount_percent']) / 100;
}

$total = $subtotal - $discountAmount;

// ============ PLACE ORDER ============
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $pincode = trim($_POST["pincode"]);
    $paymentMethod = $_POST["payment_method"];

    if ($name === "" || $email === "" || $phone === "" || $address === "" || $city === "" || $state === "" || $pincode === "") {
        $message = "All fields are required.";
    } else {
        $conn->beginTransaction();

        try {
            $couponCodeToSave = $appliedCoupon ? $appliedCoupon['code'] : null;

            $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, email, total_amount, payment_method, address, city, state, pincode, phone, coupon_code, discount_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $email, $total, $paymentMethod, $address, $city, $state, $pincode, $phone, $couponCodeToSave, $discountAmount]);

            $orderId = $conn->lastInsertId();

            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']]);
            }

            $conn->commit();

            $_SESSION['cart'] = [];
            unset($_SESSION['applied_coupon']);

            header("Location: order_success.php?order_id=" . $orderId);
            exit;

        } catch (Exception $e) {
            $conn->rollBack();
            $message = "Something went wrong placing your order. Please try again.";
        }
    }
}
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0; max-width: 900px;">

    <h1 style="margin-bottom: 25px;">Checkout</h1>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns: 1.3fr 1fr; gap:30px;">

        <form method="POST" action="checkout.php" style="background:#fff; padding:25px; border-radius:16px; border:1px solid #eee;">

            <h3 style="margin-bottom:15px;">Shipping Details</h3>

            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="field">
                <label>Mobile Number</label>
                <input type="text" name="phone" required pattern="[0-9]{10}" maxlength="10" placeholder="e.g. 9876543210" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="field">
                <label>Address</label>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div class="field">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="field">
                    <label>State</label>
                    <input type="text" name="state" required>
                </div>
            </div>

            <div class="field">
                <label>Pincode</label>
                <input type="text" name="pincode" required pattern="[0-9]{6}" maxlength="6" placeholder="e.g. 395001">
            </div>

            <h3 style="margin: 20px 0 10px;">Payment Method</h3>

            <div class="field">
                <select name="payment_method" id="paymentMethod" required onchange="togglePaymentFields()">
                    <option value="cod">Cash on Delivery</option>
                    <option value="upi">UPI</option>
                    <option value="card">Credit / Debit Card</option>
                </select>
            </div>

            <div id="upiFields" class="hidden">
                <div class="field">
                    <label>UPI ID</label>
                    <input type="text" name="upi_id" placeholder="yourname@upi">
                </div>
            </div>

            <div id="cardFields" class="hidden">
                <div class="field">
                    <label>Card Number</label>
                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="field">
                        <label>Expiry (MM/YY)</label>
                        <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="field">
                        <label>CVV</label>
                        <input type="text" name="card_cvv" placeholder="123" maxlength="3">
                    </div>
                </div>
            </div>

            <button type="submit" name="place_order" class="primary-btn full" style="margin-top:15px;">
                Place Order — ₹<?php echo number_format($total, 0); ?>
            </button>

        </form>

        <div style="background:#fff; padding:25px; border-radius:16px; border:1px solid #eee; height:fit-content;">

            <h3 style="margin-bottom:15px;">Order Summary</h3>

            <?php foreach ($cartItems as $item): ?>
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px;">
                    <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span>₹<?php echo number_format($item['subtotal'], 0); ?></span>
                </div>
            <?php endforeach; ?>

            <!-- COUPON SECTION -->
            <div style="margin: 18px 0; padding: 15px 0; border-top: 1px dashed #ddd; border-bottom: 1px dashed #ddd;">

                <?php if ($couponMessage): ?>
                    <div class="message <?php echo $couponMessageType; ?>" style="margin-bottom:10px; padding:8px 10px; font-size:13px;">
                        <?php echo htmlspecialchars($couponMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($appliedCoupon): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; background:#eaf8ef; padding:10px; border-radius:8px;">
                        <span style="font-size:13px; color:#218838; font-weight:700;">
                            <i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($appliedCoupon['code']); ?> applied (<?php echo $appliedCoupon['discount_percent']; ?>% off)
                        </span>
                        <form method="POST" action="checkout.php">
                            <button type="submit" name="remove_coupon" style="border:none; background:none; color:#e74c3c; cursor:pointer; font-size:13px;">Remove</button>
                        </form>
                    </div>
                <?php else: ?>
                    <form method="POST" action="checkout.php" style="display:flex; gap:8px;">
                        <input type="text" name="coupon_code" placeholder="Enter coupon code" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:8px; text-transform:uppercase;">
                        <button type="submit" name="apply_coupon" class="secondary-btn" style="padding:10px 16px;">Apply</button>
                    </form>
                <?php endif; ?>

            </div>

            <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:8px;">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($subtotal, 0); ?></span>
            </div>

            <?php if ($discountAmount > 0): ?>
                <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:8px; color:#218838;">
                    <span>Discount</span>
                    <span>- ₹<?php echo number_format($discountAmount, 0); ?></span>
                </div>
            <?php endif; ?>

            <div class="cart-total" style="margin-top:10px;">
                <span>Total</span>
                <span>₹<?php echo number_format($total, 0); ?></span>
            </div>

        </div>

    </div>

</section>

<script>
function togglePaymentFields() {
    const method = document.getElementById('paymentMethod').value;
    document.getElementById('upiFields').classList.add('hidden');
    document.getElementById('cardFields').classList.add('hidden');

    if (method === 'upi') {
        document.getElementById('upiFields').classList.remove('hidden');
    } else if (method === 'card') {
        document.getElementById('cardFields').classList.remove('hidden');
    }
}
</script>

<?php include "includes/footer.php"; ?>