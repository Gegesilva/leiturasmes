<?php

header('Content-type: text/html; charset=UTF-8');

date_default_timezone_set('America/Sao_Paulo');
$database = require __DIR__ . '/config/database.php';

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function monthValue($value, $fallback)
{
    return preg_match('/^\d{4}-\d{2}$/', $value) ? $value : $fallback;
}

function nextMonth($month)
{
    $date = new DateTime($month . '-01');
    $date->modify('+1 month');
    return $date->format('Y-m');
}

$today = new DateTime('first day of this month');
$defaultEnd = $today->format('Y-m');
$defaultStartDate = clone $today;
$defaultStartDate->modify('-2 months');
$defaultStart = $defaultStartDate->format('Y-m');
$start = monthValue(isset($_POST['inicio']) ? $_POST['inicio'] : '', $defaultStart);
$end = monthValue(isset($_POST['fim']) ? $_POST['fim'] : '', $defaultEnd);

if (strtotime($end . '-01') < strtotime($start . '-01')) {
    $swap = $start;
    $start = $end;
    $end = $swap;
}

$modelo = isset($_POST['modelo']) ? trim((string) $_POST['modelo']) : '';
$contrato = isset($_POST['contrato']) ? trim((string) $_POST['contrato']) : '';
$months = [];
$monthNames = [1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'ABR', 5 => 'MAI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO', 9 => 'SET', 10 => 'OUT', 11 => 'NOV', 12 => 'DEZ'];
$cursor = new DateTime($start . '-01');
$lastMonth = new DateTime($end . '-01');

while ($cursor <= $lastMonth) {
    $months[] = ['key' => $cursor->format('Y-m'), 'label' => $monthNames[(int) $cursor->format('n')] . '/' . $cursor->format('Y')];
    $cursor->modify('+1 month');
}

$rows = [];
$error = '';

if ($database === false) {
    $error = 'Não foi possível conectar ao banco de dados.';
} else {
    try {
        $where = ['TB02117_DTCAD >= ?', 'TB02117_DTCAD < ?'];
        $filterParams = [$start . '-01', nextMonth($end) . '-01'];

        if ($modelo !== '') {
            $where[] = 'UPPER(TB01010_REFERENCIA) LIKE UPPER(?)';
            $filterParams[] = '%' . $modelo . '%';
        }
        if ($contrato !== '') {
            $where[] = 'CAST(TB02112_CODIGO AS VARCHAR(50)) LIKE ?';
            $filterParams[] = '%' . $contrato . '%';
        }

        $monthly = [];
        $params = [];
        foreach ($months as $index => $month) {
            $from = '?';
            $to = '?';
            $monthly[] = "SUM(CASE WHEN TB02117_DTCAD >= $from AND TB02117_DTCAD < $to THEN COALESCE(TB02117_TOTPB, 0) + COALESCE(TB02117_TOTCOLOR, 0) + COALESCE(TB02117_TOTGF, 0) ELSE 0 END) AS MES_$index";
            $params[] = $month['key'] . '-01';
            $params[] = nextMonth($month['key']) . '-01';
        }

        $params = array_merge($params, $filterParams);

        $sql = 'SELECT TB02112_CODIGO AS CONTRATO, TB01010_REFERENCIA AS MODELO,
                       TB02111_CODCLI AS CODCLI, TB01008_NOME AS CLIENTE,
                       TB02112_NUMSERIE AS SERIAL, ' . implode(', ', $monthly) . ',
                       MAX(TB02117_MEDATUAL) AS MEDATUAL
                  FROM TB02112
             LEFT JOIN TB02111 ON TB02111_CODIGO = TB02112_CODIGO
             LEFT JOIN TB01010 ON TB01010_CODIGO = TB02112_PRODUTO
             LEFT JOIN TB02117 ON TB02117_CODIGO = TB02111_CODIGO
                              AND TB02117_NUMSERIE = TB02112_NUMSERIE
             LEFT JOIN TB01008 ON TB01008_CODIGO = TB02111_CODCLI
                 WHERE ' . implode(' AND ', $where) . '
              GROUP BY TB02112_CODIGO, TB01010_REFERENCIA, TB02111_CODCLI,
                       TB01008_NOME, TB02112_NUMSERIE
              ORDER BY TB01010_REFERENCIA, TB01008_NOME, TB02112_NUMSERIE';

        $statement = sqlsrv_query($database, $sql, $params);
        if ($statement === false) {
            throw new RuntimeException('Falha ao executar a consulta.');
        }

        while ($record = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
            $monthlyValues = [];
            foreach ($months as $index => $month) {
                $monthlyValues[$month['key']] = (float) $record['MES_' . $index];
            }
            $rows[] = [
                'contrato' => $record['CONTRATO'],
                'modelo' => $record['MODELO'],
                'codcli' => $record['CODCLI'],
                'cliente' => $record['CLIENTE'],
                'serial' => $record['SERIAL'],
                'months' => $monthlyValues,
                'medatual' => (float) $record['MEDATUAL']
            ];
        }
    } catch (Throwable $exception) {
        $error = 'Não foi possível consultar o banco. Verifique a conexão e a extensão SQLSRV do PHP.';
    }
}

?><!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leituras de Contadores</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <div class="app-shell">
        <header class="hero">
            <div>
                <p class="eyebrow">Relatório operacional</p>
                <h1>Leituras de Contadores por Mês</h1>
            </div>
            <div class="hero-card"><span>Registros encontrados</span><strong><?= count($rows) ?></strong></div>
        </header>
        <section class="panel">
            <form method="post" class="filters">
                <label><span>Modelo</span><input type="text" name="modelo" value="<?= esc($modelo) ?>"
                        placeholder="Ex.: M 2040"></label>
                <label><span>Contrato</span><input type="text" name="contrato" value="<?= esc($contrato) ?>"
                        placeholder="Ex.: 12345"></label>
                <label><span>Início</span><input type="month" name="inicio" value="<?= esc($start) ?>"></label>
                <label><span>Fim</span><input type="month" name="fim" value="<?= esc($end) ?>"></label>
                <button type="submit">Consultar banco</button>
            </form>
        </section>
        <section class="panel table-panel">
            <div class="table-heading">
                <div>
                    <p class="eyebrow">Resultado</p>
                    <h2>Produção mensal</h2>
                </div><span class="period-badge"><?= esc($start . ' a ' . $end) ?></span>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-error"><strong>Consulta não realizada.</strong> <?= esc($error) ?></div>
            <?php endif; ?>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Contrato</th>
                            <th>Modelo</th>
                            <th>CodCli</th>
                            <th>Cliente</th>
                            <th>Serial</th><?php foreach ($months as $month): ?>
                                <th><?= esc($month['label']) ?></th><?php endforeach; ?>
                            <th>Med. atual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$rows && !$error): ?>
                            <tr>
                                <td class="empty" colspan="<?= 6 + count($months) ?>">Nenhum registro do banco encontrado.
                                </td>
                            </tr><?php endif; ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= esc($row['contrato']) ?></td>
                                <td><?= esc($row['modelo']) ?></td>
                                <td><?= esc($row['codcli']) ?></td>
                                <td><?= esc($row['cliente']) ?></td>
                                <td><?= esc($row['serial']) ?></td><?php foreach ($months as $month): ?>
                                    <td class="number"><?= number_format($row['months'][$month['key']], 0, ',', '.') ?></td>
                                <?php endforeach; ?>
                                <td class="number emphasis"><?= number_format($row['medatual'], 0, ',', '.') ?></td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>

</html>
