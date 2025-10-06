<?php
require_once __DIR__ . '/../../../core/db.php';
session_start();

// jogosultság ellenőrzés
if (!isset($_SESSION['user_id']) || ($_SESSION['user_permission'] ?? '') !== 'rendszergazda') {
    header("Location: ../../../login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY family_name, given_name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
  <h2>Felhasználók kezelése</h2>
  <p>Kattints a felhasználó sorára a részletek megtekintéséhez.</p>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Felhasználónév</th>
        <th>Név</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr onclick="window.location='view.php?id=<?= $user['id'] ?>'" style="cursor:pointer;">
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['family_name'] . ' ' . $user['given_name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="add.php" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Új felhasználó hozzáadása
  </a>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>