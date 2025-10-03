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
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['family_name']      = $user['family_name'];
        $_SESSION['given_name']      = $user['given_name'];
        $_SESSION['user_permission'] = $user['user_permission']; // EZ HIÁNYZIK

       header("Location: dashboard.php");
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
      <div class="mb-3 position-relative">
        <label for="password" class="form-label">Jelszó</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password" required>
          <button type="button" class="btn btn-outline-secondary" id="togglePassword">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Belépés</button>
    </form>
  </div>
</div>

<script>
// Jelszó felfedés/elrejtés
document.getElementById('togglePassword').addEventListener('click', function () {
  const passwordField = document.getElementById('password');
  const icon = this.querySelector('i');
  if (passwordField.type === 'password') {
    passwordField.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    passwordField.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
});
</script>

<?php include __DIR__ . '/templates/auth_footer.php'; ?>
