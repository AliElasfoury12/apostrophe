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

    public function exec (string $query): bool|int 
    {
       return $this->pdo->exec($query);
    }

    public function prepare (string $prepare): bool|PDOStatement  
    {
        return $this->pdo->prepare($prepare);
    }

    public function FetchAll (string $sql, array $params = [], int $mode = PDO::FETCH_ASSOC): array  
    {
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll($mode);
    }

    public function Fetch (string $sql, array $params = [],int $mode = PDO::FETCH_ASSOC): array  
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch($mode);
    }

    public function insert (string $tableName, array $columns, array $values): bool  
    {
        $columns_string = implode(', ',$columns);
        $placholders = $this->CreatePlacholders($columns,$values);
       
        $sql = "INSERT INTO $tableName ($columns_string) VALUES $placholders";
        return $this->prepare($sql)->execute($values);
    }

    private function CreatePlacholders (array $columns,array $values): string 
    {
        $columnsCount = \count($columns);
        $valuesCount = \count($values);

        $is_multiple_insert = \is_array($values[0]) || $valuesCount > $columnsCount ;
        $placholders  = str_repeat('?, ',$columnsCount);
        $placholders = trim($placholders, ', ');
        $placholders = "($placholders)";
        
        if($is_multiple_insert) {
            $placholders = str_repeat("$placholders, ",$valuesCount);
            $placholders = trim($placholders, ', ');
        }

        return $placholders;
    }

   
    public function lastRecord (string $tableName): array  
    {
        $lastId = $this->pdo->lastInsertId();
        $sql = "SELECT * FROM  $tableName WHERE id = $lastId";
        return $this->Fetch($sql);
    }

    public function tableIsExsists (string $table): bool
    {   
        $sql = "SHOW TABLES LIKE '$table'";
        return $this->pdo->query($sql)->rowCount() === 1;
    }

}