<?php
require_once "includes/auth.php";
require_once "config/db.php";

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: products.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) { header("Location: products.php"); exit; }
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0; display:grid; grid-template-columns: 1fr 1fr; gap: 40px;">

    <div class="product-image" style="height:400px;">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:16px;">
    </div>

    <div>
        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
        <h1 style="margin: 10px 0;"><?php echo htmlspecialchars($product['name']); ?></h1>
        <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
    <div class="product-detail-price">
        ₹<?php echo number_format($product['discount_price'], 0); ?>
        <span style="text-decoration:line-through; color:#999; font-size:16px; font-weight:400; margin-left:8px;">₹<?php echo number_format($product['price'], 0); ?></span>
        <span style="background:#eaf8ef; color:#218838; font-size:12px; padding:3px 8px; border-radius:6px; margin-left:8px;">
            <?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>% OFF
        </span>
    </div>
<?php else: ?>
    <div class="product-detail-price">₹<?php echo number_format($product['price'], 0); ?></div>
<?php endif; ?>

        <p class="muted"><?php echo htmlspecialchars($product['description']); ?></p>
        <p class="muted">Stock available: <?php echo $product['stock']; ?></p>
        <?php if (!empty($product['specs'])): ?>
    <h4 style="margin-top:15px; margin-bottom:8px;">Specifications</h4>
    <p class="muted"><?php echo nl2br(htmlspecialchars($product['specs'])); ?></p>
<?php endif; ?>

<?php if (isLoggedIn()): ?>
    <form method="POST" action="cart.php" style="display:flex; gap:12px; align-items:center;">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="action" value="add">
        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width:70px; padding:12px; border:1px solid #ddd; border-radius:9px; text-align:center;">
        <button type="submit" class="primary-btn" style="flex:1;"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
    </form>
<?php else: ?>
    <a href="login.php" class="primary-btn full" style="text-align:center;">Login to Purchase</a>
<?php endif; ?>
    </div>

</section>

<?php include "includes/footer.php"; ?>