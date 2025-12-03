<?php 

namespace App;

class Response 
{
    public static function json ($response,int $code = 200): bool|string  
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode($response,JSON_PRETTY_PRINT);
    }

    public static function errorJson ($errors,int $code = 422): bool|string  
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode(['errors' => $errors],JSON_PRETTY_PRINT);
    }

    public static function jsonException ($errors,int $code = 422): never
    {
        exit(self::errorJson($errors,$code));
    }
}