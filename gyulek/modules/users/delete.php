<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin']);

$id = $_GET['id'] ?? null;

if ($id) {
    // Törlés a user_orgs táblából (csak az adott org-ból)
    $stmt = $pdo->prepare("DELETE FROM user_orgs WHERE user_id = ? AND org_id = ?");
    $stmt->execute([$id, $_SESSION['org_id']]);

    // Ha a user más szervezethez nem tartozik, töröljük a users táblából is
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_orgs WHERE user_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    }
}

header("Location: index.php");
exit;
