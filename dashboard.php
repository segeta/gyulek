<?php
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

/**
 * Lekérdezi a névnapokat a megadott naphoz képest (0 = ma, 1 = holnap, 2 = holnapután).
 */
function getNamedays($pdo, $offset = 0) {
    $date = new DateTime();
    if ($offset !== 0) {
        $date->modify("+$offset day");
    }
    $month = (int)$date->format('n'); // hónap szám (1-12)
    $day   = (int)$date->format('j'); // nap szám (1-31)

    $stmt = $pdo->prepare("SELECT name FROM namedays WHERE month = ? AND day = ?");
    $stmt->execute([$month, $day]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Névnapi adatok
$today    = getNamedays($pdo, 0);
$tomorrow = getNamedays($pdo, 1);
$after    = getNamedays($pdo, 2);

// Napszakos köszöntés
$hour = (int)date('H');
if ($hour < 12) {
    $greeting = "Jó reggelt";
} elseif ($hour < 18) {
    $greeting = "Jó napot";
} else {
    $greeting = "Jó estét";
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3><?= $greeting ?>, <?= htmlspecialchars($_SESSION['name']) ?>!</h3>
          <p>Örülök, hogy újra itt vagy! Lássuk, milyen tennivalók várnak!</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Közeli események -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Közeli események</h5>
          <p>Itt fognak megjelenni a mai, holnapi és holnaputáni események.</p>
        </div>
      </div>
    </div>

    <!-- Születésnapok -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Születésnapok</h5>
          <p>Itt fognak megjelenni a születésnaposok.</p>
        </div>
      </div>
    </div>

    <!-- Névnapok -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Névnapok</h5>
          <p><strong>Ma:</strong> <?= !empty($today) ? implode(", ", $today) : 'nincs' ?></p>
          <p><strong>Holnap:</strong> <?= !empty($tomorrow) ? implode(", ", $tomorrow) : 'nincs' ?></p>
          <p><strong>Holnapután:</strong> <?= !empty($after) ? implode(", ", $after) : 'nincs' ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Teendők -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">FONTOS! Ellenőrizd az eseményeket</h5>
          <p>Ezek az események még megerősítésre várnak. Ha nem rögzíted, automatikusan törlődnek.</p>
          <ul>
            <li>Próba esemény 1</li>
            <li>Próba esemény 2</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Szervezet választó -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h5>Hol kezdjük? Válassz intézményt!</h5>
          <a href="/select_org.php" class="btn btn-primary">Szervezet választása</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
