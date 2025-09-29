<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = ? AND org_id = ?");
    $stmt->execute([$id, $_SESSION['org_id']]);
}
header("Location: index.php");
exit;
