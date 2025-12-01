<?php 

namespace App\Migrations;

return new class {

    public function up ()  
    {
        return"CREATE TABLE migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP);";
    }

    public function down ()  
    {
    
    }
};