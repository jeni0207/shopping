<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: categories.php?msg=Category deleted successfully");
exit;