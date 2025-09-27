<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

if (!isset($_GET['id']) || !isset($_GET['event'])) {
    die("❌ Hiányzó adat.");
}

$detail_id = (int) $_GET['id'];
$event_id  = (int) $_GET['event'];

// Ellenőrizzük, hogy az esemény a user szervezetéhez tartozik
$stmt = $pdo->prepare("SELECT org_id FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event || $event['org_id'] != $_SESSION['org_id']) {
    die("❌ Nincs jogosultság.");
}

$stmt = $pdo->prepare("DELETE FROM event_details WHERE id = ? AND event_id = ?");
$stmt->execute([$detail_id, $event_id]);

header("Location: details.php?id=" . $event_id);
exit;
