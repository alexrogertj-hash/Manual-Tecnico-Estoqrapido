<?php require 'config.php'; verificarLogado(); include 'header.php'; ?>

<div class="row g-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-4">Painel de Controle</h2>
    </div>

    <!-- Resumo -->
    <div class="col-md-4">
        <div class="card p-4 border-start border-primary border-4">
            <span class="text-muted small fw-bold">VALOR TOTAL EM ESTOQUE</span>
        <?php 
// Cálculo de Valor Total: (Quantidade * Valor Unitário Decimal)
$totalFinanceiro = $pdo->query("SELECT SUM(estoque_atual * valor_unitario) FROM materiais")->fetchColumn();
echo "R$ " . number_format($totalFinanceiro, 2, ',', '.');
        ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 border-start border-danger border-4">
            <span class="text-muted small fw-bold text-danger">ESTOQUE BAIXO</span>
            <?php 
                $baixo = $pdo->query("SELECT COUNT(*) FROM materiais WHERE estoque_atual < 10")->fetchColumn();
                echo "<h1 class='fw-bold text-danger'>$baixo</h1>";
            ?>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="col-md-4">
        <div class="card p-4 bg-primary text-white">
            <span class="small fw-bold">AÇÃO RÁPIDA</span>
            <div class="mt-2">
                <a href="movimentacao.php" class="btn btn-light btn-sm w-100">Lançar Movimentação</a>
            </div>
        </div>
    </div>

    <!-- Tabela de Últimas Movimentações -->
    <div class="col-md-12 mt-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Atividades Recentes</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Material</th>
                            <th>Tipo</th>
                            <th>Qtd</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentes = $pdo->query("SELECT m.nome, mov.tipo, mov.quantidade, mov.data_movimentacao 
                                                 FROM movimentacoes mov 
                                                 JOIN materiais m ON mov.material_id = m.id 
                                                 ORDER BY mov.id DESC LIMIT 5")->fetchAll();
                        foreach($recentes as $r):
                        ?>
                        <tr>
                            <td><?= $r['nome'] ?></td>
                            <td>
                                <span class="badge <?= $r['tipo'] == 'entrada' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                    <?= strtoupper($r['tipo']) ?>
                                </span>
                            </td>
                            <td><?= $r['quantidade'] ?></td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($r['data_movimentacao'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>