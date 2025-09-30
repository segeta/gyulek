<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mindig a dashboard-ra dobunk
header("Location: dashboard.php");
exit;
