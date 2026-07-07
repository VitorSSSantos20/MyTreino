<?php
require_once __DIR__ . '/helpers.php';

$tituloPagina = $tituloPagina ?? 'MyTreino';
$paginaAtiva  = $paginaAtiva ?? '';
$flash        = flash_get();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina) ?> · MyTreino</title>

    <!-- Bootstrap 5 + Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Fontes do app -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Estilos do MyTreino -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="app-body">

<!-- Navbar fixa e responsiva -->
<nav class="navbar navbar-expand-lg navbar-dark mt-navbar fixed-top" aria-label="Navegação principal">
    <div class="container">
        <a class="navbar-brand mt-brand" href="index.php">
            <span class="mt-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
            My<span class="mt-brand-accent">Treino</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtiva === 'home' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtiva === 'treinos' ? 'active' : '' ?>" href="treinos.php">
                        <i class="bi bi-calendar-week"></i> Meus Treinos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtiva === 'perfil' ? 'active' : '' ?>" href="perfil.php">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-sair" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container app-main">

    <?php if ($flash): ?>
        <!-- Mensagem de feedback -->
        <div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show mt-alert" role="alert" data-auto-dismiss>
            <i class="bi <?= $flash['tipo'] === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill' ?> me-2"></i>
            <?= e($flash['mensagem']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
