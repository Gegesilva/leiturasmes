<?php $pageTitle = 'Leituras de Contadores por Mês'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="app-shell">
    <header class="hero">
        <div>
            <p class="eyebrow">Relatório operacional</p>
            <h1>Leituras de Contadores por Mês</h1>
            <p class="subtitle">Acompanhe a produção mensal por contrato, equipamento e cliente.</p>
        </div>
        <div class="hero-card"><span>Equipamentos encontrados</span><strong><?= count($rows) ?></strong></div>
    </header>

    <section class="panel">
        <form method="get" class="filters">
            <label><span>Modelo do equipamento</span><input type="text" name="modelo" value="<?= htmlspecialchars($filters->modelo) ?>" placeholder="Ex.: M 2040"></label>
            <label><span>Contrato</span><input type="text" name="contrato" value="<?= htmlspecialchars($filters->contrato) ?>" placeholder="Ex.: 12345"></label>
            <label><span>Início</span><input type="month" name="inicio" value="<?= htmlspecialchars($filters->startMonthValue()) ?>"></label>
            <label><span>Fim</span><input type="month" name="fim" value="<?= htmlspecialchars($filters->endMonthValue()) ?>"></label>
            <button type="submit">Aplicar filtros</button>
        </form>
    </section>

    <section class="panel table-panel">
        <div class="table-heading"><div><p class="eyebrow">Resultado</p><h2>Produção mensal</h2></div><span class="period-badge"><?= htmlspecialchars($filters->periodLabel()) ?></span></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Contrato</th><th>Modelo</th><th>CodCli</th><th>Cliente</th><th>Serial</th><?php foreach ($months as $month): ?><th><?= htmlspecialchars($month->label) ?></th><?php endforeach; ?><th>Med. atual</th></tr></thead>
                <tbody>
                <?php if (!$rows): ?><tr><td class="empty" colspan="<?= 6 + count($months) ?>">Nenhum equipamento encontrado para os filtros informados.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?><tr><td><?= htmlspecialchars($row['contrato']) ?></td><td><?= htmlspecialchars($row['modelo']) ?></td><td><?= htmlspecialchars($row['codcli']) ?></td><td><?= htmlspecialchars($row['cliente']) ?></td><td><?= htmlspecialchars($row['serial']) ?></td><?php foreach ($months as $month): ?><td class="number"><?= number_format($row['months'][$month->key], 0, ',', '.') ?></td><?php endforeach; ?><td class="number emphasis"><?= number_format($row['medatual'], 0, ',', '.') ?></td></tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="table-note">Os valores mensais representam a soma de TB02117_TOTPB, TB02117_TOTCOLOR e TB02117_TOTGF por data de cadastro.</p>
    </section>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
