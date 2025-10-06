<?php
/**
 * Telefonszám formázó segédfüggvény
 * 
 * Bemenet: bármilyen formátumú telefonszám (pl. 06201234567 vagy +36201234567)
 * Kimenet: +36 20 123 4567 formátumban
 */

function format_phone($number) {
    if (empty($number)) return '';

    // Csak számokat tart meg (és a + jelet az elején)
    $number = preg_replace('/[^0-9+]/', '', $number);

    // Ha 06-tal kezdődik → +36
    if (strpos($number, '06') === 0) {
        $number = '+36' . substr($number, 2);
    }

    // Ha 0036-tal kezdődik → +36
    if (strpos($number, '0036') === 0) {
        $number = '+36' . substr($number, 4);
    }

    // Ha csak 9 számjegy (pl. 201234567), hozzáadjuk a +36 előhívót
    if (preg_match('/^[0-9]{9}$/', $number)) {
        $number = '+36' . $number;
    }

    // Formázás: +36 20 123 4567
    if (preg_match('/^\+36([0-9]{2})([0-9]{3})([0-9]{4})$/', $number, $m)) {
        return "+36 {$m[1]} {$m[2]} {$m[3]}";
    }

    return $number; // ha nem illeszkedik a mintára, hagyjuk változatlanul
}