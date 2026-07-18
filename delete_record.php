<?php
require_once "config.php";
require_once "includes/auth.php";
requireAdmin(); // only admins may delete

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM records WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php?success=" . urlencode("Record deleted."));
    exit;
}

header("Location: dashboard.php?error=" . urlencode("No record specified."));
exit;
