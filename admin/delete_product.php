<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: products.php?msg=Product deleted successfully");
exit;