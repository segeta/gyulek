<?php
require_once __DIR__ . '/../../core/auth.php';
check_permission(['admin','penztaros','lelkesz']);

// Legutóbbi 10 tranzakció
$stmt = $pdo->prepare("
    SELECT d.id AS donation_id, d.date, d.amount, d.note,
           m.id AS member_id, m.name AS member_name,
           t.name AS type_name
    FROM donations d
    JOIN members m ON d.member_id = m.id
    JOIN donation_types t ON d.type_id = t.id
    WHERE d.org_id = ?
    ORDER BY d.date DESC, d.id DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['org_id']]);
$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Éves összesítés kategóriánként
$currentYear = date("Y");
$stmt = $pdo->prepare("
    SELECT t.name AS type_name, SUM(d.amount) AS total
    FROM donations d
    JOIN donation_types t ON d.type_id = t.id
    WHERE d.org_id = ? AND YEAR(d.date) = ?
    GROUP BY t.name
    ORDER BY total DESC
");
$stmt->execute([$_SESSION['org_id'], $currentYear]);
$yearly = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../templates/header.php';
?>

<h2>Adomány statisztika</h2>

<h3>Legutóbbi 10 tranzakció</h3>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Dátum</th>
      <th>Típus</th>
      <th>Tag</th>
      <th class="text-end">Összeg (Ft)</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($latest as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td><?= htmlspecialchars($row['type_name']) ?></td>
        <td>
          <a href="member.php?id=<?= $row['member_id'] ?>">
            <?= htmlspecialchars($row['member_name']) ?>
          </a>
        </td>
        <td class="text-end"><?= number_format($row['amount'], 0, ',', ' ') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3><?= $currentYear ?>. évi befizetések típusonként</h3>
<canvas id="donationChart" height="100"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('donationChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($yearly, 'type_name')) ?>,
    datasets: [{
      label: 'Összeg (Ft)',
      data: <?= json_encode(array_map('floatval', array_column($yearly, 'total'))) ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: val => val.toLocaleString('hu-HU') + " Ft" }
      }
    }
  }
});
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
