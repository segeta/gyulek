<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/login.php");
    exit;
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-5">
  <h2 class="mb-4">Rendszerkezelés</h2>
  <div class="row g-4">

    <div class="col-md-6">
      <a href="users/index.php" class="text-decoration-none">
        <div class="card shadow text-center p-4 h-100 border-danger">
          <h4 class="text-danger"><i class="bi bi-people"></i> Felhasználókezelés</h4>
          <p class="text-muted">Új felhasználók hozzáadása, meglévők szerkesztése, jogosultságok kezelése.</p>
        </div>
      </a>
    </div>

    <div class="col-md-6">
      <a href="organizations/index.php" class="text-decoration-none">
        <div class="card shadow text-center p-4 h-100 border-primary">
          <h4 class="text-primary"><i class="bi bi-building"></i> Egyházszervezet kezelés</h4>
          <p class="text-muted">Egyházszervezetek adatainak kezelése, új szervezetek rögzítése.</p>
        </div>
      </a>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>