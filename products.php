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

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($sort === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY created_at DESC";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$catStmt = $conn->query("SELECT name FROM categories WHERE status = 'active' ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="products-section container">
    <div class="section-heading">
        <h2><?php echo $category !== '' ? htmlspecialchars($category) : 'All Products'; ?></h2>
        <form method="GET" action="products.php">
            <?php if ($category): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>
            <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
            <select name="sort" class="sort-select" onchange="this.form.submit()">
                <option value="">Sort by</option>
                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>
        </form>
    </div>

    <div class="category-grid" style="margin-bottom: 25px;">
        <div class="category-card <?php echo $category === '' ? 'active' : ''; ?>" onclick="window.location.href='products.php'">✨<span>All</span></div>
        <?php foreach ($categories as $cat): ?>
            <div class="category-card <?php echo $category === $cat ? 'active' : ''; ?>" onclick="window.location.href='products.php?category=<?php echo urlencode($cat); ?>'">
                <?php echo getCatEmoji($cat, $categoryIcons); ?><span><?php echo htmlspecialchars($cat); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty">No products found.</div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image" onclick="window.location.href='product_details.php?id=<?php echo $product['id']; ?>'" style="position:relative;">
                        <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
                            <span style="position:absolute; top:10px; left:10px; background:#ff5a3c; color:#fff; font-size:11px; font-weight:700; padding:4px 8px; border-radius:6px; z-index:2;">
                                <?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>% OFF
                            </span>
                        <?php endif; ?>
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
    <?php endif; ?>
</section>

<?php include "includes/footer.php"; ?>