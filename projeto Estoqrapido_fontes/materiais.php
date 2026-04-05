<?php
require 'config.php';
verificarLogado();

// Lógica de Cadastro (Processamento)
if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor_unitario']; // Novo campo
    $stmt = $pdo->prepare("INSERT INTO materiais (nome, descricao, valor_unitario) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $descricao, $valor]);
    
    // Redireciona para evitar que ao dar F5 cadastre de novo
    header("Location: materiais.php");
    exit;
}

// Busca os dados para a tabela
$materiais = $pdo->query("SELECT * FROM materiais ORDER BY nome ASC")->fetchAll();

// --- INICIO DO HTML ---
include 'header.php'; // Aqui entra o Menu, o CSS e o Botão Voltar
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2 class="fw-bold">Cadastro de Materiais</h2>
        <p class="text-muted">Gerencie os itens disponíveis no estoque.</p>
    </div>

    <!-- Formulário de Cadastro -->
    <div class="col-md-12 mb-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Novo Item</h5>
            <form method="POST" class="row g-3">
<div class="col-md-3">
    <label class="form-label small fw-bold text-muted">NOME DO MATERIAL</label>
    <input type="text" name="nome" class="form-control" required>
</div>
<div class="col-md-4">
    <label class="form-label small fw-bold text-muted">DESCRIÇÃO</label>
    <input type="text" name="descricao" class="form-control">
</div>
<div class="col-md-3">
    <label class="form-label small fw-bold text-muted">VALOR UNITÁRIO (R$)</label>
    <input type="number" name="valor_unitario" class="form-control" step="0.01" placeholder="0,00" required>
</div>
<div class="col-md-2 d-flex align-items-end">
    <button name="cadastrar" class="btn btn-primary w-100 fw-bold">Cadastrar</button>
</div>
            </form>
        </div>
    </div>

    <!-- Listagem de Materiais -->
    <div class="col-md-12">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Itens em Estoque</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th>Nome do Material</th>
                            <th>Descrição</th>
                            <th width="150" class="text-center">Estoque Atual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($materiais) > 0): ?>
                            <?php foreach($materiais as $m): ?>
                            <tr>
                                <td class="text-muted">#<?= $m['id'] ?></td>
                                <td class="fw-semibold"><?= $m['nome'] ?></td>
                                <td class="text-muted small"><?= $m['descricao'] ?></td>
                                <td class="text-center">
                                 <span class="badge <?= $m['estoque_atual'] < 10 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?> px-3 py-2">
                                    <?= number_format($m['estoque_atual'], 2, ',', '.') ?>
                                 </span>
                            </td>
                            
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Nenhum material cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; // Fecha o layout e traz o JavaScript
?>