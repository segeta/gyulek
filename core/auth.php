<?php
session_start();
require_once __DIR__ . '/db.php';

// Bejelentkezett-e?
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Jelenlegi org kiválasztva?
function has_org_selected() {
    return isset($_SESSION['org_id']);
}

// Felhasználó adatai
function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Jogosultság ellenőrzés
function check_permission($required_roles = []) {
    if (!has_org_selected()) {
        header("Location: /gyulek/select_org.php");
        exit;
    }

    $role = $_SESSION['role'] ?? 'megtekinto';
    if (!in_array($role, $required_roles)) {
        die("Nincs jogosultságod ehhez a művelethez.");
    }
}
?>
