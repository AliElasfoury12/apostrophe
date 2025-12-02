<?php 

namespace App\Models;

use App\App;

class User {

    private static array $fillable = [
        'name',
        'email',
        'password'
    ];

    public const ROLES = ['admin', 'user'];

    public static function create (array $data): array|null  
    {
        $values = [];
        foreach (self::$fillable as $field) {
            if($data[$field]) $values[] = $data[$field];
        }

        $db = App::$app->db;
        $is_success = $db->insert('users',self::$fillable,$values);

        if($is_success) return $db->lastRecord('users');
        return null;
    }

    public static function exsits (string $email):array|bool 
    {
        $sql = "SELECT * from users WHERE email = :email";
        return App::$app->db->Fetch($sql,[':email' => $email]);
    }

    public static function CreateToken (array $payload,int $time)  
    {
        return App::$app->jwt_token->CreatToken($payload,$time);
    }

    public static function all (string $columns): array  
    {
        $sql = "SELECT $columns FROM users";
        return App::$app->db->FetchAll($sql);
    }

    public static function update (array $data): bool  
    {
        $id = 1;
        return App::$app->db->update('users',$data,$id);
    }
}