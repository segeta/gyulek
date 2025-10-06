<?php
session_start();
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Lekérdezzük a szervezetet
$stmt = $pdo->prepare("
    SELECT * FROM organizations WHERE id = ?
");
$stmt->execute([$id]);
$org = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$org) {
    echo "<div class='alert alert-danger'>A kért egyházszervezet nem található.</div>";
    exit;
}

// Fölérendelt szervezet
$parentOrg = null;
if ($org['parent_id']) {
    $stmt = $pdo->prepare("SELECT id, name FROM organizations WHERE id = ?");
    $stmt->execute([$org['parent_id']]);
    $parentOrg = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Alárendelt szervezetek
$stmt = $pdo->prepare("SELECT id, name FROM organizations WHERE parent_id = ?");
$stmt->execute([$id]);
$childOrgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kapcsolt személyek
$stmt = $pdo->prepare("
    SELECT u.family_name, u.given_name, u.email, u.phone, u.id AS user_id, uo.role
    FROM user_orgs uo
    JOIN users u ON uo.user_id = u.id
    WHERE uo.org_id = ?
");
$stmt->execute([$id]);
$relatedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';

?>

<div class="container mt-4">
  <div class="col-md-8 mx-auto">
    <div class="card shadow p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= htmlspecialchars($org['name']) ?></h3>
        <div>
          <a href="edit.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil"></i> Szerkesztés
          </a>
          <a href="delete.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törlöd ezt az egyházszervezetet?');">
            <i class="bi bi-trash"></i> Törlés
          </a>
        </div>
      </div>

      <h5 class="text-secondary mb-3">Egyházszervezet adatai</h5>
      <table class="table table-bordered">
        <tbody>
          <tr><th>Cím</th><td><?= htmlspecialchars($org['address']) ?></td></tr>
          <tr><th>Email</th><td><a href="mailto:<?= htmlspecialchars($org['email']) ?>"><?= htmlspecialchars($org['email']) ?></a></td></tr>
          <tr><th>Telefonszám</th><td><a href="tel:<?= htmlspecialchars($org['phone']) ?>"><?= htmlspecialchars($org['phone']) ?></a></td></tr>
          <tr><th>Mobiltelefonszám</th><td><a href="tel:<?= htmlspecialchars($org['mobile_phone']) ?>"><?= htmlspecialchars($org['mobile_phone']) ?></a></td></tr>
          <tr><th>Adószám</th><td><?= htmlspecialchars($org['tax_number']) ?></td></tr>
          <tr><th>Bankszámlaszám</th><td><?= htmlspecialchars($org['bank_account']) ?></td></tr>
          <tr><th>Nyilvántartási szám</th><td><?= htmlspecialchars($org['registry_code']) ?></td></tr>
        </tbody>
      </table>

      <h5 class="text-secondary mt-4 mb-3">Fölérendelt egyházszervezet</h5>
      <?php if ($parentOrg): ?>
        <p><a href="view.php?id=<?= htmlspecialchars($parentOrg['id']) ?>"><?= htmlspecialchars($parentOrg['name']) ?></a></p>
      <?php else: ?>
        <p>—</p>
      <?php endif; ?>

      <h5 class="text-secondary mt-4 mb-3">Alárendelt egyházszervezetek</h5>
      <?php if (count($childOrgs) > 0): ?>
        <ul class="list-group mb-3">
          <?php foreach ($childOrgs as $child): ?>
            <li class="list-group-item">
              <a href="view.php?id=<?= htmlspecialchars($child['id']) ?>"><?= htmlspecialchars($child['name']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Nincsenek alárendelt szervezetek.</p>
      <?php endif; ?>

      <h5 class="text-secondary mt-4 mb-3">Kapcsolt személyek és pozícióik</h5>
      <?php if (count($relatedUsers) > 0): ?>
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>Név</th>
              <th>Szerepkör</th>
              <th>Email</th>
              <th>Telefon</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($relatedUsers as $user): ?>
              <tr>
                <td>
                  <a href="/modules/admin/users/view.php?id=<?= htmlspecialchars($user['user_id']) ?>">
                    <?= htmlspecialchars($user['family_name'] . ' ' . $user['given_name']) ?>
                  </a>
                </td>
                <td><?= htmlspecialchars(ucfirst(str_replace(['lelkesz', 'penztaros', 'felugyelo', 'esperes', 'puspok', 'admin'], ['Lelkész', 'Pénztáros', 'Felügyelő', 'Esperes', 'Püspök', 'Adminisztrátor'], $user['role']))) ?></td>
                <td><a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a></td>
                <td><a href="tel:<?= htmlspecialchars($user['phone']) ?>"><?= htmlspecialchars($user['phone']) ?></a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Nincsenek kapcsolt személyek.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
