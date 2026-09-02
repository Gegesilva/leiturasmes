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
        $today = new DateTime('first day of this month');
        $inicio = !empty($query['inicio']) ? new DateTime($query['inicio'] . '-01') : clone $today;
        $fim = !empty($query['fim']) ? new DateTime($query['fim'] . '-01') : clone $today;

        if (empty($query['inicio'])) {
            $inicio = $inicio->modify('-2 months');
        }

        if ($fim->getTimestamp() < $inicio->getTimestamp()) {
            $temp = $inicio;
            $inicio = $fim;
            $fim = $temp;
        }

        $inicio = new DateTime($inicio->format('Y-m-01'));
        $fim = new DateTime($fim->format('Y-m-01'));

        $modelo = isset($query['modelo']) ? trim((string)$query['modelo']) : '';
        $contrato = isset($query['contrato']) ? trim((string)$query['contrato']) : '';

        return new self(
            $modelo,
            $contrato,
            $inicio,
            $fim
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

    public function periodLabel()
    {
        return $this->startDate->format('m/Y') . ' a ' . $this->endDate->format('m/Y');
    }
}
