<?php 

namespace App;

use PDO;
use PDOException;
use PDOStatement;
class DB {
    private string $host = 'localhost';
    private string $dbName = 'apostrophe';
    private string $charset = 'utf8mb4';
    private string $user = 'root';
    private string $password = 'root';
    private PDO $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO($this->dsn(),$this->user,$this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection Failed: {$e->getMessage()}");
        }
    }

    private function dsn (): string 
    {
        return "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
    }

    public function execute (string $query): bool|int 
    {
       return $this->pdo->exec($query);
    }

    public function prepare (string $prepare): bool|PDOStatement  
    {
        return $this->pdo->prepare($prepare);
    }

    public function FetchAll (string $sql, int $mode = PDO::FETCH_ASSOC): array  
    {
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll($mode);
    }

    // public function CreateDB (): bool|int  
    // {
    //     $sql = "CREATE DATABASE {$this->dbName};";
    //     return $this->execute($sql);
    // }
}