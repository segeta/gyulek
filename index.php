<?php
require_once __DIR__ . '/core/auth.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

if (!has_org_selected()) {
    header("Location: select_org.php");
    exit;
}

include __DIR__ . '/templates/header.php';

?>
<h1>Kezdőlap</h1>
<p>Üdvözöllek, <?= htmlspecialchars(current_user()['username']) ?>!</p>
<p>Aktív egyházközség: <?= htmlspecialchars($_SESSION['org_id']) ?> (szerep: <?= htmlspecialchars($_SESSION['role']) ?>)</p>
<a href="logout.php">Kijelentkezés</a>
<?php include __DIR__ . '/templates/footer.php'; ?>
