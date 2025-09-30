<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Köszönés napszak szerint
$hour = date('H');
if ($hour < 12) {
    $greeting = "Jó reggelt";
} elseif ($hour < 18) {
    $greeting = "Jó napot";
} else {
    $greeting = "Jó estét";
}

// TODO: adatbázisból töltjük majd
$events = [];
$birthdays = [];
$namedays = [];
$tasks = [];

include __DIR__ . '/templates/header.php';
?>

<div class="container-fluid">
  <div class="row">
    <!-- Bal oldali panel: a header már tartalmazza a menüt, ezért itt nem kell külön -->

    <!-- Középső tartalom -->
    <main class="col-md-12 col-12 p-4">
      <h2><?= $greeting ?>, <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']) ?>!</h2>
      <p>Üdvözöllek virtuális irodádban, ahol az összes általad kezelt szervezetről láthatod a legfontosabb tudnivalókat.</p>

      <div class="row g-3">
        <!-- Közelgő események -->
        <div class="col-md-4">
          <div class="card shadow p-3 h-100">
            <h5 class="card-title">Közelgő események</h5>
            <ul class="list-unstyled">
              <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                  <li><?= htmlspecialchars($event['date']) ?> – <?= htmlspecialchars($event['title']) ?></li>
                <?php endforeach; ?>
              <?php else: ?>
                <li><em>Nincs tervben program a következő három napra.</em></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <!-- Szülinapok -->
        <div class="col-md-4">
          <div class="card shadow p-3 h-100">
            <h5 class="card-title">Születésnapok</h5>
            <ul class="list-unstyled">
              <?php if (!empty($birthdays)): ?>
                <?php foreach ($birthdays as $b): ?>
                  <li><?= htmlspecialchars($b['date']) ?> – <?= htmlspecialchars($b['name']) ?></li>
                <?php endforeach; ?>
              <?php else: ?>
                <li><em>Senki sem ünnepli a születésnapját a következő három napban.</em></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <!-- Névnapok -->
        <div class="col-md-4">
          <div class="card shadow p-3 h-100">
            <h5 class="card-title">Névnapok</h5>
            <ul class="list-unstyled">
              <?php if (!empty($namedays)): ?>
                <?php foreach ($namedays as $n): ?>
                  <li><?= htmlspecialchars($n['date']) ?> – <?= htmlspecialchars($n['name']) ?></li>
                <?php endforeach; ?>
              <?php else: ?>
                <li><em>Senki sem ünnepli a névnapját a következő három napban.</em></li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Teendők -->
      <div class="card shadow p-3 mt-4">
        <h5 class="card-title">Teendők</h5>
        <ul class="list-unstyled">
          <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
              <li>⚠️ <?= htmlspecialchars($task) ?></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><em>Szuper! Minden tervezett eseményt rögzítettél!</em></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Szervezet választás -->
      <div class="mt-4 text-center">
        <a href="select_org.php" class="btn btn-primary">Hol kezdjük? Válassz intézményt!</a>
      </div>
    </main>

    <!-- Jobb oldali panel -->
      <?php include __DIR__ . '/templates/footer.php'; ?>
      </div>
</div>

<?php include __DIR__ . '/templates/auth_footer.php'; ?>
