<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: ../../../login.php");
    exit;
}

// Szervezetek lekérdezése
$stmt = $pdo->query("SELECT * FROM organizations ORDER BY name ASC");
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

<main class="col-md-12 ms-sm-auto col-lg-12 px-md-4 py-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
    <h2 class="h4">Egyházszervezetek kezelése</h2>
    <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Új szervezet</a>
  </div>

  <!-- Keresőmező -->
  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Keresés név alapján...">
  </div>

  <!-- Táblázat -->
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Név</th>
          <th>Cím</th>
          <th>Email</th>
          <th>Telefonszám</th>
        </tr>
      </thead>
      <tbody id="orgTable">
        <?php foreach ($orgs as $org): ?>
          <tr onclick="window.location='view.php?id=<?= $org['id'] ?>'" style="cursor:pointer;">
            <td><?= htmlspecialchars($org['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($org['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?php if (!empty($org['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($org['email']) ?>"><?= htmlspecialchars($org['email']) ?></a>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($org['phone'])): ?>
                <a href="tel:<?= htmlspecialchars($org['phone']) ?>"><?= htmlspecialchars($org['phone']) ?></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
  // Keresőmező: név szerinti szűrés
  document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#orgTable tr');
    rows.forEach(row => {
      const name = row.cells[0].textContent.toLowerCase();
      row.style.display = name.includes(filter) ? '' : 'none';
    });
  });
</script>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>