<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz','tag']);

header('Content-Type: application/json; charset=utf-8');

$term = $_GET['term'] ?? '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, name, birth_date, address 
    FROM members 
    WHERE org_id = ? 
      AND (name LIKE ? OR address LIKE ?)
    ORDER BY name
    LIMIT 10
");
$search = "%" . $term . "%";
$stmt->execute([$_SESSION['org_id'], $search, $search]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
