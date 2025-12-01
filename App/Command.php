<?php

namespace App;

class Command  
{
    public Migrations $migrations;
    public DB $db;
    public static Command $command;

    public function __construct() {
        $this->migrations = new Migrations;
        $this->db = new DB;
        self::$command = $this;
    }

    public function handleCommand ($argv): void 
    {
        if($argv[0] != 'bmbo' || empty($argv[1])) {
            $this->notFound();
        }

        switch ($argv[1]) {
            case 'start':
                $port = 8000;
                while (\is_resource(@fsockopen('localhost',$port))) {
                   $port++;
                }
                exec("php -S localhost:$port -t public/");
            break;

            case 'migrate':
                $this->migrations->applyMigrations();
            break;

            case 'migration':
                if(str_contains($argv[2],'create')) 
                    $this->migrations->createTable( $argv[2]);
                elseif(str_contains($argv[2],'alter'))
                    $this->migrations->alterTable($argv[2]);
            break;

            default:
                $this->notFound();
            break;
        }
    
    }

    public function notFound (): void 
    {
        echo "Command Not Found \n";
        exit;
    }
}