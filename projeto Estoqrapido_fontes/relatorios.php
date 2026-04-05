<?php
require 'config.php';
verificarLogado();

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$dados = [];
$titulo_relatorio = "Selecione um relatório acima";

// Lógica de busca de dados
switch ($tipo) {
    case 'estoque_geral':
        $titulo_relatorio = "Relatório de Valor de Estoque (Geral)";
        $dados = $pdo->query("SELECT nome, valor_unitario, estoque_atual as total FROM materiais ORDER BY nome ASC")->fetchAll();
        break;

    case 'entradas':
        $titulo_relatorio = "Relatório Detalhado de Entradas";
        $dados = $pdo->query("SELECT m.nome, mov.quantidade, mov.data_movimentacao 
                             FROM movimentacoes mov 
                             JOIN materiais m ON mov.material_id = m.id 
                             WHERE mov.tipo = 'entrada' 
                             ORDER BY mov.data_movimentacao DESC")->fetchAll();
        break;

    case 'saidas':
        $titulo_relatorio = "Relatório Detalhado de Saídas";
        $dados = $pdo->query("SELECT m.nome, mov.quantidade, mov.data_movimentacao 
                             FROM movimentacoes mov 
                             JOIN materiais m ON mov.material_id = m.id 
                             WHERE mov.tipo = 'saida' 
                             ORDER BY mov.data_movimentacao DESC")->fetchAll();
        break;

    case 'mais_vendidos':
        $titulo_relatorio = "Materiais Mais Retirados (Top 10)";
        $dados = $pdo->query("SELECT m.nome, SUM(mov.quantidade) as total 
                             FROM movimentacoes mov 
                             JOIN materiais m ON mov.material_id = m.id 
                             WHERE mov.tipo = 'saida' 
                             GROUP BY mov.material_id 
                             ORDER BY total DESC LIMIT 10")->fetchAll();
        break;

    case 'estoque_baixo':
        $titulo_relatorio = "Alerta: Estoque Baixo (Menos de 10 unidades)";
        $dados = $pdo->query("SELECT nome, estoque_atual as total FROM materiais 
                             WHERE estoque_atual < 10 
                             ORDER BY estoque_atual ASC")->fetchAll();
        break;
}

include 'header.php';
?>

<div class="row">
    <div class="col-md-12 mb-4 text-center">
        <h2 class="fw-bold">Relatórios do Sistema</h2>
        <p class="text-muted small">Consulte a situação financeira e física do seu depósito.</p>
    </div>

    <!-- Menu de Seleção de Relatórios -->
    <div class="col-md-12 mb-5 no-print">
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="relatorios.php?tipo=estoque_geral" class="btn <?= $tipo == 'estoque_geral' ? 'btn-primary' : 'btn-outline-primary' ?> px-4 fw-bold">
                📦 Estoque Atual
            </a>
            <a href="relatorios.php?tipo=entradas" class="btn <?= $tipo == 'entradas' ? 'btn-primary' : 'btn-outline-primary' ?> px-4 fw-bold">
                📥 Entradas
            </a>
            <a href="relatorios.php?tipo=saidas" class="btn <?= $tipo == 'saidas' ? 'btn-primary' : 'btn-outline-primary' ?> px-4 fw-bold">
                📤 Saídas
            </a>
            <a href="relatorios.php?tipo=mais_vendidos" class="btn <?= $tipo == 'mais_vendidos' ? 'btn-primary' : 'btn-outline-primary' ?> px-4 fw-bold">
                🏆 Mais Retirados
            </a>
            <a href="relatorios.php?tipo=estoque_baixo" class="btn <?= $tipo == 'estoque_baixo' ? 'btn-danger' : 'btn-outline-danger' ?> px-4 fw-bold">
                ⚠️ Estoque Baixo
            </a>
        </div>
    </div>

    <!-- Exibição dos Dados -->
    <div class="col-md-12">
        <div class="card p-4 shadow-sm border-0">
            <h4 class="fw-bold mb-4 text-center text-secondary"><?= $titulo_relatorio ?></h4>
            
            <?php if ($tipo == ''): ?>
                <div class="text-center py-5">
                    <p class="text-muted mt-3">Clique em um dos botões acima para gerar o relatório.</p>
                </div>
            <?php elseif (empty($dados)): ?>
                <div class="alert alert-light text-center">Nenhum registro encontrado para este critério.</div>
            <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <?php if($tipo == 'estoque_geral'): ?>
                                    <th class="text-center">Vlr. Unitário</th>
                                <?php endif; ?>
                                <th class="text-center">Quantidade</th>
                                <?php if($tipo == 'estoque_geral'): ?>
                                    <th class="text-end">Vlr. Total</th>
                                <?php endif; ?>
                                <?php if($tipo == 'entradas' || $tipo == 'saidas'): ?>
                                    <th class="text-end">Data/Hora</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $somaGeral = 0;
                            foreach ($dados as $d): 
                                $qtd = isset($d['total']) ? $d['total'] : (isset($d['quantidade']) ? $d['quantidade'] : 0);
                                
                                // Cálculo financeiro apenas se for estoque geral
                                $vlrTotalItem = 0;
                                if ($tipo == 'estoque_geral') {
                                    $vlrTotalItem = $qtd * $d['valor_unitario'];
                                    $somaGeral += $vlrTotalItem;
                                }
                            ?>
                                <tr>
                                    <td class="fw-semibold"><?= $d['nome'] ?></td>
                                    
                                    <?php if($tipo == 'estoque_geral'): ?>
                                        <td class="text-center">R$ <?= number_format($d['valor_unitario'], 2, ',', '.') ?></td>
                                    <?php endif; ?>

                                    <td class="text-center">
                                        <span class="badge rounded-pill <?= ($tipo == 'estoque_baixo' || ($tipo == 'estoque_geral' && $qtd < 10)) ? 'bg-danger' : 'bg-secondary' ?> px-3">
                                            <?= number_format($qtd, 2, ',', '.') ?> un
                                        </span>
                                    </td>

                                    <?php if($tipo == 'estoque_geral'): ?>
                                        <td class="text-end fw-bold text-primary">R$ <?= number_format($vlrTotalItem, 2, ',', '.') ?></td>
                                    <?php endif; ?>

                                    <?php if($tipo == 'entradas' || $tipo == 'saidas'): ?>
                                        <td class="text-end text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($d['data_movimentacao'])) ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        
                        <?php if($tipo == 'estoque_geral'): ?>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-uppercase small">Valor Total do Patrimônio em Estoque:</td>
                                <td class="text-end fw-bold text-success" style="font-size: 1.1rem;">
                                    R$ <?= number_format($somaGeral, 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                
                <div class="mt-4 text-end no-print">
                    <button onclick="window.print()" class="btn btn-sm btn-light border shadow-sm">
                        🖨️ Imprimir Relatório (PDF)
                    </button>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .navbar, .btn, .mb-4, .alert { display: none !important; }
    body { background-color: white !important; padding: 0; }
    .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
    .card { box-shadow: none !important; border: none !important; }
    .table { font-size: 12px; }
}
</style>

<?php include 'footer.php'; ?>