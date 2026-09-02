<?php

final class FilterForm
{
    public $modelo;
    public $contrato;
    public $startDate;
    public $endDate;

    public function __construct($modelo, $contrato, $startDate, $endDate)
    {
        $this->modelo = $modelo;
        $this->contrato = $contrato;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function fromRequest(array $query)
    {
        $today = new DateTimeImmutable('first day of this month');
        $inicio = !empty($query['inicio']) ? new DateTimeImmutable($query['inicio'] . '-01') : $today->modify('-2 months');
        $fim = !empty($query['fim']) ? new DateTimeImmutable($query['fim'] . '-01') : $today;

        if ($fim < $inicio) {
            $temp = $inicio;
            $inicio = $fim;
            $fim = $temp;
        }

        $modelo = isset($query['modelo']) ? trim((string)$query['modelo']) : '';
        $contrato = isset($query['contrato']) ? trim((string)$query['contrato']) : '';

        return new self(
            $modelo,
            $contrato,
            $inicio->modify('first day of this month'),
            $fim->modify('first day of this month')
        );
    }

    public function startMonthValue()
    {
        return $this->startDate->format('Y-m');
    }

    public function endMonthValue()
    {
        return $this->endDate->format('Y-m');
    }
}
