<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/menu.php';

session_start();

// Csak rendszergazda érheti el
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

// Ellenőrizzük, létezik-e a szervezet
$stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$org) {
    echo "<div class='alert alert-danger'>A megadott szervezet nem található.</div>";
    include __DIR__ . '/../../../templates/footer.php';
    exit;
}

// Törlés megerősítése után
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM organizations WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger m-3'>
                <strong>Hiba:</strong> A szervezet nem törölhető, mert kapcsolódó rekordok léteznek más táblákban.
              </div>";
    }
}
?>

<main class="col-md-12 p-4">
  <div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
      <h3 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Szervezet törlése</h3>
    </div>
    <div class="card-body text-center">
      <p class="fs-5">Biztosan törölni szeretnéd az alábbi szervezetet?</p>
      <h4 class="fw-bold text-danger"><?= htmlspecialchars($org['name']) ?></h4>

      <p class="mt-3 text-muted">Ez a művelet nem vonható vissza, és az összes hozzá tartozó adat elveszik.</p>

      <form method="post" class="mt-4">
        <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='view.php?id=<?= $id ?>'">Mégsem</button>
        <button type="submit" name="confirm_delete" class="btn btn-danger">Törlés megerősítése</button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>