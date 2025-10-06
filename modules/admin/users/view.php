<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Nincs ilyen felhasználó.");
}

// betöltjük a hozzárendelt szervezeteket
$stmt = $pdo->prepare("
    SELECT o.name, uo.role 
    FROM user_orgs uo
    JOIN organizations o ON uo.org_id = o.id
    WHERE uo.user_id = ?
");
$stmt->execute([$id]);
$user_orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// új szervezet hozzárendelés kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['org_name'], $_POST['role'])) {
    $org_name = trim($_POST['org_name']);
    $role = $_POST['role'];

    if ($org_name !== '') {
        $stmt = $pdo->prepare("SELECT id FROM organizations WHERE name = ?");
        $stmt->execute([$org_name]);
        if ($org = $stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
            $stmt->execute([$id, $org['id'], $role]);
            header("Location: view.php?id=" . $id);
            exit;
        }
    }
}

// betöltjük a szervezeteket a datalisthez
$stmt = $pdo->query("SELECT name FROM organizations ORDER BY name");
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow-lg p-4 mx-auto" style="max-width: 700px;">
    <h3 class="mb-3"><?= htmlspecialchars($user['family_name'] . " " . $user['given_name']) ?></h3>
    <p><strong>Felhasználónév:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Telefonszám:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    <p><strong>Jogosultság:</strong> <?= htmlspecialchars($user['user_permission']) ?></p>

    <h5 class="mt-4">Hozzárendelt egyházszervezetek</h5>
<?php if ($user_orgs): ?>
  <ul class="list-group">
    <?php foreach ($user_orgs as $uo): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= htmlspecialchars($uo['name']) ?> (<?= htmlspecialchars($uo['role']) ?>)
        <a href="delete_user_org.php?user_id=<?= $id ?>&org=<?= urlencode($uo['name']) ?>"
           class="btn btn-sm btn-outline-danger"
           onclick="return confirm('Biztosan törlöd ezt a hozzárendelést?')">❌</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>Nincs hozzárendelt egyházszervezet.</p>
<?php endif; ?>

    <!-- Új hozzárendelés blokk -->
    <div class="mt-4">
      <h6>Új szervezet hozzárendelése</h6>
      <form method="post" class="row g-2">
        <div class="col-md-8">
          <input list="orgs" name="org_name" class="form-control" placeholder="Válassz szervezetet">
          <datalist id="orgs">
            <?php foreach ($orgs as $org): ?>
              <option value="<?= htmlspecialchars($org['name']) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>
        <div class="col-md-4">
          <select name="role" class="form-select">
            <option value="">-- szerepkör --</option>
            <option value="lelkész">Lelkész</option>
            <option value="felügyelő">Felügyelő</option>
            <option value="pénztáros">Pénztáros</option>
            <option value="adminisztrátor">Adminisztrátor</option>
            <option value="esperes">Esperes</option>
            <option value="püspök">Püspök</option>
            <option value="megtekintő">Megtekintő</option>
          </select>
        </div>
        <div class="col-12 mt-2">
          <button type="submit" class="btn btn-primary">Hozzárendelés</button>
        </div>
      </form>
    </div>

    <div class="mt-4 d-flex justify-content-between">
      <a href="index.php" class="btn btn-secondary">Vissza</a>
      <div>
        <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning">Szerkesztés</a>
        <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-danger"
           onclick="return confirm('Biztosan törlöd ezt a felhasználót?')">Törlés</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>