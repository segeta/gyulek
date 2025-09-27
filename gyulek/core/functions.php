<?php
// Jelszó hash-elés
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Jelszó ellenőrzés
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
?>
