<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

// Lekérdezzük az egyházszervezet adatait
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch();

if (!$org) {
    die("A szervezet nem található.");
}

// Fölérendelt szervezet neve
$parent = null;
if (!empty($org['parent_id'])) {
    $stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
    $stmt->execute([$org['parent_id']]);
    $parent = $stmt->fetchColumn();
}

// Alárendelt szervezetek
$stmt = $pdo->prepare("SELECT id, name FROM organizations WHERE parent_id = ?");
$stmt->execute([$id]);
$children = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kapcsolt személyek
$stmt = $pdo->prepare("
    SELECT u.id, u.family_name, u.given_name, u.email, u.phone, uo.role 
    FROM users u 
    JOIN user_orgs uo ON u.id = uo.user_id 
    WHERE uo.org_id = ?
");
$stmt->execute([$id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow p-4 mb-4" style="max-width: 900px; margin: auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3><?= htmlspecialchars($org['name']) ?></h3>
      <div>
        <a href="edit.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-outline-primary me-2">
          <i class="bi bi-pencil-square"></i> Szerkesztés
        </a>
        <a href="delete.php?id=<?= $org['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Biztosan törölni szeretnéd ezt az egyházszervezetet?');">
          <i class="bi bi-trash"></i> Törlés
        </a>
      </div>
    </div>

    <h5 class="text-secondary mb-3">Egyházszervezet adatai</h5>
    <ul class="list-group mb-4">
      <li class="list-group-item"><strong>Cím:</strong> <?= htmlspecialchars($org['address'] ?? '—') ?></li>
      <li class="list-group-item"><strong>Email:</strong>
        <?php if (!empty($org['email'])): ?>
          <a href="mailto:<?= htmlspecialchars($org['email']) ?>"><?= htmlspecialchars($org['email']) ?></a>
        <?php else: ?>
          —
        <?php endif; ?>
      </li>
      <li class="list-group-item"><strong>Telefonszám:</strong>
        <?php if (!empty($org['phone'])): ?>
          <a href="tel:<?= preg_replace('/\s+/', '', htmlspecialchars($org['phone'])) ?>">
            <?= htmlspecialchars($org['phone']) ?>
          </a>
        <?php else: ?>
          —
        <?php endif; ?>
      </li>
      <li class="list-group-item"><strong>Adószám:</strong> <?= htmlspecialchars($org['tax_number'] ?? '—') ?></li>
      <li class="list-group-item"><strong>Bankszámlaszám:</strong> <?= htmlspecialchars($org['bank_account'] ?? '—') ?></li>
      <li class="list-group-item"><strong>Nyilvántartási szám:</strong> <?= htmlspecialchars($org['registry_number'] ?? '—') ?></li>
      <li class="list-group-item"><strong>Fölérendelt szervezet:</strong> <?= htmlspecialchars($parent ?? '—') ?></li>
    </ul>

    <h5 class="text-secondary mb-3">Alárendelt egyházszervezetek</h5>
    <?php if (count($children) > 0): ?>
      <ul class="list-group mb-4">
        <?php foreach ($children as $child): ?>
          <li class="list-group-item">
            <a href="view.php?id=<?= $child['id'] ?>"><?= htmlspecialchars($child['name']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">Nincsenek alárendelt szervezetek.</p>
    <?php endif; ?>

    <h5 class="text-secondary mb-3">Kapcsolt személyek és pozícióik</h5>
    <?php if (count($members) > 0): ?>
      <ul class="list-group">
        <?php foreach ($members as $m): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
              <?= htmlspecialchars($m['family_name'] . ' ' . $m['given_name']) ?> — <em><?= htmlspecialchars($m['role']) ?></em>
            </span>
            <span>
              <?php if ($m['email']): ?>
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="me-3"><i class="bi bi-envelope"></i></a>
              <?php endif; ?>
              <?php if ($m['phone']): ?>
                <a href="tel:<?= preg_replace('/\s+/', '', htmlspecialchars($m['phone'])) ?>"><i class="bi bi-telephone"></i></a>
              <?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">Ehhez az egyházszervezethez még nincs kapcsolt személy.</p>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>