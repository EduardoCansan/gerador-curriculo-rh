<?php

class Database
{
    private static ?PDO $instance = null;

    /**
     * Retorna a instância única (Singleton) da conexão PDO.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
                );

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    die('Erro de conexão com o banco de dados: ' . $e->getMessage());
                }
                die('Erro interno. Tente novamente mais tarde.');
            }
        }

        return self::$instance;
    }

    // Impede instanciação e clonagem
    private function __construct() {}
    private function __clone() {}
}
