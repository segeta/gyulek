<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add.php");
    exit;
}

$date      = $_POST['date'] ?? null;
$member_id = $_POST['member_id'] ?? null;
$type_id   = $_POST['type_id'] ?? null;
$amount    = $_POST['amount'] ?? null;
$note      = $_POST['note'] ?? null;

$message = "";

try {
    $stmt = $pdo->prepare("INSERT INTO donations (org_id, member_id, type_id, date, amount, note)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['org_id'],
        $member_id,
        $type_id,
        $date,
        $amount,
        $note
    ]);

    // Sikeres mentés után vissza az indexhez
    header("Location: index.php?success=1");
    exit;

} catch (PDOException $e) {
    $message = "❌ Hiba az adomány mentésekor: " . $e->getMessage();
}

include __DIR__ . '/../../templates/header.php';
?>

<h2>Adomány mentése</h2>
<div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<a href="add.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Vissza az űrlaphoz</a>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
