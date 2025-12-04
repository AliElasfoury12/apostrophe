<?php 

namespace App;

use Closure;

class Route {
    public string $url;
    public array|string|Closure $callback;
    public array $params = [];
    public array $middelwares = [];
    public function __construct(string $url,array|string|Closure $callback) {
        $this->url = $url;
        $this->callback = $callback;
    }

    public static function get (string $url,array|string|Closure $callback): Route  
    {
        $route = new Route($url,$callback);
        App::$app->router->GetRoutes[$url] = $route;
        return $route;
    }

    public static function post (string $url,array|string|Closure $callback): Route  
    {
        $route = new Route($url,$callback);
        App::$app->router->PostRoutes[$url] = $route;
        return $route;
    }

    public static function put (string $url,array|string|Closure $callback)  
    {
        $route = new Route($url,$callback);
        App::$app->router->PutRoutes[$url] = $route;
        return $route;
    }

    public static function patch (string $url,array|string|Closure $callback)  
    {
        $route = new Route($url,$callback);
        App::$app->router->PatchRoutes[$url] = $route;
        return $route;
    }

    public static function delete (string $url,array|string|Closure $callback)  
    {
        $route = new Route($url,$callback);
        App::$app->router->DeleteRoutes[$url] = $route;
        return $route;
    }

    public function middleware (array $middelwares)  
    {
        $this->middelwares = $middelwares;
    }
    
}