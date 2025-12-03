<?php 

namespace App\Models;

use App\App;
use App\Response;

class User {

    private static array $fillable = [
        'name',
        'email',
        'password'
    ];

    public const ADMIN = 'admin';
    public const USER = 'user';
    public const ROLES = [self::ADMIN, self::USER];

    public static function create (array $data): array|null  
    {
        $data = self::filterFillableInputs($data);
        $data = array_values($data);

        $db = App::$app->db;
        $is_success = $db->insert('users',self::$fillable,$data);

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

    public static function update (array $data, int|string $id): bool  
    {
        $data = self::filterFillableInputs($data);
        if(!$data) false;
        return App::$app->db->update('users',$data,$id);
    }

    private static function filterFillableInputs (array $data): array 
    {
        $values = [];
        foreach (self::$fillable as $field) {
            if(@$data[$field]) $values[$field] = $data[$field];
        }

        return $values;
    }

    public static function find (int|string $id)  
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        return App::$app->db->Fetch($sql,[$id]);
    }

    public static function delete (int|string $id): bool  
    {
       return App::$app->db->delete('users',$id);
    }
}