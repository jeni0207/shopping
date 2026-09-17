<?php
require_once "includes/auth.php";
require_once "config/db.php";

$categoryIcons = [
    'Fashion' => '👕',
    'Electronics' => '💻',
    'Home' => '🏠',
    'Beauty' => '💄',
    'Footwear' => '👟',
    'Accessories' => '🎒',
];
function getCatEmoji($cat, $map) {
    return $map[$cat] ?? '🏷️';
}

$catStmt = $conn->query("SELECT DISTINCT category FROM products");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$featuredProducts = $stmt->fetchAll();
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<main>
    <section class="hero">
        <div class="container hero-content">
            <div>
                <p class="eyebrow">NEW SEASON • UP TO 50% OFF</p>
                <h1>Everything you love.<br><span>One easy place.</span></h1>
                <p class="hero-text">Discover fashion, electronics, home essentials and more with fast delivery and secure checkout.</p>
                <a href="products.php" class="primary-btn">Shop Now →</a>
            </div>
            <div class="hero-card">
                <div class="floating-card"><i class="fa-solid fa-fire"></i> Trending Now</div>
                <div class="hero-product"><i class="fa-solid fa-bag-shopping" style="font-size:80px;"></i></div>
                <strong>Fresh deals every day</strong>
                <small>Curated products at better prices.</small>
            </div>
        </div>
    </section>

    <section class="container categories-section">
        <div class="section-heading">
            <div><p class="eyebrow">EXPLORE</p><h2>Shop by Category</h2></div>
        </div>
        <div class="category-grid">
            <button class="category-card active" onclick="window.location.href='products.php'">✨<span>All</span></button>
            <?php foreach ($categories as $cat): ?>
                <button class="category-card" onclick="window.location.href='products.php?category=<?php echo urlencode($cat); ?>'">
                    <?php echo getCatEmoji($cat, $categoryIcons); ?><span><?php echo htmlspecialchars($cat); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container products-section">
        <div class="section-heading">
            <div><p class="eyebrow">OUR PICKS</p><h2>Featured Products</h2></div>
            <a href="products.php" class="text-btn">View All →</a>
        </div>

        <?php if (empty($featuredProducts)): ?>
            <div class="empty">No products found.</div>
        <?php else: ?>
            <div class="carousel-wrapper">
                <button class="carousel-arrow left" onclick="scrollCarousel(-1)"><i class="fa-solid fa-chevron-left"></i></button>

                <div class="carousel-track" id="productCarousel">
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="product-card carousel-item">
                            <div class="product-image" onclick="window.location.href='product_details.php?id=<?php echo $product['id']; ?>'">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="price-row">
    <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
        <span class="price">
            ₹<?php echo number_format($product['discount_price'], 0); ?>
            <small style="text-decoration:line-through; color:#999; font-weight:400; font-size:12px; margin-left:4px;">₹<?php echo number_format($product['price'], 0); ?></small>
        </span>
    <?php else: ?>
        <span class="price">₹<?php echo number_format($product['price'], 0); ?></span>
    <?php endif; ?>
                                    <?php if (isLoggedIn()): ?>
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="action" value="add">
                                            <button type="submit" class="add-btn">Add</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="login.php" class="add-btn" style="text-decoration:none;">Add</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-arrow right" onclick="scrollCarousel(1)"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        <?php endif; ?>
    </section>

    <section class="benefits">
        <div class="container benefits-grid">
            <div><i class="fa-solid fa-truck-fast"></i> <div><b>Fast Delivery</b><small>Quick doorstep delivery</small></div></div>
            <div><i class="fa-solid fa-lock"></i> <div><b>Secure Payment</b><small>Safe & encrypted checkout</small></div></div>
            <div><i class="fa-solid fa-rotate-left"></i> <div><b>Easy Returns</b><small>Simple 7-day returns</small></div></div>
            <div><i class="fa-solid fa-comments"></i> <div><b>24/7 Support</b><small>We're here to help</small></div></div>
        </div>
    </section>
</main>

<script>
function scrollCarousel(direction) {
    const track = document.getElementById('productCarousel');
    const item = track.querySelector('.carousel-item');
    if (!item) return;
    const itemWidth = item.offsetWidth + 20;
    track.scrollBy({ left: direction * itemWidth * 2, behavior: 'smooth' });
}
</script>

<?php include "includes/footer.php"; ?>