<?php
/**
 * Conexão com o banco de dados (PDO + MySQL).
 * Ajuste as constantes abaixo se o seu MySQL usar outro usuário/senha.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'mytreino');
define('DB_USER', 'root'); // padrão do XAMPP
define('DB_PASS', '');     // padrão do XAMPP (sem senha)

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            // Lança exceção em caso de erro de SQL
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Retorna resultados como array associativo
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Usa prepared statements nativos do MySQL
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    exit('Não foi possível conectar ao banco de dados. Verifique o arquivo config/db.php e se o MySQL está rodando.');
}
