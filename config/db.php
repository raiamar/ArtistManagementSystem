<?php

require_once __DIR__ . '/config.php';


class Database  
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if(self::$instance == null)
        {
            $dsn = 'mysql:host=' . DB_HOST . ';port='.DB_PORT. ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []) : PDOStatement
    {
        $stm = self::getInstance()->prepare($sql);
        $stm->execute($params);
        return $stm;
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []) : array 
    {
        return self::query($sql, $params)->fetchAll();
    }


    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int) self::getInstance()->lastInsertId();
    }
    
}
