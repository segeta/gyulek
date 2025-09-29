<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz','penztaros','tag','megtekinto']);

header('Content-Type: application/json');

$stmt = $pdo->prepare("SELECT e.id, e.title, e.start, e.end, t.name AS type_name
                       FROM events e
                       LEFT JOIN event_types t ON e.type_id = t.id
                       WHERE e.org_id = ?");
$stmt->execute([$_SESSION['org_id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);
