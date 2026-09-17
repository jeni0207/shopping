<?php

require_once "includes/auth.php";
require_once "config/db.php";

requireLogin();
checkIfBlocked($conn);

$userId = currentUserId();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {

        $productId = (int)($_POST['product_id'] ?? 0);
        $qtyToAdd = (int)($_POST['quantity'] ?? 1);
        if ($qtyToAdd < 1) $qtyToAdd = 1;
    
        if ($productId > 0) {
    
            $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
    
            if ($product) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] += $qtyToAdd;
                } else {
                    $_SESSION['cart'][$productId] = $qtyToAdd;
                }
            }
        }
    
        header("Location: cart.php");
        exit;
    }

    if ($action === 'update') {

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = $quantity;
        }

        header("Location: cart.php");
        exit;
    }

    if ($action === 'remove') {

        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            unset($_SESSION['cart'][$productId]);
        }

        header("Location: cart.php");
        exit;
    }

    if ($action === 'clear') {

        $_SESSION['cart'] = [];

        header("Location: cart.php");
        exit;
    }
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {

    $productIds = array_keys($_SESSION['cart']);

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    $stmt = $conn->prepare("
        SELECT id, name, price, image
        FROM products
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($productIds);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {

        $productId = (int)$product['id'];
        $quantity = (int)($_SESSION['cart'][$productId] ?? 1);

        if ($quantity < 1) {
            $quantity = 1;
        }

        $subtotal = $product['price'] * $quantity;

        $cartItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];

        $total += $subtotal;
    }
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding:50px 0;">

    <h1 style="margin-bottom:25px;">Your Cart</h1>

    <?php if (empty($cartItems)): ?>

        <div class="empty">
            <p>Your cart is empty.</p>
            <a href="products.php">Browse Products</a>
        </div>

    <?php else: ?>

        <?php foreach ($cartItems as $item): ?>

            <div class="cart-item">

                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">

                <div class="cart-item-info">
                    <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="price">₹<?php echo number_format($item['price'], 0); ?></div>
                    <div style="margin-top:5px; font-size:14px;">
                        Subtotal: <strong>₹<?php echo number_format($item['subtotal'], 0); ?></strong>
                    </div>
                </div>

                <form method="POST" action="cart.php" class="qty">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width:60px; text-align:center;" onchange="this.form.submit();">
                </form>

                <form method="POST" action="cart.php">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                    <button type="submit" class="remove"><i class="fa-solid fa-xmark"></i> Remove</button>
                </form>

            </div>

        <?php endforeach; ?>

        <div class="cart-total">
            <span>Total</span>
            <span>₹<?php echo number_format($total, 0); ?></span>
        </div>

        <div style="display:flex; gap:15px; margin-top:20px; flex-wrap:wrap;">

            <a href="products.php" class="secondary-btn" style="text-decoration:none; flex:1; text-align:center;">
                Continue Shopping
            </a>

            <form method="POST" action="cart.php" style="flex:1;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="remove" style="width:100%; height:100%; cursor:pointer;">
                    Clear Cart
                </button>
            </form>

            <a href="checkout.php" class="primary-btn" style="text-decoration:none; flex:1; text-align:center;">
                Proceed to Checkout →
            </a>

        </div>

    <?php endif; ?>

</section>

<?php include "includes/footer.php"; ?>