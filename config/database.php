<?php

$server = getenv('DB_SERVER') ?: '192.168.254.197';
$base = getenv('DB_DATABASE') ?: 'WC_NOVO';
$usuarioBanco = getenv('DB_USERNAME') ?: 'wc';
$SenhaBanco = getenv('DB_PASSWORD') ?: 'wc!@#$2023';

if (!function_exists('sqlsrv_connect')) {
    return false;
}

$connectionInfo = [
    'Database' => $base,
    'Encrypt' => false,
    'TrustServerCertificate' => false,
    'UID' => $usuarioBanco,
    'PWD' => $SenhaBanco,
    'CharacterSet' => 'UTF-8',
];

return sqlsrv_connect($server, $connectionInfo);
