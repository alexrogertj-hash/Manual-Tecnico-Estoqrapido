<?php
require 'config.php';
verificarLogado();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    
    if ($stmt->execute([$nova_senha, $_SESSION['usuario_id']])) {
        $msg = "Senha atualizada com sucesso!";
    } else {
        $msg = "Erro ao atualizar a senha.";
    }
}

include 'header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="mb-4 text-center">
            <h2 class="fw-bold">Segurança da Conta</h2>
            <p class="text-muted small">Mantenha seu acesso protegido alterando sua senha regularmente.</p>
        </div>

        <?php if($msg !== ""): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✨ <?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card p-4 shadow-sm border-0">
            <div class="text-center mb-4">
                <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                    <span style="font-size: 2rem;">🔒</span>
                </div>
                <h5 class="fw-bold">Alterar Minha Senha</h5>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOVA SENHA</label>
                    <input type="password" name="senha" class="form-control form-control-lg" 
                           placeholder="Digite a nova senha" required minlength="4">
                    <div class="form-text mt-2 small text-muted">
                        Escolha uma senha segura que você não utilize em outros sites.
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                        Atualizar Senha
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-4">
            <p class="small text-muted">
                Logado como: <strong><?= $_SESSION['nome'] ?></strong>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>