<?php 

namespace App;

use Closure;

class Route {
    public string $url;
    public Array|string|Closure $callback;
    
    public function __construct(string $url,Array|string|Closure $callback) {
        $this->url = $url;
        $this->callback = $callback;
    }

    public static function get (string $url,Array|string|Closure $callback)  
    {
        App::$app->router->GetRoutes[$url] = new Route($url,$callback);
    }

    public static function post (string $url,Array|string|Closure $callback)  
    {
        App::$app->router->PostRoutes[$url] = new Route($url,$callback);
    }

    public static function put (string $url,Array|string|Closure $callback)  
    {
        App::$app->router->PutRoutes[$url] = new Route($url,$callback);
    }

    public static function delete (string $url,Array|string|Closure $callback)  
    {
        App::$app->router->DeleteRoutes[$url] = new Route($url,$callback);
    }
    
}