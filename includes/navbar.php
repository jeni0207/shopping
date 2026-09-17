<header class="header">
    <div class="container nav">

        <a href="index.php" class="logo"><span>Shop</span>Ease</a>

        <form class="search-box" action="products.php" method="GET">
            <input type="search" name="search" placeholder="Search products...">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="nav-actions">
            <?php if (isLoggedIn()): ?>
                <a href="profile.php" class="nav-btn"><i class="fa-solid fa-user"></i> <span><?php echo htmlspecialchars(currentUserName()); ?></span></a>
                <a href="orders.php" class="nav-btn"><i class="fa-solid fa-receipt"></i> <span>Orders</span></a>
                <?php $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                <a href="cart.php" class="nav-btn cart-btn">
                    <i class="fa-solid fa-cart-shopping"></i> <span>Cart</span>
                    <?php if ($cartCount > 0): ?>
                        <b><?php echo $cartCount; ?></b>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
            <?php else: ?>
                <a href="login.php" class="nav-btn"><i class="fa-solid fa-user"></i> <span>Login</span></a>
                <a href="register.php" class="nav-btn"><i class="fa-solid fa-user-plus"></i> <span>Register</span></a>
            <?php endif; ?>
        </div>

    </div>
</header>