<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Köszöntés napszak szerint
$hour = (int)date('H');
if ($hour < 9) {
    $greeting = "Jó reggelt";
} elseif ($hour < 18) {
    $greeting = "Jó napot";
} else {
    $greeting = "Jó estét";
}

include __DIR__ . '/templates/header.php';
?>

<main class="col-md-12 col-12 p-4">
  <h2><?= $greeting ?>, <?= htmlspecialchars($_SESSION['given_name'] ?? $_SESSION['username']) ?>!</h2>
  <p>Örülök, hogy újra itt vagy! Lássuk, milyen tennivalók várnak!</p>

  <div class="row g-3">
    <!-- Bal oldal 2/3 -->
    <div class="col-md-8">

      <!-- Közelgő események -->
      <div class="card shadow p-3 mb-3">
        <h5 class="card-title">Közelgő események</h5>
        <div class="row">
          <div class="col-md-4">
            <h6>Ma</h6>
            <ul class="list-unstyled">
              <li>08:00 – Hittan óra (Apostag)</li>
              <li>09:00 – Tárgyalás (Budapest)</li>
              <li>16:30 – Áhítat (Kiskőrös)</li>
            </ul>
          </div>
          <div class="col-md-4">
            <h6>Holnap</h6>
            <ul class="list-unstyled">
              <li>Nincs esemény</li>
            </ul>
          </div>
          <div class="col-md-4">
            <h6>Holnapután</h6>
            <ul class="list-unstyled">
              <li>18:00 – Közgyűlés (Soltvadkert)</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Tennivalók -->
      <div class="card shadow p-3">
        <h5 class="card-title">Tennivalók</h5>
        <p>Szuper! Minden tervezett eseményt rögzítettél!</p>
      </div>
    </div>

    <!-- Jobb oldal 1/3 -->
    <div class="col-md-4">
      <!-- Születésnapok -->
      <div class="card shadow p-3 mb-3">
        <h5 class="card-title">Születésnaposok</h5>
        <ul class="list-unstyled">
          <li>Ma: Komáromi Petra Gabriella</li>
          <li>Holnap: Jakab Béla</li>
          <li>Holnapután: Kis Péter, Rózsa Mária</li>
        </ul>
      </div>

      <!-- Névnapok -->
      <div class="card shadow p-3">
        <h5 class="card-title">Névnapok</h5>
        <ul class="list-unstyled">
          <li>Ma: Mihály, Rafael</li>
          <li>Holnap: Malvin</li>
          <li>Holnapután: Petra</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Intézmény választás gomb -->
  <div class="text-center mt-4">
    <a href="select_org.php" class="btn btn-primary">Hol kezdjük? Válassz intézményt!</a>
  </div>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
