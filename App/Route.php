<?php 

namespace App;

class Route {
    
    public static function get ()  
    {
        self::AddRoute('get');
    }

    public static function post ()  
    {
    
    }

    private static function AddRoute (string $method) 
    {
        switch ($method) {
            case 'get':
                break;
            
            default:
                # code...
                break;
        }
    }
}