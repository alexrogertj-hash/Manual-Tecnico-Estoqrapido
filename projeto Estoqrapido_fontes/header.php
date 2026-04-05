<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstoqRápido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏗️ EstoqRápido</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="materiais.php">Materiais</a></li>
                <li class="nav-item"><a class="nav-link" href="movimentacao.php">Entrada/Saída</a></li>
                <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
                <?php if($_SESSION['nivel'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-primary" href="usuarios_cad.php">Gerenciar Usuários</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted small">Olá, <?= $_SESSION['nome'] ?></span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Minha Conta
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="perfil.php">Trocar Senha</a></li>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="container">