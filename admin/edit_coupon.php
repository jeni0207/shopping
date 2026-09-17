<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: coupons.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
$stmt->execute([$id]);
$coupon = $stmt->fetch();

if (!$coupon) { header("Location: coupons.php"); exit; }

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = strtoupper(trim($_POST["code"]));
    $discount = $_POST["discount_percent"];
    $minOrder = $_POST["min_order_amount"] !== '' ? $_POST["min_order_amount"] : 0;
    $status = $_POST["status"];
    $expiry = $_POST["expiry_date"] !== '' ? $_POST["expiry_date"] : null;

    $check = $conn->prepare("SELECT id FROM coupons WHERE code = ? AND id != ?");
    $check->execute([$code, $id]);

    if ($check->fetch()) {
        $message = "This coupon code already exists.";
    } else {
        $stmt = $conn->prepare("UPDATE coupons SET code=?, discount_percent=?, min_order_amount=?, status=?, expiry_date=? WHERE id=?");
        $stmt->execute([$code, $discount, $minOrder, $status, $expiry, $id]);
        header("Location: coupons.php?msg=Coupon updated successfully");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Coupon</title>
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
    <h2 style="margin-bottom:20px;">Edit Coupon</h2>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_coupon.php?id=<?php echo $coupon['id']; ?>">

        <div class="field">
            <label>Coupon Code</label>
            <input type="text" name="code" required value="<?php echo htmlspecialchars($coupon['code']); ?>" style="text-transform:uppercase;">
        </div>

        <div class="field">
            <label>Discount Percent (%)</label>
            <input type="number" name="discount_percent" required min="1" max="100" value="<?php echo $coupon['discount_percent']; ?>">
        </div>

        <div class="field">
            <label>Minimum Order Amount (₹)</label>
            <input type="number" step="0.01" name="min_order_amount" value="<?php echo $coupon['min_order_amount']; ?>">
        </div>

        <div class="field">
            <label>Expiry Date</label>
            <input type="date" name="expiry_date" value="<?php echo $coupon['expiry_date']; ?>">
        </div>

        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="active" <?php echo $coupon['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $coupon['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="primary-btn full">Update Coupon</button>

    </form>
</section>

</body>
</html>