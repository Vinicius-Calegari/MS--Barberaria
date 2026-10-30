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
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Verify TLS against the CA provided by the database provider.
        $caBase64 = env('DB_SSL_CA_PEM_B64');
        if ($caBase64 !== '') {
            $pem = base64_decode($caBase64, true);
            if ($pem === false || !str_contains($pem, 'BEGIN CERTIFICATE')) {
                throw new RuntimeException('Invalid database CA certificate');
            }
            $caFile = sys_get_temp_dir() . '/ms-barbearia-aiven-ca.pem';
            if (!is_file($caFile) || file_get_contents($caFile) !== $pem) {
                if (file_put_contents($caFile, $pem, LOCK_EX) === false) {
                    throw new RuntimeException('Cannot save database CA certificate');
                }
            }
            $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        return new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $password,
            $options
        );
    } catch (Throwable $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit('Não foi possível conectar ao banco de dados.');
    }
}
