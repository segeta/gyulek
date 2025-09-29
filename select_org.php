<?php
require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// lekérjük a felhasználóhoz rendelt szervezeteket
$stmt = $pdo->prepare("
    SELECT o.id, o.name 
    FROM user_orgs uo
    JOIN organizations o ON uo.org_id = o.id
    WHERE uo.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $org_id = (int) ($_POST['org_id'] ?? 0);
    if ($org_id) {
        $_SESSION['org_id'] = $org_id;
        $stmt = $pdo->prepare("SELECT role FROM user_orgs WHERE user_id = ? AND org_id = ?");
		$stmt->execute([$_SESSION['user_id'], $org_id]);
		$_SESSION['role'] = $stmt->fetchColumn();
        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/templates/auth_header.php';  // <<< FIGYELEM: auth_header
?>

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
    <h3 class="text-center mb-3">Szervezet választása</h3>
    <form method="post">
      <div class="mb-3">
        <label for="org_id" class="form-label">Válassz szervezetet</label>
        <select class="form-select" id="org_id" name="org_id" required>
          <option value="">-- válassz --</option>
          <?php foreach ($orgs as $org): ?>
            <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-100">Belépés</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/templates/auth_footer.php'; // <<< FIGYELEM: auth_footer ?>
