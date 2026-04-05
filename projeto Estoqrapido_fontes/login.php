<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['nivel'] = $user['nivel'];
        header("Location: index.php");
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card mx-auto bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Bem-vindo</h3>
            <p class="text-muted">Acesse o EstoqRápido</p>
        </div>
        
        <?php if(isset($erro)): ?>
            <div class='alert alert-danger py-2 small'><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">USUÁRIO</label>
                <input type="text" name="usuario" class="form-control form-control-lg" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">SENHA</label>
                <input type="password" name="senha" class="form-control form-control-lg" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">Entrar no Sistema</button>
        </form>
    </div>
</div>
</body>
</html>