<?php 

namespace App\Models;

use App\App;

class User {

    private static array $fillable = [
        'name',
        'email',
        'password'
    ];

    public static function create (array $data): array|null  
    {
        $values = [];
        foreach (self::$fillable as $filed) {
            if($data[$filed]) $values[] = $data[$filed];
        }

        $db = App::$app->db;
        $is_success = $db->insert('users',self::$fillable,$values);

        if($is_success) return $db->lastRecord('users');
        return null;
    }
}