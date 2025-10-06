<?php
session_start();
require_once __DIR__ . '/../../../core/db.php';
require_once __DIR__ . '/../../../core/functions.php';

// Csak rendszergazda
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Szervezet lekérése
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE id = ?");
$stmt->execute([$id]);
$org = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$org) {
    echo "<div class='alert alert-danger'>A kért egyházszervezet nem található.</div>";
    exit;
}

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

    if ($name && $email) {
        $stmt = $pdo->prepare("
            UPDATE organizations 
            SET name = ?, address = ?, email = ?, phone = ?, mobile_phone = ?, 
                tax_number = ?, bank_account = ?, registry_code = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $address, $email, $phone, $mobile_phone, $tax_number, $bank_account, $registry_code, $id]);

        header("Location: view.php?id=" . $id);
        exit;
    } else {
        $error = "A név és az e-mail megadása kötelező.";
    }
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/menu.php';
?>

<div class="container mt-4">
  <div class="col-md-8 mx-auto">
    <div class="card shadow p-4">
      <h3 class="mb-3">Egyházszervezet adatainak szerkesztése</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label">Név *</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($org['name']) ?>" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label">Cím</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($org['address']) ?>">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($org['email']) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Telefonszám</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($org['phone']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Mobiltelefonszám</label>
            <input type="text" name="mobile_phone" class="form-control" value="<?= htmlspecialchars($org['mobile_phone']) ?>">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Adószám</label>
            <input type="text" name="tax_number" class="form-control" value="<?= htmlspecialchars($org['tax_number']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Bankszámlaszám</label>
            <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($org['bank_account']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Nyilvántartási szám</label>
            <input type="text" name="registry_code" class="form-control" value="<?= htmlspecialchars($org['registry_code']) ?>">
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="view.php?id=<?= $org['id'] ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Mégsem
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Mentés
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
