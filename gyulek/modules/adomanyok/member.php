<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz']);

if (!isset($_GET['id'])) {
    die("❌ Hiányzó tag ID.");
}

$member_id = (int) $_GET['id'];

// Lekérjük a tag adatait
$stmt = $pdo->prepare("SELECT id, name, birth_date, address 
                       FROM members 
                       WHERE id = ? AND org_id = ?");
$stmt->execute([$member_id, $_SESSION['org_id']]);
$member = $stmt->fetch();

if (!$member) {
    die("❌ Nincs ilyen tag vagy nincs jogosultság.");
}

// Lekérjük a befizetéseket
$stmt = $pdo->prepare("
    SELECT d.date, d.amount, d.note, t.name AS type_name
    FROM donations d
    JOIN donation_types t ON d.type_id = t.id
    WHERE d.org_id = ? AND d.member_id = ?
    ORDER BY d.date DESC
");
$stmt->execute([$_SESSION['org_id'], $member_id]);
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Összes befizetés
$total = array_sum(array_column($donations, 'amount'));

include __DIR__ . '/../../templates/header.php';
?>

<h2><?= htmlspecialchars($member['name']) ?> befizetései</h2>
<p><b>Születési dátum:</b> <?= htmlspecialchars($member['birth_date'] ?? '-') ?><br>
   <b>Lakcím:</b> <?= htmlspecialchars($member['address'] ?? '-') ?></p>

<?php if ($donations): ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Dátum</th>
        <th>Típus</th>
        <th class="text-end">Összeg (Ft)</th>
        <th>Megjegyzés</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($donations as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= htmlspecialchars($row['type_name']) ?></td>
          <td class="text-end"><?= number_format($row['amount'], 0, ',', ' ') ?></td>
          <td><?= nl2br(htmlspecialchars($row['note'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="2" class="text-end">Összesen:</th>
        <th class="text-end"><?= number_format($total, 0, ',', ' ') ?> Ft</th>
        <th></th>
      </tr>
    </tfoot>
  </table>
<?php else: ?>
  <div class="alert alert-info">Ennek a tagnak még nincs rögzített befizetése.</div>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary">
  <i class="bi bi-arrow-left"></i> Vissza a statisztikához
</a>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
