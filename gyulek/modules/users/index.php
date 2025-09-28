<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

// Csak admin férhet hozzá
if (!isset($_SESSION['user_id'])) {
    header("Location: /gyulek/login.php");
    exit;
}

// Ellenőrzés: admin szerepkör
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_orgs WHERE user_id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$isAdmin = $stmt->fetchColumn() > 0;

if (!$isAdmin) {
    die("Nincs jogosultságod a felhasználókezeléshez.");
}

// Lekérjük az összes felhasználót
$users = $pdo->query("SELECT id, username, name FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Lekérjük a hozzárendeléseket
$stmt = $pdo->query("
    SELECT uo.user_id, o.name AS org_name, uo.role
    FROM user_orgs uo
    JOIN organizations o ON uo.org_id = o.id
    ORDER BY uo.user_id, o.name
");
$user_orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Átalakítjuk asszociatívvá
$relations = [];
foreach ($user_orgs as $uo) {
    $relations[$uo['user_id']][] = $uo['org_name'] . " (" . ucfirst($uo['role']) . ")";
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Felhasználók kezelése</h2>
  <a href="add.php" class="btn btn-success">
    <i class="bi bi-person-plus"></i> Új felhasználó
  </a>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Felhasználónév</th>
      <th>Név</th>
      <th>Szervezetek és szerepkörök</th>
      <th>Műveletek</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['name']) ?></td>
        <td>
          <?php
            if (!empty($relations[$user['id']])) {
                echo implode("<br>", $relations[$user['id']]);
            } else {
                echo "<span class='text-muted'>Nincs hozzárendelve</span>";
            }
          ?>
        </td>
        <td>
          <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Szerkesztés
          </a>
          <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i> Törlés
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
