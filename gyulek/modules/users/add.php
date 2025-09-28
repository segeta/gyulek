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

// Szervezetek betöltése a legördülőhöz
$orgs = $pdo->query("SELECT id, name FROM organizations ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $org_id = (int) ($_POST['org_id'] ?? 0);
    $role = trim($_POST['role'] ?? '');

    if ($password !== $password2) {
        $error = "A jelszavak nem egyeznek!";
    } elseif (!$username || !$name || !$password || !$org_id || !$role) {
        $error = "Minden mező kitöltése kötelező!";
    } else {
        // Ellenőrizzük, hogy a felhasználónév már létezik-e
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Ez a felhasználónév már foglalt!";
        } else {
            // Users táblába beszúrás
            $stmt = $pdo->prepare("INSERT INTO users (username, name, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $name, password_hash($password, PASSWORD_BCRYPT)]);
            $user_id = $pdo->lastInsertId();

            // User_orgs kapcsolathoz beszúrás
            $stmt = $pdo->prepare("INSERT INTO user_orgs (user_id, org_id, role) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $org_id, $role]);

            header("Location: index.php");
            exit;
        }
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <h2>Új felhasználó létrehozása</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Felhasználónév</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>

    <div class="mb-3">
      <label for="name" class="form-label">Teljes név</label>
      <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Jelszó</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="mb-3">
      <label for="password2" class="form-label">Jelszó ismét</label>
      <input type="password" class="form-control" id="password2" name="password2" required>
    </div>

    <div class="mb-3">
      <label for="org_id" class="form-label">Szervezet</label>
      <select class="form-select" id="org_id" name="org_id" required>
        <option value="">-- Válassz szervezetet --</option>
        <?php foreach ($orgs as $org): ?>
          <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Szerepkör</label>
      <select class="form-select" id="role" name="role" required>
        <option value="">-- Válassz szerepkört --</option>
        <option value="admin">Admin</option>
        <option value="lelkesz">Lelkész</option>
        <option value="penztaros">Pénztáros</option>
        <option value="esperes">Esperes</option>
        <option value="puspok">Püspök</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Mentés</button>
    <a href="index.php" class="btn btn-secondary">Mégsem</a>
  </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
