<?php 

namespace App;

use PDO;

class Migrations {
    private const LAYOUT_PATH = __DIR__.'/layouts';

    public function applyMigrations (): void
    {
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(__DIR__."/Migrations");
        $toApplyMigrtions = array_diff($appliedMigrations,$files);;

        foreach ($toApplyMigrtions as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once __DIR__."/../../../database/migrations/$migration";

            $className = pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up();
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
            return $db->FetchAll("SELECT migration FROM migrations",PDO::FETCH_COLUMN);

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

        $fileName = 'M'.floor(microtime(true))."_$fileName";

        $migrationFile = file_get_contents(self::LAYOUT_PATH.'/migrations/createTable.php');
        $migrationFile = str_replace('tableName',$tableName,$migrationFile);

        file_put_contents(__DIR__."/../../database/migrations/$fileName.php",$migrationFile);
        echo "[ database/migrations/$fileName ] - Created Successfully \n";
    }

    public function alterTable (string $fileName): void
    {
        $tableName = '';

        if(str_contains($fileName,'table')) {
            $tableName = str_replace('alter_','', $fileName);
            $tableName = str_replace('_table','', $tableName);
        }

        $fileName = 'M'.floor(microtime(true))."_$fileName";

        $migrationFile = file_get_contents(self::LAYOUT_PATH.'/migrations/alterTable.php');
        $migrationFile = str_replace('tableName',$tableName,$migrationFile);
        
        file_put_contents(__DIR__."/../../database/migrations/$fileName.php",$migrationFile);
        echo "[ database/migrations/$fileName ] - Created Successfully \n";
    }
}