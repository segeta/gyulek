<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $permission = $_POST['user_permission'] ?? 'felhasználó';
    $family_name = trim($_POST['family_name'] ?? '');
    $given_name = trim($_POST['given_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['Új dokumentum.txtpassword'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password !== $password2) {
        $error = "A megadott jelszavak nem egyeznek!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // felhasználó mentése
        $stmt = $pdo->prepare("INSERT INTO users (username, user_permission, family_name, given_name, email, phone, password_hash, created_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $permission, $family_name, $given_name, $email, $phone, $password_hash]);
        $user_id = $pdo->lastInsertId();

        // egyházszervezet hozzárendelés, ha megadva
        $org_name = trim($_POST['org_name'] ?? '');
        $role = $_POST['role'] ?? '';
        if ($org_name !== '') {
            $stmt = $pdo->prepare("SELECT id FROM organizations WHERE name = ?");
            $stmt->execute([$org_name]);
            if ($org = $stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $org['id'], $role]);
            }
        }

        header("Location: users.php");
        exit;
    }
}

// betöltjük a szervezeteket a datalist-hez
$stmt = $pdo->query("SELECT id, name FROM organizations ORDER BY name");
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

	<div class="container mt-4">
	 <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
    <h3 class="text-center mb-3">Új felhasználó hozzáadása</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Felhasználónév</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col">
          <label class="form-label">Jogosultság</label>
          <select name="user_permission" class="form-select">
            <option value="rendszergazda">Rendszergazda</option>
            <option value="felhasználó" selected>Felhasználó</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Vezetéknév</label>
          <input type="text" name="family_name" class="form-control" required>
        </div>
        <div class="col">
          <label class="form-label">Keresztnév</label>
          <input type="text" name="given_name" class="form-control" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="col">
          <label class="form-label">Telefonszám</label>
          <input type="text" name="phone" class="form-control">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Jelszó</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col">
          <label class="form-label">Jelszó ismét</label>
          <input type="password" name="password2" class="form-control" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">EÚj dokumentum.txtgyházszervezet</label>
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