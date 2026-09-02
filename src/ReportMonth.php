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
}
