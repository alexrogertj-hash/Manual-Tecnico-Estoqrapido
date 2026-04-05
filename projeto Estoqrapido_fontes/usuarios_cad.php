<?php
require 'config.php';
verificarLogado();

// Proteção: Apenas administradores podem acessar esta página
if ($_SESSION['nivel'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$mensagem = "";
$tipo_alerta = "";

// Lógica de Cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_cadastrar'])) {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $nivel = $_POST['nivel'];

    // Verificar se o nome de usuário já existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check->execute([$usuario]);

    if ($check->rowCount() > 0) {
        $mensagem = "Erro: Este nome de usuário já está em uso.";
        $tipo_alerta = "danger";
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, usuario, senha, nivel) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nome, $usuario, $senha, $nivel])) {
            $mensagem = "Usuário cadastrado com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao cadastrar usuário.";
            $tipo_alerta = "danger";
        }
    }
}

// Busca todos os usuários para listar na tabela
$lista_usuarios = $pdo->query("SELECT id, nome, usuario, nivel FROM usuarios ORDER BY nome ASC")->fetchAll();

include 'header.php';
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2 class="fw-bold">Gestão de Usuários</h2>
        <p class="text-muted small">Cadastre novos colaboradores e gerencie permissões.</p>
    </div>

    <!-- Formulário de Cadastro -->
    <div class="col-md-4">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Novo Usuário</h5>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?= $tipo_alerta ?> py-2 small"><?= $mensagem ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOME COMPLETO</label>
                    <input type="text" name="nome" class="form-control" placeholder="Ex: João Silva" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">USUÁRIO (LOGIN)</label>
                    <input type="text" name="usuario" class="form-control" placeholder="Ex: joao.silva" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">SENHA INICIAL</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">NÍVEL DE ACESSO</label>
                    <select name="nivel" class="form-select">
                        <option value="usuario">Usuário Padrão</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" name="btn_cadastrar" class="btn btn-primary w-100 fw-bold">Criar Conta</button>
            </form>
        </div>
    </div>

    <!-- Listagem de Usuários -->
    <div class="col-md-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Usuários Ativos</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>Nível</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lista_usuarios as $u): ?>
                        <tr>
                            <td class="align-middle fw-semibold"><?= $u['nome'] ?></td>
                            <td class="align-middle text-muted"><?= $u['usuario'] ?></td>
                            <td class="align-middle">
                                <span class="badge <?= $u['nivel'] == 'admin' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= strtoupper($u['nivel']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <!-- Link para a página de reset que você já tem -->
                                <form action="admin_reset.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                                    <button type="submit" name="resetar" class="btn btn-sm btn-outline-warning border-0" title="Resetar Senha para 123456">
                                        🔄 Resetar Senha
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>