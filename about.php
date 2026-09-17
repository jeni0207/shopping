<?php
require_once "includes/auth.php";
require_once "config/db.php";
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 60px 0; max-width: 850px;">

    <p class="eyebrow">ABOUT US</p>
    <h1 style="margin-bottom: 20px;">Welcome to ShopEase</h1>

    <p style="line-height:1.8; color:#555; margin-bottom:20px;">
        ShopEase was founded with one simple idea: online shopping should be easy, honest, and enjoyable.
        We bring together quality products across electronics, fashion, footwear, accessories, home, and beauty —
        all in one place, at prices that make sense.
    </p>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin: 35px 0;">
        <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px;">
            <i class="fa-solid fa-bullseye" style="font-size:26px; color:#ff5a3c;"></i>
            <h3 style="margin:12px 0 8px;">Our Mission</h3>
            <p style="color:#777; font-size:14px; line-height:1.6;">
                To make quality products accessible to everyone through a simple, trustworthy, and fast online shopping experience.
            </p>
        </div>
        <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px;">
            <i class="fa-solid fa-eye" style="font-size:26px; color:#ff5a3c;"></i>
            <h3 style="margin:12px 0 8px;">Our Vision</h3>
            <p style="color:#777; font-size:14px; line-height:1.6;">
                To become a trusted name in online retail, known for reliability, variety, and customer-first service.
            </p>
        </div>
    </div>

    <h3 style="margin-bottom:15px;">What We Offer</h3>
    <div class="category-grid">
        <div class="category-card"><i class="fa-solid fa-laptop"></i><span>Electronics</span></div>
        <div class="category-card"><i class="fa-solid fa-shoe-prints"></i><span>Footwear</span></div>
        <div class="category-card"><i class="fa-solid fa-bag-shopping"></i><span>Accessories</span></div>
        <div class="category-card"><i class="fa-solid fa-shirt"></i><span>Fashion</span></div>
        <div class="category-card"><i class="fa-solid fa-house"></i><span>Home</span></div>
        <div class="category-card"><i class="fa-solid fa-spray-can-sparkles"></i><span>Beauty</span></div>
    </div>

</section>

<?php include "includes/footer.php"; ?>