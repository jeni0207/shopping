<?php
require_once "includes/auth.php";
require_once "config/db.php";

$stmt = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0;">

    <div class="section-heading">
        <div><p class="eyebrow">BROWSE</p><h2>All Categories</h2></div>
    </div>

    <?php if (empty($categories)): ?>
        <div class="empty">No categories available.</div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($categories as $cat): ?>
                <div class="product-card">
                    <div class="product-image" onclick="window.location.href='products.php?category=<?php echo urlencode($cat['name']); ?>'">
                        <img src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-name" style="font-size:18px;"><?php echo htmlspecialchars($cat['name']); ?></div>
                        <p class="muted" style="margin: 8px 0;"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <a href="products.php?category=<?php echo urlencode($cat['name']); ?>" class="primary-btn full" style="text-decoration:none;">View Products</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>

<?php include "includes/footer.php"; ?>