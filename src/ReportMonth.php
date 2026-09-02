<?php

final class ReportMonth
{
    public $key;
    public $label;

    public function __construct($key, $label)
    {
        $this->key = $key;
        $this->label = $label;
    }

    public function nextKey()
    {
        return (new DateTime($this->key . '-01'))->modify('+1 month')->format('Y-m');
    }
}
