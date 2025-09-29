<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz']);

if (!isset($_GET['id'])) {
    die("❌ Hiányzó esemény ID.");
}
$event_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND org_id = ?");
$stmt->execute([$event_id, $_SESSION['org_id']]);
$event = $stmt->fetch();

if (!$event) {
    die("❌ Nincs ilyen esemény vagy nincs jogosultság.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE events
                               SET title = ?, type_id = ?, start = ?, end = ?, location = ?, description = ?
                               WHERE id = ? AND org_id = ?");
        $stmt->execute([
            $_POST['title'],
            !empty($_POST['type_id']) ? $_POST['type_id'] : null,
            $_POST['start'],
            $_POST['end'] ?: null,
            $_POST['location'],
            $_POST['description'],
            $event_id,
            $_SESSION['org_id']
        ]);

        header("Location: details.php?id=" . $event_id);
        exit;

    } catch (PDOException $e) {
        $message = "❌ Hiba: " . $e->getMessage();
    }
}

$types = $pdo->query("SELECT id, name FROM event_types ORDER BY name")->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Esemény szerkesztése</h2>
<form method="post" class="form">
  <div class="mb-3">
    <label class="form-label">Cím</label>
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Esemény típusa</label>
    <select name="type_id" class="form-select">
      <option value="">-- Válassz típust --</option>
      <?php foreach ($types as $t): ?>
        <option value="<?= $t['id'] ?>" <?= ($event['type_id'] == $t['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($t['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Kezdet</label>
    <input type="datetime-local" name="start" value="<?= date('Y-m-d\TH:i', strtotime($event['start'])) ?>" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Vége</label>
    <input type="datetime-local" name="end" value="<?= $event['end'] ? date('Y-m-d\TH:i', strtotime($event['end'])) : '' ?>" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Helyszín</label>
    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Leírás</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($event['description']) ?></textarea>
  </div>
  <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i>Mentés</button>
  <a href="details.php?id=<?= $event_id ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i>Mégse</a>
</form>
<p><?= $message ?></p>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
