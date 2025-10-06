<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['org_id'])) {
	$_SESSION['org_id'] = null;
}
if (!isset($_SESSION['role'])) {
	$_SESSION['role'] = null;
}
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

include __DIR__ . '/../../../templates/header.php';
// Csak rendszergazda érheti el
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

// Lekérdezzük a felettes szervezeteket
$stmt = $pdo->query("SELECT id, name FROM organizations ORDER BY name ASC");
$orgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mentés
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $mobile_phone = trim($_POST['mobile_phone']);
    $tax_number = trim($_POST['tax_number']);
    $bank_account = trim($_POST['bank_account']);
    $registry_code = trim($_POST['registry_code']);
    $parent_id = (int)($_POST['parent_id'] ?? 0);

    $stmt = $pdo->prepare("INSERT INTO organizations 
        (name, address, email, phone, mobile_phone, tax_number, bank_account, registry_code, parent_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $address, $email, $phone, $mobile_phone, $tax_number, $bank_account, $registry_code, $parent_id]);

    header("Location: index.php");
    exit;
}
?>

<main class="col-md-9 p-4">
  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <h3 class="mb-0">Új egyházszervezet hozzáadása</h3>
    </div>
    <div class="card-body">
      <form method="post">

        <div class="mb-3">
          <label for="name" class="form-label">Név *</label>
          <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="address" class="form-label">Cím</label>
          <input type="text" name="address" id="address" class="form-control">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <input type="text" name="phone" id="phone" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label for="mobile_phone" class="form-label">Mobil</label>
            <input type="text" name="mobile_phone" id="mobile_phone" class="form-control">
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="tax_number" class="form-label">Adószám</label>
            <input type="text" name="tax_number" id="tax_number" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label for="bank_account" class="form-label">Bankszámlaszám</label>
            <input type="text" name="bank_account" id="bank_account" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label for="registry_code" class="form-label">Nyilvántartási szám</label>
            <input type="text" name="registry_code" id="registry_code" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label for="parent_id" class="form-label">Felettes egyházszervezet *</label>
          <select name="parent_id" id="parent_id" class="form-select" required>
            <option value="">-- válassz --</option>
            <?php foreach ($orgs as $o): ?>
              <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-flex justify-content-end">
          <a href="index.php" class="btn btn-secondary me-2">Mégsem</a>
          <button type="submit" class="btn btn-success">Mentés</button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>