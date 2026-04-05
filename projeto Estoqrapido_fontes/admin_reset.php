<?php
require 'config.php';
verificarLogado();
if ($_SESSION['nivel'] != 'admin') die("Acesso negado.");

if (isset($_POST['resetar'])) {
    $id_usuario = $_POST['usuario_id'];
    $senha_padrao = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->execute([$senha_padrao, $id_usuario]);
    echo "Senha resetada para '123456'";
}

$usuarios = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll();
?>
<div class="container mt-4">
    <h3>Resetar Senha de Usuário (Admin)</h3>
    <form method="POST">
        <select name="usuario_id" class="form-control mb-2">
            <?php foreach($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['nome'] ?></option>
            <?php endforeach; ?>
        </select>
        <button name="resetar" class="btn btn-danger">Resetar para 123456</button>
    </form>
</div>