<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($id && in_array($status, ['active', 'blocked'])) {
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header("Location: users.php?msg=User status updated");
exit;