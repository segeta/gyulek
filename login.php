<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && verify_password($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];

        // Megnézzük, admin-e?
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_orgs WHERE user_id = ? AND role = 'rendszergazda'");
        $stmt->execute([$user['id']]);
        $isAdmin = $stmt->fetchColumn() > 0;

        if ($isAdmin) {
            header("Location: /gyulek/modules/users/index.php");
        } else {
            header("Location: select_org.php");
        }
        exit;
    } else {
        $error = "Hibás felhasználónév vagy jelszó!";
    }
}

include __DIR__ . '/templates/auth_header.php';
?>

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
    <h3 class="text-center mb-3">Bejelentkezés</h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Felhasználónév</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Jelszó</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Belépés</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/templates/auth_footer.php'; ?>
