<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['org_id'])) {
    header("Location: dashboard.php");
    exit;
}

// lekérdezzük az aktuális szervezetet
$stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
$stmt->execute([$_SESSION['org_id']]);
$org = $stmt->fetch();

include __DIR__ . '/templates/header.php';
?>

<main class="col-md-12 col-12 p-4">
  <h2><?= htmlspecialchars($org['name']) ?> – Kezdőoldal</h2>
  <p>Itt láthatod az egyházszervezet aktuális eseményeit, születésnaposait, névnaposait és teendőit.</p>

  <div class="row g-4">
    <div class="col-md-8">
      <!-- közelgő események és teendők -->
      <div class="card shadow p-3 mb-4">
        <h5 class="card-title">Közelgő események</h5>
        <ul class="mb-0">
          <?php
          $today = date('Y-m-d');
          $limit = date('Y-m-d', strtotime('+3 days'));

          $stmt = $pdo->prepare("
              SELECT e.start_time, et.name AS type, e.location
              FROM events e
              JOIN event_types et ON e.type_id = et.id
              WHERE e.org_id = ? AND DATE(e.start_time) BETWEEN ? AND ?
              ORDER BY e.start_time ASC
          ");
          $stmt->execute([$_SESSION['org_id'], $today, $limit]);
          $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($events) {
              foreach ($events as $ev) {
                  echo "<li>" . date('Y.m.d H:i', strtotime($ev['start_time'])) .
                       " – " . htmlspecialchars($ev['type']) .
                       " (" . htmlspecialchars($ev['location']) . ")</li>";
              }
          } else {
              echo "<li>Nincs közelgő esemény a következő 3 napban.</li>";
          }
          ?>
        </ul>
      </div>
    </div>

    <div class="col-md-4">
      <!-- születésnaposok -->
      <div class="card shadow p-3 mb-4">
        <h5 class="card-title">Születésnaposok</h5>
        <ul class="mb-0">
          <?php
          $today = new DateTime();
          $dates = [
            'ma' => $today->format('m-d'),
            'holnap' => $today->modify('+1 day')->format('m-d'),
            'holnapután' => $today->modify('+1 day')->format('m-d'),
          ];

          $stmt = $pdo->prepare("
              SELECT name, DATE(birth_date) AS bdate
              FROM members
              WHERE org_id = ?
          ");
          $stmt->execute([$_SESSION['org_id']]);
          $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $found = false;
          foreach ($dates as $label => $md) {
              $printed = false;
              foreach ($members as $m) {
                  if (substr($m['bdate'], 5, 5) === $md) {
                      if (!$printed) {
                          echo "<li class='fw-bold'>" . ucfirst($label) . ":</li>";
                          $printed = true;
                      }
                      $age = date('Y') - date('Y', strtotime($m['bdate']));
                      if ($label !== 'ma') $age++;
                      echo "<li>" . htmlspecialchars($m['name']) . " ({$age})</li>";
                      $found = true;
                  }
              }
          }
          if (!$found) {
              echo "<li>Nincs születésnapos a következő 3 napban.</li>";
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
