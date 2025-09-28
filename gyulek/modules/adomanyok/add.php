<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['org_id'])) {
    header("Location: /gyulek/login.php");
    exit;
}

$error = "";
$success = false;

// Adománytípusok betöltése
$types = $pdo->query("SELECT id, name FROM donation_types ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $type_id = (int)($_POST['type_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $note = trim($_POST['note'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');

    if (!$member_id || !$type_id || !$amount) {
        $error = "Minden kötelező mezőt ki kell tölteni!";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO donations (member_id, type_id, amount, note, date, org_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$member_id, $type_id, $amount, $note, $date, $_SESSION['org_id']]);

        $success = true;
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
    <h2 class="text-center mb-4">Új adomány rögzítése</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success">Az adomány sikeresen rögzítve!</div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label for="date" class="form-label">Dátum</label>
        <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="mb-3">
        <label for="member_id" class="form-label">Tag</label>
        <select class="form-select" id="member_id" name="member_id" required>
          <option value="">-- Válassz tagot --</option>
          <?php
          $stmt = $pdo->prepare("SELECT id, name, birth_date, address FROM members WHERE org_id = ? ORDER BY name");
          $stmt->execute([$_SESSION['org_id']]);
          foreach ($stmt as $m): ?>
            <option value="<?= $m['id'] ?>">
              <?= htmlspecialchars($m['name']) ?> 
              (<?= htmlspecialchars($m['birth_date']) ?>, <?= htmlspecialchars($m['address']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="type_id" class="form-label">Adomány típusa</label>
        <select class="form-select" id="type_id" name="type_id" required>
          <option value="">-- Válassz típust --</option>
          <?php foreach ($types as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="amount" class="form-label">Összeg (Ft)</label>
        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
      </div>

      <div class="mb-3">
        <label for="note" class="form-label">Megjegyzés</label>
        <textarea class="form-control" id="note" name="note"></textarea>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Mentés</button>
        <a href="index.php" class="btn btn-secondary">Mégsem</a>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
