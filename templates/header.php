<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Egyházközség admin</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Ikonok -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Saját CSS -->
  <link rel="stylesheet" href="/gyulek/assets/css/style.css">
</head>
<body>
  <!-- Fejléc -->
  <header class="header bg-primary text-white text-center py-3">
    <h1 class="h4 mb-0">Egyházközség admin rendszer</h1>
    <!-- később ide kerülhet kép, montázs, díszítés -->
  </header>

  <!-- Háromoszlopos layout -->
  <div class="layout d-flex">
    <!-- Bal panel: menü -->
    <?php include __DIR__ . '/menu.php'; ?>

    <!-- Középső tartalom -->
    <main class="content flex-grow-1 p-3">
