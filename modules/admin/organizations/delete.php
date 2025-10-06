<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

// Jogosultság ellenőrzése
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Érvénytelen azonosító.");
}

// Ellenőrzés: létezik-e a szervezet
$stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch();

if (!$org) {
    die("A szervezet nem található.");
}

// Törlés kérése POST-tal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Először töröljük a kapcsolt adatokat (ha vannak)
    $pdo->prepare("DELETE FROM user_orgs WHERE org_id = ?")->execute([$id]);

    // Majd magát a szervezetet
    $pdo->prepare("DELETE FROM organizations WHERE id = ?")->execute([$id]);

    header("Location: index.php?deleted=1");
    exit;
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-5">
  <div class="card shadow-lg p-4 mx-auto" style="max-width: 600px;">
    <h4 class="text-danger mb-3"><i class="bi bi-exclamation-triangle"></i> Figyelmeztetés!</h4>
    <p>
      Biztosan törölni szeretnéd a következő egyházszervezetet?<br>
      <strong><?= htmlspecialchars($org['name']) ?></strong>
    </p>
    <p class="text-muted">A törlés végleges, és az összes kapcsolódó adat (felhasználói kapcsolatok stb.) is törlődik.</p>

    <form method="post" class="mt-4">
      <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Mégsem
        </a>
        <button type="submit" class="btn btn-danger">
          <i class="bi bi-trash"></i> Törlés megerősítése
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>