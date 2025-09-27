<aside class="right-panel p-3 border-start" style="width:250px;">
  <h6 class="fw-bold">Felhasználó</h6>
  <p><?= htmlspecialchars($_SESSION['username']) ?></p>

  <h6 class="fw-bold">Szervezet</h6>
  <p>
    <?php
    // lekérjük az aktuális szervezet nevét
    $stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
    $stmt->execute([$_SESSION['org_id']]);
    $org = $stmt->fetchColumn();
    echo htmlspecialchars($org ?? '-');
    ?>
  </p>
  <a href="/gyulek/select_org.php" class="btn btn-sm btn-outline-primary w-100 mb-3">
    <i class="bi bi-building"></i> Szervezet váltása
  </a>

  <h6 class="fw-bold">Szerepkör</h6>
  <p>
    <?php
    $roles = [
      'admin' => 'Adminisztrátor',
      'lelkesz' => 'Lelkész',
      'penztaros' => 'Pénztáros',
      'tag' => 'Tag',
      'megtekinto' => 'Megtekintő'
    ];
    echo htmlspecialchars($roles[$_SESSION['role']] ?? $_SESSION['role']);
    ?>
  </p>

  <h6 class="fw-bold">Mai nap</h6>
  <p>
    <?php
    setlocale(LC_TIME, "hu_HU.UTF-8");
    echo "Ma " . strftime("%Y. %B %d., %A");
    ?>
  </p>

  <h6 class="fw-bold">Névnap</h6>
  <p>
    <?php
    $nevnapok = [
      '09-27' => 'Adalbert, Vince',
      '09-28' => 'Vencel',
      '09-29' => 'Mihály, Gábor',
    ];
    $today = date("m-d");
    echo $nevnapok[$today] ?? "Nincs adat";
    ?>
  </p>

  <a href="/gyulek/logout.php" class="btn btn-outline-danger w-100 mt-3">
    <i class="bi bi-box-arrow-right"></i> Kijelentkezés
  </a>
</aside>

