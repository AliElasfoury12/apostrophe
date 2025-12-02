<?php 

namespace App\Models;

use App\App;
use App\JWT_Token;

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
        foreach (self::$fillable as $filed) {
            if($data[$filed]) $values[] = $data[$filed];
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
        $secretKey = 'e9cac20ca310d324ca363f745bd7643394355b9aabff9aea31baed5d4b470b78';
        $jwt_token = new JWT_Token();
        return $jwt_token->CreatToken($payload,$secretKey,$time);
    }

    public static function all (string $columns): array  
    {
        $sql = "SELECT $columns FROM users";
        return App::$app->db->FetchAll($sql);
    }
}