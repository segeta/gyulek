<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

// Csak admin férhet hozzá
if (!isset($_SESSION['user_id'])) {
    header("Location: /gyulek/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_orgs WHERE user_id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$isAdmin = $stmt->fetchColumn() > 0;

if (!$isAdmin) {
    die("Nincs jogosultságod a felhasználókezeléshez.");
}

// Felhasználó lekérdezése
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Nincs ilyen felhasználó.");
}

// Szervezetek listája
$orgs = $pdo->query("SELECT id, name FROM organizations ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Jelenlegi hozzárendelések
$stmt = $pdo->prepare("
    SELECT uo.id, uo.org_id, o.name AS org_name, uo.role
    FROM user_orgs uo
    JOIN organizations o ON uo.org_id = o.id
    WHERE uo.user_id = ?
");
$stmt->execute([$id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name) {
        $error = "A név nem lehet üres!";
    } elseif ($password && $password !== $password2) {
        $error = "A jelszavak nem egyeznek!";
    } else {
        // Users frissítése
        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$name, password_hash($password, PASSWORD_BCRYPT), $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
        }

        // Létező hozzárendelések frissítése
        if (isset($_POST['assignments'])) {
            foreach ($_POST['assignments'] as $assign_id => $data) {
                $org_id = (int)$data['org_id'];
                $role = trim($data['role']);
                if ($org_id && $role) {
                    $stmt = $pdo->prepare("UPDATE user_orgs SET org_id = ?, role = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$org_id, $role, $assign_id, $id]);
                }
            }
        }

        // Új hozzárendelés(ek) hozzáadása
        if (!empty($_POST['new_assignments'])) {
            foreach ($_POST['new_assignments'] as $data) {
                $org_id = (int)$data['org_id'];
                $role = trim($data['role']);
                if ($org_id && $role) {
                    $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $org_id, $role]);
                }
            }
        }

        // Törlések
        if (!empty($_POST['delete_assignments'])) {
            foreach ($_POST['delete_assignments'] as $assign_id) {
                $stmt = $pdo->prepare("DELETE FROM user_orgs WHERE id = ? AND user_id = ?");
                $stmt->execute([(int)$assign_id, $id]);
            }
        }

        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <h2>Felhasználó szerkesztése</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Felhasználónév</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="name" class="form-label">Teljes név</label>
      <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Új jelszó (ha változtatni akarod)</label>
      <input type="password" class="form-control" id="password" name="password">
    </div>

    <div class="mb-3">
      <label for="password2" class="form-label">Új jelszó ismét</label>
      <input type="password" class="form-control" id="password2" name="password2">
    </div>

    <h4>Hozzárendelések</h4>

    <?php foreach ($assignments as $a): ?>
      <div class="row align-items-center mb-2">
        <div class="col-md-5">
          <select class="form-select" name="assignments[<?= $a['id'] ?>][org_id]">
            <?php foreach ($orgs as $org): ?>
              <option value="<?= $org['id'] ?>" <?= $org['id'] == $a['org_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($org['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <select class="form-select" name="assignments[<?= $a['id'] ?>][role]">
            <option value="admin" <?= $a['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="lelkesz" <?= $a['role'] === 'lelkesz' ? 'selected' : '' ?>>Lelkész</option>
            <option value="penztaros" <?= $a['role'] === 'penztaros' ? 'selected' : '' ?>>Pénztáros</option>
            <option value="esperes" <?= $a['role'] === 'esperes' ? 'selected' : '' ?>>Esperes</option>
            <option value="puspok" <?= $a['role'] === 'puspok' ? 'selected' : '' ?>>Püspök</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-check-label">
            <input type="checkbox" class="form-check-input" name="delete_assignments[]" value="<?= $a['id'] ?>">
            Törlés
          </label>
        </div>
      </div>
    <?php endforeach; ?>

    <h5 class="mt-3">Új hozzárendelés</h5>
    <div id="newAssignments"></div>
    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addAssignment()">+ Hozzárendelés hozzáadása</button>

    <div>
      <button type="submit" class="btn btn-primary">Mentés</button>
      <a href="index.php" class="btn btn-secondary">Mégsem</a>
    </div>
  </form>
</div>

<script>
function addAssignment() {
  const container = document.getElementById('newAssignments');
  const index = container.children.length;
  const html = `
    <div class="row align-items-center mb-2">
      <div class="col-md-5">
        <select class="form-select" name="new_assignments[${index}][org_id]" required>
          <option value="">-- Szervezet --</option>
          <?php foreach ($orgs as $org): ?>
            <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" name="new_assignments[${index}][role]" required>
          <option value="">-- Szerepkör --</option>
          <option value="admin">Admin</option>
          <option value="lelkesz">Lelkész</option>
          <option value="penztaros">Pénztáros</option>
          <option value="esperes">Esperes</option>
          <option value="puspok">Püspök</option>
        </select>
      </div>
    </div>`;
  container.insertAdjacentHTML('beforeend', html);
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
