<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: coupons.php?msg=Coupon deleted successfully");
exit;