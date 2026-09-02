<?php

function buildMonths($startDate, $endDate)
{
    $months = [];
    $cursor = new DateTime($startDate->format('Y-m-01'));
    $end = new DateTime($endDate->format('Y-m-01'));

    while ($cursor <= $end) {
        $months[] = new ReportMonth($cursor->format('Y-m'), mb_strtoupper($cursor->format('M/Y'), 'UTF-8'));
        $cursor = $cursor->modify('+1 month');
    }

    return $months;
}

function fetchReportRows(FilterForm $filters, array $months)
{
    $sample = [
        [
            'contrato' => 'TB02112_CODIGO',
            'modelo' => 'TB01010_REFERENCIA',
            'codcli' => 'TB02111_CODCLI',
            'cliente' => 'TB01008_NOME',
            'serial' => 'TB02112_NUMSERIE',
            'medatual' => 'TB02117_MEDATUAL',
            'seed' => 1000,
        ],
        [
            'contrato' => '000123',
            'modelo' => 'M 2040',
            'codcli' => '00158',
            'cliente' => 'CLIMACO',
            'serial' => 'VR99723456',
            'medatual' => 12500,
            'seed' => 1200,
        ],
        [
            'contrato' => '000456',
            'modelo' => 'M 2540',
            'codcli' => '00421',
            'cliente' => 'ESCOLA FRENET',
            'serial' => 'VR99151222',
            'medatual' => 8300,
            'seed' => 950,
        ],
    ];

    return array_values(array_map(function ($row) use ($filters, $months) {
        $monthly = [];
        foreach ($months as $index => $month) {
            $monthly[$month->key] = $row['seed'] + ($index * 125);
        }

        return [
            'contrato' => $row['contrato'],
            'modelo' => $row['modelo'],
            'codcli' => $row['codcli'],
            'cliente' => $row['cliente'],
            'serial' => $row['serial'],
            'months' => $monthly,
            'medatual' => $row['medatual'],
        ];
    }, $sample));
}
