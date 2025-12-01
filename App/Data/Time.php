<?php 

namespace App\Data;

class Time {
    public static function Minutes (int $minutes): int  
    {
        return $minutes * 60;
    }

    public static function Hours (int $hours): int  
    {
        return self::Minutes($hours * 60);
    }

    public static function Days (int $days): int  
    {
        return self::Hours($days * 24);
    }

}