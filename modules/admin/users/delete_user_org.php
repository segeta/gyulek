<?php
require_once __DIR__ . '/../../../core/db.php';
session_start();

$user_id = (int)($_GET['user_id'] ?? 0);
$org_name = $_GET['org'] ?? '';

if ($user_id && $org_name) {
    // szervezet id lekérdezése a névből
    $stmt = $pdo->prepare("SELECT id FROM organizations WHERE name = ?");
    $stmt->execute([$org_name]);
    if ($org = $stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM user_orgs WHERE user_id = ? AND org_id = ?");
        $stmt->execute([$user_id, $org['id']]);
    }
}

// vissza a view oldalra
header("Location: view.php?id=" . $user_id);
exit;