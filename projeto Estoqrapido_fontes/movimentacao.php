<?php
require 'config.php';
verificarLogado();

// Lógica de Processamento (Entrada/Saída)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $material_id = $_POST['material_id'];
    $tipo = $_POST['tipo'];
    $qtd = $_POST['quantidade'];
    $user_id = $_SESSION['usuario_id'];

    // Inserir movimentação
    $stmt = $pdo->prepare("INSERT INTO movimentacoes (material_id, tipo, quantidade, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$material_id, $tipo, $qtd, $user_id]);

    // Atualizar estoque no cadastro do material
    if ($tipo == 'entrada') {
        $stmt = $pdo->prepare("UPDATE materiais SET estoque_atual = estoque_atual + ? WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE materiais SET estoque_atual = estoque_atual - ? WHERE id = ?");
    }
    $stmt->execute([$qtd, $material_id]);
    
    // Redireciona com parâmetro de sucesso
    header("Location: movimentacao.php?sucesso=1");
    exit;
}

// Busca materiais para o select
$materiais = $pdo->query("SELECT * FROM materiais ORDER BY nome ASC")->fetchAll();

// Busca as últimas 5 movimentações para exibir abaixo do formulário
$ultimas_mov = $pdo->query("SELECT m.nome, mov.tipo, mov.quantidade, mov.data_movimentacao 
                            FROM movimentacoes mov 
                            JOIN materiais m ON mov.material_id = m.id 
                            ORDER BY mov.id DESC LIMIT 5")->fetchAll();

include 'header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-4">
            <h2 class="fw-bold">Movimentação de Estoque</h2>
            <p class="text-muted">Registre entradas e saídas de materiais do depósito.</p>
        </div>

        <?php if(isset($_GET['sucesso'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                 Movimentação registrada com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card p-4 mb-4 shadow-sm">
            <h5 class="fw-bold mb-4">Lançar Novo Registro</h5>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted">SELECIONE O MATERIAL</label>
                        <select name="material_id" class="form-select form-select-lg" required>
                            <option value="">Escolha um item...</option>
                            <?php foreach($materiais as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= $m['nome'] ?> (Saldo Atual: <?= $m['estoque_atual'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">TIPO DE OPERAÇÃO</label>
                        <select name="tipo" class="form-select">
                            <option value="entrada" class="text-success">⬆️ ENTRADA (Adicionar ao estoque)</option>
                            <option value="saida" class="text-danger">⬇️ SAÍDA (Retirar do estoque)</option>
                        </select>
                    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold text-muted">QUANTIDADE</label>   
        <input type="number" name="quantidade" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
    </div>

                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            Confirmar Movimentação
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Ultimos lançamentos -->
        <div class="card p-4 border-0 bg-white shadow-sm">
            <h6 class="fw-bold text-muted mb-3 small">ÚLTIMOS LANÇAMENTOS</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Material</th>
                            <th>Tipo</th>
                            <th>Qtd</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ultimas_mov as $um): ?>
                        <tr>
                            <td class="small fw-semibold"><?= $um['nome'] ?></td>
                            <td>
                                <span class="badge <?= $um['tipo'] == 'entrada' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> small">
                                    <?= strtoupper($um['tipo']) ?>
                                </span>
                            </td>
                            <td class="small"><?= $um['quantidade'] ?></td>
                            <td class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($um['data_movimentacao'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>