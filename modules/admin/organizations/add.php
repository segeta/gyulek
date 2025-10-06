<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'rendszergazda') {
    header("Location: /gyulek/dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $tax_number = trim($_POST['tax_number']);
    $bank_account = trim($_POST['bank_account']);
    $registry_number = trim($_POST['registry_number']);
    $parent_id = (int)($_POST['parent_id'] ?? 0);

    if ($name && $parent_id) {
        $stmt = $pdo->prepare("INSERT INTO organizations 
            (name, address, email, phone, tax_number, bank_account, registry_number, parent_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $address, $email, $phone, $tax_number, $bank_account, $registry_number, $parent_id]);
        $success = "Az egyházszervezet sikeresen hozzáadva!";
    } else {
        $error = "A név és a felettes szervezet megadása kötelező!";
    }
}

// Felettes szervezetek listája
$parents = $pdo->query("SELECT id, name FROM organizations ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow p-4" style="max-width: 800px; margin: auto;">
    <h3 class="mb-3">Új egyházszervezet hozzáadása</h3>

    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="post">
      <div class="row mb-3">
        <div class="col-md-8">
          <label class="form-label">Név *</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Felettes szervezet *</label>
          <select name="parent_id" class="form-select" required>
            <option value="">-- válassz --</option>
            <?php foreach ($parents as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Cím</label>
          <input type="text" name="address" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Telefonszám</label>
          <input type="text" name="phone" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Adószám</label>
          <input type="text" name="tax_number" class="form-control">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Bankszámlaszám</label>
          <input type="text" name="bank_account" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Nyilvántartási szám</label>
          <input type="text" name="registry_number" class="form-control">
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <a href="organizations.php" class="btn btn-secondary me-2">Mégsem</a>
        <button type="submit" class="btn btn-primary">Mentés</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>