<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = strtoupper(trim($_POST["code"]));
    $discount = $_POST["discount_percent"];
    $minOrder = $_POST["min_order_amount"] !== '' ? $_POST["min_order_amount"] : 0;
    $status = $_POST["status"];
    $expiry = $_POST["expiry_date"] !== '' ? $_POST["expiry_date"] : null;

    if ($code === "" || $discount === "") {
        $message = "Coupon code and discount percent are required.";
    } elseif ($discount < 1 || $discount > 100) {
        $message = "Discount percent must be between 1 and 100.";
    } else {
        $check = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
        $check->execute([$code]);

        if ($check->fetch()) {
            $message = "This coupon code already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO coupons (code, discount_percent, min_order_amount, status, expiry_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$code, $discount, $minOrder, $status, $expiry]);
            header("Location: coupons.php?msg=Coupon added successfully");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Coupon</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="coupons.php" class="nav-btn"><i class="fa-solid fa-ticket"></i> Coupons</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding:40px 0; max-width:600px;">
    <h2 style="margin-bottom:20px;">Add New Coupon</h2>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="add_coupon.php">

        <div class="field">
            <label>Coupon Code</label>
            <input type="text" name="code" required placeholder="e.g. SAVE20" style="text-transform:uppercase;">
        </div>

        <div class="field">
            <label>Discount Percent (%)</label>
            <input type="number" name="discount_percent" required min="1" max="100" placeholder="e.g. 10">
        </div>

        <div class="field">
            <label>Minimum Order Amount (₹) — optional</label>
            <input type="number" step="0.01" name="min_order_amount" placeholder="0 = no minimum">
        </div>

        <div class="field">
            <label>Expiry Date — optional</label>
            <input type="date" name="expiry_date">
        </div>

        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="primary-btn full">Add Coupon</button>

    </form>
</section>

</body>
</html>