<?php 

namespace App;

use PDO;

class Migrations {
    private const LAYOUT_PATH = __DIR__.'/layouts';
    private const Migrations_DIR = __DIR__."/Migrations";

    public function applyMigrations (): void
    {
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(self::Migrations_DIR);
        $toApplyMigrtions = array_diff($files,$appliedMigrations);

        foreach ($toApplyMigrtions as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            $instance = require_once self::Migrations_DIR."/$migration";;
            $this->log("Applying migration $migration");
            $sql = $instance->up();
            Command::$command->db->exec($sql);
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }

        if (\count($newMigrations)) {
            $this->saveMigrations($newMigrations);
        }else {
           $this->log('All Migrations Are Applied');
        }
    }

    public function getAppliedMigrations ()
    {
        $db = Command::$command->db;
        if($db->tableIsExsists('migrations'))
            return $db->Fetch("SELECT migration FROM migrations");

        return [];
    }

    public function log (string $message): void
    {
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }

    private function saveMigrations (array $migrations): void
    {
        Command::$command->db->insert('migrations',['migration'],$migrations);
    }

    public function createTable (string $fileName): void
    {
        $tableName = '';

        if(str_contains($fileName,'table')) {
            $tableName = str_replace('create_','', $fileName);
            $tableName = str_replace('_table','', $tableName);
        }

        $fileName = floor(microtime(true)).'_'.date('Y-m-d')."_$fileName";
        $migrationFile = file_get_contents(self::LAYOUT_PATH.'/migrations/createTable.php');

        file_put_contents(self::Migrations_DIR."/$fileName.php",$migrationFile);
        echo "[ database/migrations/$fileName ] - Created Successfully \n";
    }

    public function alterTable (string $fileName): void
    {
        $tableName = '';

        if(str_contains($fileName,'table')) {
            $tableName = str_replace('alter_','', $fileName);
            $tableName = str_replace('_table','', $tableName);
        }

        $fileName = floor(microtime(true)).'_'.date('Y-m-d')."_$fileName";
        $migrationFile = file_get_contents(self::LAYOUT_PATH.'/migrations/alterTable.php');
        
        file_put_contents(self::Migrations_DIR."/$fileName.php",$migrationFile);
        echo "[ database/migrations/$fileName ] - Created Successfully \n";
    }
}