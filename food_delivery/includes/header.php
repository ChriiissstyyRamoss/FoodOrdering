<?php
// Shared header + navigation bar. Set $pageTitle and $active before including.
$pageTitle = $pageTitle ?? 'FoodRush';
$active = $active ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Christy Ramos">
    <title><?= e($pageTitle) ?> | FoodRush by Christy Ramos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-bag-heart-fill me-1"></i>FoodRush
            <small class="d-block fw-normal" style="font-size:.65rem;opacity:.85;line-height:1">by Christy Ramos</small>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= $active==='home'?'active':'' ?>" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= $active==='orders'?'active':'' ?>" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link <?= $active==='menu'?'active':'' ?>" href="menu.php">Menu</a></li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['err'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($_GET['err']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
