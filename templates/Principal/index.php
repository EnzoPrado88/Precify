<?php

/**
 * @var \App\View\AppView $this
 * @var array|null $resultado
 * @var array $errors
 * @var string $title
 */

function getLabelWithTooltip($fieldName, $labelText, $tooltipText)
{
    $tooltipHtml = sprintf(
        ' <span class="tooltip-wrapper" tabindex="0"><i class="fas fa-info-circle tooltip-icon-modern"></i><span class="tooltip-text-modern">%s</span></span>',
        h($tooltipText)
    );
    return $labelText . $tooltipHtml;
}

$this->assign('title', $title ?? 'Precificação Inteligente');

// --- PREPARAÇÃO DAS EXPLICAÇÕES PARA O MODAL ---
if (isset($resultado)) {
    $expTotalOuroPeca = sprintf(
        "O <strong>total de ouro utilizado por peça</strong> é a soma do ouro necessário para o banho e do ouro equivalente ao custo da mão de obra.<br><br>" .
        "<strong>1. Ouro para Banho:</strong><br>" .
        "Espessura do banho (milésimos) ÷ 1000 × Peso da peça (g)<br>" .
        "Exemplo: (%.2f ÷ 1000) × %.2f = <strong>%.4f g</strong><br><br>" .
        "<strong>2. Ouro para Mão de Obra:</strong><br>" .
        "Mão de obra (milésimos) ÷ 1000 × Peso da peça (g)<br>" .
        "Exemplo: (%.2f ÷ 1000) × %.2f = <strong>%.4f g</strong><br><br>" .
        "<strong>3. Total de Ouro:</strong><br>" .
        "Ouro para banho + Ouro para mão de obra<br>" .
        "Exemplo: %.4f g + %.4f g = <strong>%.4f g</strong>",
        $resultado['inputs']['espessura_banho'],
        $resultado['inputs']['peso_peca'],
        $resultado['ouroParaBanhoPorPeca'],
        $resultado['inputs']['mao_de_obra_milesimos'],
        $resultado['inputs']['peso_peca'],
        $resultado['ouroParaMaoDeObraPorPeca'],
        $resultado['ouroParaBanhoPorPeca'],
        $resultado['ouroParaMaoDeObraPorPeca'],
        $resultado['totalOuroPorPeca']
    );

    $expCustoPorPeca = sprintf(
        "O <strong>custo de produção por peça</strong> é calculado multiplicando o total de ouro utilizado pelo valor do grama do ouro.<br><br>" .
        "<strong>Fórmula:</strong> Total de ouro por peça × Cotação do ouro<br>" .
        "Exemplo: %.4f g × R$ %.2f = <strong>R$ %.2f</strong><br><br>" .
        "Esse valor representa o custo bruto para produzir uma peça, considerando apenas o ouro (banho + mão de obra).",
        $resultado['totalOuroPorPeca'],
        $resultado['inputs']['cotacao_ouro'],
        $resultado['custoPorPeca']
    );

    $expLucroPorPeca = sprintf(
        "O <strong>lucro por peça</strong> é a diferença entre o preço de venda e o custo de produção.<br><br>" .
        "Primeiro, calcula-se o valor do lucro desejado aplicando a margem de lucro sobre o custo:<br>" .
        "<strong>Fórmula:</strong> Custo por peça × (Margem de lucro ÷ 100)<br>" .
        "Exemplo: R$ %.2f × (%.2f ÷ 100) = <strong>R$ %.2f</strong><br><br>" .
        "Esse valor é somado ao custo para formar o preço final de venda.",
        $resultado['custoPorPeca'],
        $resultado['inputs']['margem_lucro'],
        $resultado['lucroPorPeca']
    );

    $expPrecoVendaPorPeca = sprintf(
        "O <strong>preço final de venda por peça</strong> é a soma do custo de produção com o lucro desejado.<br><br>" .
        "<strong>Fórmula:</strong> Custo por peça + Lucro por peça<br>" .
        "Exemplo: R$ %.2f + R$ %.2f = <strong>R$ %.2f</strong><br><br>" .
        "Esse é o valor sugerido para comercialização de cada peça, considerando todos os custos e a margem de lucro definida.",
        $resultado['custoPorPeca'],
        $resultado['lucroPorPeca'],
        $resultado['precoVendaPorPeca']
    );

    $expCustoMaoDeObra = sprintf(
        "O <strong>custo da mão de obra</strong> em reais é calculado convertendo o ouro equivalente à mão de obra pelo valor do grama do ouro.<br><br>" .
        "<strong>Fórmula:</strong> Ouro para mão de obra × Cotação do ouro<br>" .
        "Exemplo: %.4f g × R$ %.2f = <strong>R$ %.2f</strong><br><br>" .
        "Esse valor representa quanto do custo total corresponde apenas à mão de obra envolvida no processo.",
        $resultado['ouroParaMaoDeObraPorPeca'],
        $resultado['inputs']['cotacao_ouro'],
        $resultado['custoMaoDeObraPorPeca']
    );

    $expQtdPecas = "A <strong>quantidade de peças</strong> é o número total de itens idênticos informados para o cálculo. Todos os valores totais do lote são multiplicados por essa quantidade, permitindo visualizar o custo e o preço final para toda a produção.";
}
?>
<?php
$this->append('script');
?>
<style>

</style>
<?php $this->end(); ?>

<!DOCTYPE html>
<html lang="pt-BR">

<body class="page-background">
    <main class="page-container">
        <div class="content-wrapper">
            <section class="form-section card-ui">
                <div class="card-header-custom">
                    <h2><i class="fas fa-cogs form-icon"></i> Configurar Cálculo de Banho de Ouro</h2>
                </div>
                <div class="card-body-custom">
                    <?= $this->Form->create(null, ['url' => ['controller' => 'Principal', 'action' => 'index'], 'class' => 'modern-form', 'novalidate' => true]) ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <?= $this->Form->label(
                                'cotacao_ouro',
                                getLabelWithTooltip(
                                    'cotacao_ouro',
                                    'Cotação do Ouro (g)',
                                    'Informe o valor atual do grama do ouro puro no mercado financeiro. Esse valor é fundamental para calcular o custo do banho, pois o preço do ouro pode variar diariamente. Exemplo: 590,00.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('cotacao_ouro', ['label' => false, 'type' => 'number', 'step' => '0.01', 'min' => '0', 'class' => 'form-control-modern' . (isset($errors['cotacao_ouro']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 590.00']) ?>
                            <?php if (isset($errors['cotacao_ouro'])): ?>
                                <div class="error-message"><?= h(current($errors['cotacao_ouro'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->label(
                                'peso_peca',
                                getLabelWithTooltip(
                                    'peso_peca',
                                    'Peso da Peça (g)',
                                    'Digite o peso individual da peça em gramas antes do banho de ouro. O peso influencia diretamente na quantidade de ouro utilizada no processo. Exemplo: 12,00.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('peso_peca', ['label' => false, 'type' => 'number', 'step' => '0.01', 'min' => '0', 'class' => 'form-control-modern' . (isset($errors['peso_peca']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 12.00']) ?>
                            <?php if (isset($errors['peso_peca'])): ?>
                                <div class="error-message"><?= h(current($errors['peso_peca'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->label(
                                'espessura_banho',
                                getLabelWithTooltip(
                                    'espessura_banho',
                                    'Espessura (milésimos)',
                                    'Defina a espessura desejada do banho de ouro em milésimos de milímetro por grama. Por exemplo, 10 significa que será aplicado um banho de 10 milésimos. Quanto maior o valor, mais ouro será utilizado.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('espessura_banho', ['label' => false, 'type' => 'number', 'step' => '0.1', 'min' => '0', 'class' => 'form-control-modern' . (isset($errors['espessura_banho']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 10']) ?>
                            <?php if (isset($errors['espessura_banho'])): ?>
                                <div class="error-message"><?= h(current($errors['espessura_banho'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->label(
                                'mao_de_obra_milesimos',
                                getLabelWithTooltip(
                                    'mao_de_obra_milesimos',
                                    'Mão de Obra (milésimos)',
                                    'Informe o valor equivalente em milésimos de ouro que representa o custo da mão de obra para cada grama da peça. Esse valor é convertido em ouro para compor o custo total. Exemplo: 3,5.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('mao_de_obra_milesimos', ['label' => false, 'type' => 'number', 'step' => '0.1', 'min' => '0', 'class' => 'form-control-modern' . (isset($errors['mao_de_obra_milesimos']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 3.5']) ?>
                            <?php if (isset($errors['mao_de_obra_milesimos'])): ?>
                                <div class="error-message"><?= h(current($errors['mao_de_obra_milesimos'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->label(
                                'quantidade_pecas',
                                getLabelWithTooltip(
                                    'quantidade_pecas',
                                    'Qtde. de Peças',
                                    'Digite o número total de peças idênticas para calcular o custo do lote. O sistema irá multiplicar os custos individuais pela quantidade informada.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('quantidade_pecas', ['label' => false, 'type' => 'number', 'step' => '1', 'min' => '1', 'class' => 'form-control-modern' . (isset($errors['quantidade_pecas']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 1']) ?>
                            <?php if (isset($errors['quantidade_pecas'])): ?>
                                <div class="error-message"><?= h(current($errors['quantidade_pecas'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->label(
                                'margem_lucro',
                                getLabelWithTooltip(
                                    'margem_lucro',
                                    'Margem de Lucro (%)',
                                    'Informe o percentual de lucro desejado sobre o custo total. Por exemplo, 100 significa que o preço de venda será o dobro do custo. O valor pode ser ajustado conforme sua estratégia comercial.'
                                ),
                                ['escape' => false, 'class' => 'form-label-modern']
                            ) ?>

                            <?= $this->Form->control('margem_lucro', ['label' => false, 'type' => 'number', 'step' => '0.1', 'min' => '0', 'class' => 'form-control-modern' . (isset($errors['margem_lucro']) ? ' is-invalid' : ''), 'placeholder' => 'Ex: 150']) ?>
                            <?php if (isset($errors['margem_lucro'])): ?>
                                <div class="error-message"><?= h(current($errors['margem_lucro'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <?= $this->Form->button('<i class="fas fa-check-circle"></i> Calcular Preço', ['type' => 'submit', 'class' => 'btn-modern btn-primary-modern', 'escapeTitle' => false]) ?>
                        <?= $this->Html->link('<i class="fas fa-undo"></i> Resetar', ['controller' => 'Principal', 'action' => 'index'], ['class' => 'btn-modern btn-secondary-modern', 'role' => 'button', 'escape' => false]) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </section>

            <?php if (isset($resultado)): ?>
                <section id="resultado-tabela" class="results-section card-ui">
                    <div class="card-header-custom">
                        <h2><i class="fas fa-poll results-icon"></i> Resumo do Cálculo (Lote de <?= h($resultado['inputs']['quantidade_pecas']) ?> Peças)</h2>
                    </div>
                    <div class="card-body-custom">
                        <div class="table-responsive-modern">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th class="text-right-modern">Por Peça</th>
                                        <th class="text-right-modern">Total do Lote</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="highlight-row trigger-row" data-title="Cálculo do Preço de Venda" data-explanation="<?= h($expPrecoVendaPorPeca) ?>">
                                        <td><strong><i class="fas fa-tag table-icon"></i> Preço Final de Venda</strong></td>
                                        <td class="text-right-modern value-highlight">R$ <?= h(number_format($resultado['precoVendaPorPeca'], 2, ',', '.')) ?></td>
                                        <td class="text-right-modern value-highlight">R$ <?= h(number_format($resultado['precoVendaTotalLote'], 2, ',', '.')) ?></td>
                                    </tr>
                                    <tr class="highlight-row trigger-row" data-title="Cálculo do Lucro" data-explanation="<?= h($expLucroPorPeca) ?>">
                                        <td><strong><i class="fas fa-chart-line table-icon"></i> Lucro Estimado</strong></td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['lucroPorPeca'], 2, ',', '.')) ?></td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['lucroTotalLote'], 2, ',', '.')) ?></td>
                                    </tr>
                                    <tr class="highlight-row trigger-row" data-title="Cálculo do Custo de Produção" data-explanation="<?= h($expCustoPorPeca) ?>">
                                        <td><strong><i class="fas fa-wallet table-icon"></i> Custo de Produção</strong></td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['custoPorPeca'], 2, ',', '.')) ?></td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['custoTotalLote'], 2, ',', '.')) ?></td>
                                    </tr>
                                    <tr class="table-separator">
                                        <td colspan="3">Detalhamento do Custo</td>
                                    </tr>
                                    <tr class="trigger-row" data-title="Cálculo do Total de Ouro" data-explanation="<?= h($expTotalOuroPeca) ?>">
                                        <td><i class="fas fa-balance-scale table-icon"></i> Total de Ouro Utilizado</td>
                                        <td class="text-right-modern"><strong><?= h(number_format($resultado['totalOuroPorPeca'], 4, ',', '.')) ?> g</strong></td>
                                        <td class="text-right-modern"><strong><?= h(number_format($resultado['totalOuroLote'], 4, ',', '.')) ?> g</strong></td>
                                    </tr>
                                    <tr class="trigger-row" data-title="Cálculo do Custo da Mão de Obra" data-explanation="<?= h($expCustoMaoDeObra) ?>">
                                        <td><i class="fas fa-hand-holding-usd table-icon"></i> Custo da Mão de Obra (R$)</td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['custoMaoDeObraPorPeca'], 2, ',', '.')) ?></td>
                                        <td class="text-right-modern">R$ <?= h(number_format($resultado['custoMaoDeObraTotalLote'], 2, ',', '.')) ?></td>
                                    </tr>
                                    <tr class="table-separator">
                                        <td colspan="3">Parâmetros Utilizados</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-dollar-sign table-icon"></i> Cotação do Ouro</td>
                                        <td class="text-right-modern" colspan="2">R$ <?= h(number_format($resultado['inputs']['cotacao_ouro'], 2, ',', '.')) ?> /g</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-percentage table-icon"></i> Margem de Lucro Aplicada</td>
                                        <td class="text-right-modern" colspan="2"><?= h(number_format($resultado['inputs']['margem_lucro'], 2, ',', '.')) ?>%</td>
                                    </tr>
                                    <tr class="trigger-row" data-title="Quantidade de Peças" data-explanation="<?= h($expQtdPecas) ?>">
                                        <td><i class="fas fa-cubes table-icon"></i> Quantidade de Peças</td>
                                        <td class="text-right-modern" colspan="2"><?= h($resultado['inputs']['quantidade_pecas']) ?> un</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <div id="explanation-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3 id="modal-title">Título do Cálculo</h3>
            <div id="modal-body">
                <p>A explicação do cálculo aparecerá aqui...</p>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> Precify. Precificação Inteligente.</p>
    </footer>

    <?= $this->fetch('script') ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('explanation-modal');
            if (modal) {
                const modalTitle = document.getElementById('modal-title');
                const modalBody = document.getElementById('modal-body');
                const closeModalBtn = modal.querySelector('.modal-close');
                const resultsSection = document.getElementById('resultado-tabela');

                function openModal(title, explanation) {
                    if (modalTitle && modalBody) {
                        modalTitle.textContent = title;
                        modalBody.innerHTML = explanation;
                        modal.classList.add('active');
                    }
                }

                function closeModal() {
                    modal.classList.remove('active');
                }

                if (resultsSection) {
                    resultsSection.addEventListener('click', function(event) {
                        const trigger = event.target.closest('.trigger-row');
                        if (trigger) {
                            const title = trigger.getAttribute('data-title');
                            const explanation = trigger.getAttribute('data-explanation');
                            if (explanation) {
                                openModal(title, explanation);
                            }
                        }
                    });
                }

                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', closeModal);
                }

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key === "Escape" && modal.classList.contains('active')) {
                        closeModal();
                    }
                });

                if (resultsSection) {
                    resultsSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    </script>
</body>

</html>
