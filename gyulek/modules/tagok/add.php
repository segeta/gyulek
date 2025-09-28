<?php
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';
session_start();

// Csak bejelentkezett felhasználók férhetnek hozzá
if (!isset($_SESSION['user_id'])) {
    header("Location: /gyulek/login.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $birth_name = trim($_POST['birth_name'] ?? '');
    $birth_place = trim($_POST['birth_place'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$name) {
        $error = "A név megadása kötelező!";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO members (name, birth_name, birth_place, birth_date, address, phone, email, org_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name,
            $birth_name,
            $birth_place,
            $birth_date ?: null,
            $address,
            $phone,
            $email,
            $_SESSION['org_id'] ?? null  // aktuális szervezethez kötjük
        ]);

        header("Location: index.php");
        exit;
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="container mt-4">
  <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
    <h2 class="text-center mb-4">Új tag hozzáadása</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Név *</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>

      <div class="mb-3">
        <label for="birth_name" class="form-label">Születési név</label>
        <input type="text" class="form-control" id="birth_name" name="birth_name">
      </div>

      <div class="mb-3">
        <label for="birth_place" class="form-label">Születési hely</label>
        <input type="text" class="form-control" id="birth_place" name="birth_place">
      </div>

      <div class="mb-3">
        <label for="birth_date" class="form-label">Születési dátum</label>
        <input type="date" class="form-control" id="birth_date" name="birth_date">
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Lakcím</label>
        <input type="text" class="form-control" id="address" name="address">
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Telefon</label>
        <input type="text" class="form-control" id="phone" name="phone">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="email" name="email">
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Mentés</button>
        <a href="index.php" class="btn btn-secondary">Mégsem</a>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
