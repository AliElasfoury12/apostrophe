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

    public static function exsits (string $email): array  
    {
        $sql = "SELECT * from users WHERE email = :email";
        return App::$app->db->Fetch($sql,[':email' => $email]);
    }
}