<?php
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';
session_start();

if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch();

if (!$org) {
    die("A szervezet nem található.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $tax_number = trim($_POST['tax_number']);
    $bank_account = trim($_POST['bank_account']);
    $registry_number = trim($_POST['registry_number']);

    $stmt = $pdo->prepare("UPDATE organizations 
        SET name=?, address=?, email=?, phone=?, tax_number=?, bank_account=?, registry_number=? 
        WHERE id=?");
    $stmt->execute([$name, $address, $email, $phone, $tax_number, $bank_account, $registry_number, $id]);

    header("Location: view.php?id=$id");
    exit;
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow p-4" style="max-width: 800px; margin: auto;">
    <h3 class="mb-3">Egyházszervezet szerkesztése</h3>

    <form method="post">
      <div class="row mb-3">
        <div class="col-md-8">
          <label class="form-label">Név</label>
          <input type="text" name="name" value="<?= htmlspecialchars($org['name']) ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Felettes szervezet</label>
          <input type="text" value="<?= htmlspecialchars(get_org_name($pdo, $org['parent_id'])) ?>" class="form-control" disabled>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6"><label class="form-label">Cím</label>
          <input type="text" name="address" value="<?= htmlspecialchars($org['address']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($org['email']) ?>" class="form-control"></div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6"><label class="form-label">Telefonszám</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($org['phone']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Adószám</label>
          <input type="text" name="tax_number" value="<?= htmlspecialchars($org['tax_number']) ?>" class="form-control"></div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6"><label class="form-label">Bankszámlaszám</label>
          <input type="text" name="bank_account" value="<?= htmlspecialchars($org['bank_account']) ?>" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Nyilvántartási szám</label>
          <input type="text" name="registry_number" value="<?= htmlspecialchars($org['registry_number']) ?>" class="form-control"></div>
      </div>

      <div class="d-flex justify-content-end">
        <a href="view.php?id=<?= $id ?>" class="btn btn-secondary me-2">Mégsem</a>
        <button type="submit" class="btn btn-primary">Mentés</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>