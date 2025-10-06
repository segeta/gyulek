<?php
require_once __DIR__ . '/../../../core/db.php';
session_start();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT family_name, given_name FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Nincs ilyen felhasználó!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['confirm'] === 'yes') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php");
        exit;
    } else {
        header("Location: view.php?id=" . $id);
        exit;
    }
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
  <div class="alert alert-warning p-4">
    <h4>Biztosan törlöd a felhasználót?</h4>
    <p><?= htmlspecialchars($user['family_name'] . ' ' . $user['given_name']) ?></p>
    <form method="post">
      <button type="submit" name="confirm" value="yes" class="btn btn-danger">
        Igen, törlöm
      </button>
      <button type="submit" name="confirm" value="no" class="btn btn-secondary">
        Mégsem
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>