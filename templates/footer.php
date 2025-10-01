<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../core/db.php';

// lekérdezzük a user szervezeteit
$orgs = [];
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT o.id, o.name
        FROM user_orgs uo
        JOIN organizations o ON uo.org_id = o.id
        WHERE uo.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ha váltás történt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['org_id'])) {
    $_SESSION['org_id'] = (int)$_POST['org_id'];
    header("Location: /gyulek/org_home.php");
    exit;
}

// mai dátum
$today = strftime("%Y. %B %d. %A", time());

// névnap (korábban beállított logikával)
$namedayText = "";
$todayMonth = (int)date('m');
$todayDay   = (int)date('d');
$stmt = $pdo->prepare("SELECT name FROM namedays WHERE month = ? AND day = ?");
$stmt->execute([$todayMonth, $todayDay]);
$namedays = $stmt->fetchAll(PDO::FETCH_COLUMN);
if ($namedays) {
    $namedayText = implode(", ", $namedays);
}
?>

</main>


    <!-- Jobb oldali információs panel -->
<aside class="right-panel p-3 border-start" style="width:250px;">
      <h6 class="fw-bold">Felhasználó</h6>
      <p>
      <?= htmlspecialchars($_SESSION['family_name'] ?? '') ?>
      <?= htmlspecialchars($_SESSION['given_name'] ?? $_SESSION['username']) ?>
      </p>

      <h6 class="fw-bold">Szervezet</h6>
      <p>
        <?php
        $stmt = $pdo->prepare("SELECT name FROM organizations WHERE id = ?");
        $stmt->execute([$_SESSION['org_id']]);
        $org = $stmt->fetchColumn();
        echo htmlspecialchars($org ?? '-');
        ?>
      </p>
      
      <h6 class="fw-bold">Szerepkör</h6>
      <p>
        <?php
        $roles = [
          'rendszergazda' => 'Rendszergazda',
          'lelkesz' => 'Lelkész',
          'penztaros' => 'Pénztáros',
          'esperes' => 'Esperes',
          'puspok' => 'Püspök',
          'tag' => 'Tag',
          'megtekinto' => 'Megtekintő'
        ];
        echo htmlspecialchars($roles[$_SESSION['role']] ?? $_SESSION['role']);
        ?>
      </p>

		<?php if ($orgs): ?>
      <h6 class="fw-bold">Szervezet váltása</h6>
      <form method="post" class="mb-3">
        <select name="org_id" class="form-select mb-2" onchange="this.form.submit()">
          <option value="">-- válassz --</option>
          <?php foreach ($orgs as $org): ?>
            <option value="<?= $org['id'] ?>" <?= (isset($_SESSION['org_id']) && $_SESSION['org_id'] == $org['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($org['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>

      <h6 class="fw-bold">Mai nap</h6>
      <p>
        <?php
        setlocale(LC_TIME, "hu_HU.UTF-8");
        echo "Ma " . strftime("%Y. %B %d., %A") . " van.";
        ?>
      </p>

	<?php
		// Mai dátum
		$todayMonth = date('n'); // 1-12
		$todayDay   = date('j'); // 1-31

		$stmt = $pdo->prepare("SELECT name FROM namedays WHERE month = ? AND day = ?");
		$stmt->execute([$todayMonth, $todayDay]);
		$namedays = $stmt->fetchAll(PDO::FETCH_COLUMN);
	?>

		<h6 class="fw-bold mt-3">Névnap</h6>
	<p>
		<?php if ($namedays): ?>
			<?= htmlspecialchars(implode(', ', $namedays)) ?>
		<?php else: ?>
    		Ma nincs névnap.
		<?php endif; ?>
	</p>


      <a href="/gyulek/logout.php" class="btn btn-outline-danger w-100 mt-3">
        <i class="bi bi-box-arrow-right"></i> Kijelentkezés
      </a>
    </aside>
  </div> <!-- layout vége -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>