<?php

declare(strict_types=1);

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function getDBConnection(): PDO
{
    $host = env('DB_HOST', 'localhost');
    $port = env('DB_PORT', '3306');
    $name = env('DB_NAME', 'ms_barbearia');
    $user = env('DB_USER', 'root');
    $password = env('DB_PASSWORD');

    try {
        return new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit('Não foi possível conectar ao banco de dados.');
    }
}
