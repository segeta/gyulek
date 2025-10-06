<?php
require_once __DIR__ . '/../../../core/db.php';

$term = $_GET['term'] ?? '';
$stmt = $pdo->prepare("SELECT id, name FROM organizations WHERE name LIKE ?");
$stmt->execute(["%$term%"]);
$results = [];
foreach ($stmt as $row) {
    $results[] = [
        'label' => $row['name'],
        'value' => $row['id']
    ];
}
echo json_encode($results);