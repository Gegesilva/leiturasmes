<?php

require __DIR__ . '/src/bootstrap.php';

$filters = FilterForm::fromRequest($_GET);
$months = buildMonths($filters->startDate, $filters->endDate);
$rows = fetchReportRows($filters, $months);

?><!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leituras de Meses</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="app-shell">
    <header class="hero">
        <div>
            <p class="eyebrow">Base do projeto</p>
            <h1>Leituras de Contadores por Mês</h1>
            <p class="subtitle">
                Estrutura inicial com filtros de modelo, período e contrato, pronta para receber a consulta real.
            </p>
        </div>
        <div class="hero-card">
            <span>Campos previstos</span>
            <strong>Contrato, modelo, cliente, serial, totais mensais e medição atual</strong>
        </div>
    </header>

    <section class="panel">
        <form method="get" class="filters">
            <label>
                <span>Modelo</span>
                <input type="text" name="modelo" value="<?= htmlspecialchars($filters->modelo) ?>" placeholder="Ex.: M 2040 / M 2540">
            </label>
            <label>
                <span>Contrato</span>
                <input type="text" name="contrato" value="<?= htmlspecialchars($filters->contrato) ?>" placeholder="Ex.: 12345">
            </label>
            <label>
                <span>Início</span>
                <input type="month" name="inicio" value="<?= htmlspecialchars($filters->startMonthValue()) ?>">
            </label>
            <label>
                <span>Fim</span>
                <input type="month" name="fim" value="<?= htmlspecialchars($filters->endMonthValue()) ?>">
            </label>
            <button type="submit">Aplicar filtros</button>
        </form>
    </section>

    <section class="panel table-panel">
        <div class="table-scroll">
            <table>
                <thead>
                <tr>
                    <th>Contrato</th>
                    <th>Modelo</th>
                    <th>CodCli</th>
                    <th>Cliente</th>
                    <th>Serial</th>
                    <?php foreach ($months as $month): ?>
                        <th><?= htmlspecialchars($month->label) ?></th>
                    <?php endforeach; ?>
                    <th>Med atual</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['contrato']) ?></td>
                        <td><?= htmlspecialchars($row['modelo']) ?></td>
                        <td><?= htmlspecialchars($row['codcli']) ?></td>
                        <td><?= htmlspecialchars($row['cliente']) ?></td>
                        <td><?= htmlspecialchars($row['serial']) ?></td>
                        <?php foreach ($months as $month): ?>
                            <td><?= number_format(isset($row['months'][$month->key]) ? $row['months'][$month->key] : 0, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                        <td><?= number_format($row['medatual'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>
