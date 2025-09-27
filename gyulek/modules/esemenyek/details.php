<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','lelkesz','penztaros','tag','megtekinto']);

if (!isset($_GET['id'])) {
    die(" Hiányzó esemény ID.");
}

$event_id = (int) $_GET['id'];

// Esemény lekérdezése
$stmt = $pdo->prepare("SELECT e.*, t.name AS type_name
                       FROM events e
                       LEFT JOIN event_types t ON e.type_id = t.id
                       WHERE e.id = ? AND e.org_id = ?");
$stmt->execute([$event_id, $_SESSION['org_id']]);
$event = $stmt->fetch();

if (!$event) {
    die(" Nincs ilyen esemény vagy nincs jogosultság.");
}

// Új részlet mentése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_detail'])) {
    $stmt = $pdo->prepare("INSERT INTO event_details (event_id, detail_key, detail_value) VALUES (?, ?, ?)");
    $stmt->execute([$event_id, $_POST['detail_key'], $_POST['detail_value']]);
    header("Location: details.php?id=" . $event_id);
    exit;
}

// Új tag hozzárendelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $stmt = $pdo->prepare("INSERT INTO event_members (event_id, member_id, role_id) VALUES (?, ?, ?)");
    $stmt->execute([$event_id, $_POST['member_id'], $_POST['role_id']]);
    header("Location: details.php?id=" . $event_id);
    exit;
}

// Részletek lekérdezése
$details = $pdo->prepare("SELECT * FROM event_details WHERE event_id = ?");
$details->execute([$event_id]);
$details = $details->fetchAll();

// Tagok és szerepkörök lekérdezése
$members = $pdo->prepare("SELECT id, name FROM members WHERE org_id = ? ORDER BY name");
$members->execute([$_SESSION['org_id']]);
$members = $members->fetchAll();

$roles = $pdo->query("SELECT id, name FROM event_roles ORDER BY name")->fetchAll();

// Hozzárendelt tagok
$event_members = $pdo->prepare("SELECT em.id, m.name, r.name AS role_name
                                FROM event_members em
                                JOIN members m ON em.member_id = m.id
                                JOIN event_roles r ON em.role_id = r.id
                                WHERE em.event_id = ?");
$event_members->execute([$event_id]);
$event_members = $event_members->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<h2>Esemény részletei</h2>

<div class="card mb-3">
  <div class="card-body">
  <h4><?= htmlspecialchars($event['title']) ?></h4>
  <p><b>Típus:</b> <?= htmlspecialchars($event['type_name'] ?? 'Nincs megadva') ?></p>
  <p><b>Kezdet:</b> <?= htmlspecialchars($event['start']) ?></p>
  <p><b>Vége:</b> <?= htmlspecialchars($event['end']) ?></p>
  <p><b>Helyszín:</b> <?= htmlspecialchars($event['location']) ?></p>
  <p><b>Leírás:</b> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
  <a href="edit.php?id=<?= $event_id ?>" class="btn btn-warning">
    <i class="bi bi-pencil-square"></i> Szerkesztés
  </a>
  <a href="delete.php?id=<?= $event_id ?>" class="btn btn-danger"
     onclick="return confirm('Biztosan törlöd az eseményt?')">
    <i class="bi bi-trash"></i> Törlés
  </a>
</div>
</div>

<h3>Extra részletek</h3>
<ul class="list-group mb-3">
  <?php foreach ($details as $d): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <div>
        <b><?= htmlspecialchars($d['detail_key']) ?>:</b>
        <?= htmlspecialchars($d['detail_value']) ?>
      </div>
      <a href="delete_detail.php?id=<?= $d['id'] ?>&event=<?= $event_id ?>" 
         class="text-danger" 
         onclick="return confirm('Biztosan törlöd ezt a részletet?')">
        <i class="bi bi-trash"></i>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<form method="post" class="row g-2 mb-4">
  <div class="col-md-4">
    <input type="text" name="detail_key" class="form-control" placeholder="Kulcs (pl. résztvevők)" required>
  </div>
  <div class="col-md-6">
    <input type="text" name="detail_value" class="form-control" placeholder="Érték (pl. 120)" required>
  </div>
  <div class="col-md-2">
    <button type="submit" name="add_detail" class="btn btn-primary w-100">Hozzáad</button>
  </div>
</form>

<h3>Hozzárendelt tagok</h3>
<ul class="list-group mb-3">
  <?php foreach ($event_members as $em): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <div>
        <?= htmlspecialchars($em['name']) ?>  <?= htmlspecialchars($em['role_name']) ?>
      </div>
      <a href="delete_member.php?id=<?= $em['id'] ?>&event=<?= $event_id ?>" 
         class="text-danger" 
         onclick="return confirm('Biztosan törlöd ezt a hozzárendelést?')">
        <i class="bi bi-trash"></i>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<form method="post" class="row g-2">
  <div class="col-md-5">
    <select name="member_id" class="form-select" required>
      <option value="">-- Válassz tagot --</option>
      <?php foreach ($members as $m): ?>
        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-5">
    <select name="role_id" class="form-select" required>
      <option value="">-- Válassz szerepet --</option>
      <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <button type="submit" name="add_member" class="btn btn-success w-100">Hozzáad</button>
  </div>
</form>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
