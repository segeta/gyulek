<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO events (org_id, title, type_id, start, end, location, description) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['org_id'],
            $_POST['title'],
            !empty($_POST['type_id']) ? $_POST['type_id'] : null,
            $_POST['start'],
            $_POST['end'] ?: null,
            $_POST['location'],
            $_POST['description']
        ]);

        $event_id = $pdo->lastInsertId();

        if (isset($_POST['action']) && $_POST['action'] === 'details') {
            header("Location: details.php?id=" . $event_id);
        } else {
            header("Location: index.php");
        }
        exit;

    } catch (PDOException $e) {
        $message = "❌ Hiba: " . $e->getMessage();
    }
}

// Eseménytípusok lekérdezése
$types = $pdo->query("SELECT id, name FROM event_types ORDER BY name")->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Új esemény rögzítése</h2>
<form method="post" class="form">
  <div class="mb-3">
    <label class="form-label">Cím</label>
    <input type="text" name="title" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Esemény típusa</label>
    <select name="type_id" class="form-select">
      <option value="">-- Válassz típust --</option>
      <?php foreach ($types as $t): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Kezdet</label>
    <input type="datetime-local" name="start" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Vége</label>
    <input type="datetime-local" name="end" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Helyszín</label>
    <input type="text" name="location" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Leírás</label>
    <textarea name="description" class="form-control"></textarea>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" name="action" value="save" class="btn btn-success">
    <i class="bi bi-check-circle"></i>Mentés</button>
    <button type="submit" name="action" value="details" class="btn btn-primary">
    <i class="bi bi-list-check"></i>Mentés és részletek</button>
  </div>
</form>
<p><?= $message ?></p>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
