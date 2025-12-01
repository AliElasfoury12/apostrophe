<?php 

namespace App\Migrations;

return new class {

    public function up ()  
    {
        return"CREATE TABLE migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255));";
    }

    public function down ()  
    {
    
    }
};