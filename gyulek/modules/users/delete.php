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

$id = (int)($_GET['id'] ?? 0);

// Saját törlés tiltása
if ($id === (int)$_SESSION['user_id']) {
    die("Nem törölheted saját magadat!");
}

// Felhasználó lekérdezése
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Nincs ilyen felhasználó.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // Kapcsolatok törlése
        $stmt = $pdo->prepare("DELETE FROM user_orgs WHERE user_id = ?");
        $stmt->execute([$id]);

        // Felhasználó törlése
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <h2>Felhasználó törlése</h2>

  <div class="alert alert-warning">
    Biztosan törölni szeretnéd a következő felhasználót?<br>
    <strong><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['name']) ?>)</strong>
  </div>

  <form method="post">
    <input type="hidden" name="confirm" value="yes">
    <button type="submit" class="btn btn-danger">Igen, törlöm</button>
    <a href="index.php" class="btn btn-secondary">Mégsem</a>
  </form>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
