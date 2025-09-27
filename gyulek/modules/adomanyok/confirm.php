<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add.php");
    exit;
}

// Adatok összegyűjtése
$date      = $_POST['date'] ?? '';
$member_id = $_POST['member_id'] ?? '';
$type_id   = $_POST['type_id'] ?? '';
$amount    = $_POST['amount'] ?? '';
$note      = $_POST['note'] ?? '';

// Lekérjük a tag adatait
$stmt = $pdo->prepare("SELECT id, name, birth_date, address FROM members WHERE id = ? AND org_id = ?");
$stmt->execute([$member_id, $_SESSION['org_id']]);
$member = $stmt->fetch();

// Lekérjük a típust
$stmt = $pdo->prepare("SELECT id, name FROM donation_types WHERE id = ?");
$stmt->execute([$type_id]);
$type = $stmt->fetch();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Adomány rögzítésének megerősítése</h2>

<?php if ($member && $type): ?>
  <div class="card mb-3">
    <div class="card-body">
      <p><b>Dátum:</b> <?= htmlspecialchars($date) ?></p>
      <p><b>Tag:</b> <?= htmlspecialchars($member['name']) ?> 
         (<?= htmlspecialchars($member['birth_date']) ?>, <?= htmlspecialchars($member['address']) ?>)</p>
      <p><b>Típus:</b> <?= htmlspecialchars($type['name']) ?></p>
      <p><b>Összeg:</b> <?= number_format((float)$amount, 2, ',', ' ') ?> Ft</p>
      <p><b>Megjegyzés:</b> <?= nl2br(htmlspecialchars($note)) ?></p>
    </div>
  </div>

  <form method="post" action="save.php">
    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
    <input type="hidden" name="member_id" value="<?= htmlspecialchars($member_id) ?>">
    <input type="hidden" name="type_id" value="<?= htmlspecialchars($type_id) ?>">
    <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
    <input type="hidden" name="note" value="<?= htmlspecialchars($note) ?>">

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">
        <i class="bi bi-check-circle"></i> Megerősítés
      </button>
      <a href="add.php" class="btn btn-secondary">
        <i class="bi bi-x-circle"></i> Mégse
      </a>
    </div>
  </form>
<?php else: ?>
  <div class="alert alert-danger">
    ❌ Hibás adat! Kérlek, próbáld újra.
  </div>
  <a href="add.php" class="btn btn-secondary">Vissza</a>
<?php endif; ?>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
