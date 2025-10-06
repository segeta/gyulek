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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $permission = $_POST['user_permission'] ?? 'felhasználó';
    $family_name = trim($_POST['family_name'] ?? '');
    $given_name = trim($_POST['given_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $stmt = $pdo->prepare("UPDATE users 
                           SET username=?, user_permission=?, family_name=?, given_name=?, email=?, phone=? 
                           WHERE id=?");
    $stmt->execute([$username, $permission, $family_name, $given_name, $email, $phone, $id]);

    // szervezet frissítés (egyszerűsített logika: mindig új hozzárendelés)
    $org_name = trim($_POST['org_name'] ?? '');
    $role = $_POST['role'] ?? '';
    if ($org_name !== '') {
        $stmt = $pdo->prepare("SELECT id FROM organizations WHERE name = ?");
        $stmt->execute([$org_name]);
        if ($org = $stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
            $stmt->execute([$id, $org['id'], $role]);
        }
    }

    header("Location: index.php");
    exit;
}

// betöltjük a szervezeteket
$stmt = $pdo->query("SELECT id, name FROM organizations ORDER BY name");
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

	<div class="container mt-4">
	 <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
    <h3 class="text-center mb-3">Felhasználó szerkesztése</h3>

    <form method="post">
      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Felhasználónév</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="col">
          <label class="form-label">Jogosultság</label>
          <select name="user_permission" class="form-select">
            <option value="rendszergazda" <?= $user['user_permission'] === 'rendszergazda' ? 'selected' : '' ?>>Rendszergazda</option>
            <option value="felhasználó" <?= $user['user_permission'] === 'felhasználó' ? 'selected' : '' ?>>Felhasználó</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Vezetéknév</label>
          <input type="text" name="family_name" class="form-control" value="<?= htmlspecialchars($user['family_name']) ?>" required>
        </div>
        <div class="col">
          <label class="form-label">Keresztnév</label>
          <input type="text" name="given_name" class="form-control" value="<?= htmlspecialchars($user['given_name']) ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="col">
          <label class="form-label">Telefonszám</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Egyházszervezet</label>
          <input list="orgs" name="org_name" class="form-control">
          <datalist id="orgs">
            <?php foreach ($orgs as $org): ?>
              <option value="<?= htmlspecialchars($org['name']) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>
        <div class="col">
          <label class="form-label">Szerepkör</label>
          <select name="role" class="form-select">
            <option value="">-- válassz --</option>
            <option value="lelkész">Lelkész</option>
            <option value="felügyelő">Felügyelő</option>
            <option value="pénztáros">Pénztáros</option>
            <option value="adminisztrátor">Adminisztrátor</option>
            <option value="esperes">Esperes</option>
            <option value="püspök">Püspök</option>
            <option value="megtekintő">Megtekintő</option>
          </select>
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Mégsem</a>
        <button type="submit" class="btn btn-primary">Mentés</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>