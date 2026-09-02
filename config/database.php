<?php

$server = getenv('DB_SERVER') ?: 'localhost';
$base = getenv('DB_DATABASE') ?: 'MAQLAREM';
$usuarioBanco = getenv('DB_USERNAME') ?: 'sa';
$SenhaBanco = getenv('DB_PASSWORD') ?: 'databit@2022';

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
