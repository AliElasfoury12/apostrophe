<?php 

namespace App;

use Closure;

class Router {
    /**
    * @var Route[]*/
    public array $GetRoutes = [];
    /**
    * @var Route[] */
    public array $PostRoutes = [];
    /**
    * @var Route[]*/
    public array $PutRoutes = [];
    /**
    * @var Route[]*/
    public array $DeleteRoutes = [];


    public function resolve ()  
    {
        App::$app->routes->define();
        $route = $this->GetRoute();
        return $this->HandleCallback($route);
    }

    private function GetRoute (): Route|null 
    {
        $request = App::$app->request;
        $method = $request->method;
        $url = $request->url;
        $route = '';

        switch ($method) {
            case 'get': $route = $this->GetRoutes[$url]; break;

            case 'post': $route = $this->PostRoutes[$url]; break;
            
            case 'put': $route = $this->PutRoutes[$url]; break;
            
            case 'delete': $route = $this->DeleteRoutes[$url]; break;
        }

        if($route) return $route;
        return $this->HandleRouteWithParams($url,$method);
    }

    private function HandleCallback (Route|null $route) 
    {
        if($route === null) return 'Not Found 404';
        $request = App::$app->request;

        $callback = $route->callback;
        if(\is_array($callback)){
            $ClassInstance = new $callback[0];
            $method = $callback[1];
            return $ClassInstance->$method($request,...$route->params);
        }

        if($callback instanceof Closure) {
            return $callback($request,...$route->params);
        }

        return 'Not Found 404';
    }

    private function HandleRouteWithParams (string $url,string $method): Route|null 
    {
        $routes = $this->GetMethodRoutes($method);
        if(!$routes) return null;

        $request_url_parts = explode('/',$url);
        $request_url_parts_copy = $request_url_parts;
        $request_url_parts_count = \count($request_url_parts);

        foreach ($routes as &$route) {
            $route_url_parts = explode('/',$route->url);
            $route_url_parts_count = \count($route_url_parts);

            if($request_url_parts_count !== $route_url_parts_count) continue;

            $route_url = $this->CompareRoutesParts($route_url_parts,$request_url_parts,$route);
            
            if($route_url === $url) return $route;
            $request_url_parts = $request_url_parts_copy;
        }
        return null;
    }

    private function GetMethodRoutes (string $method): array|null 
    {
        switch ($method) {
            case 'get': return $this->GetRoutes;

            case 'post': return $this->PostRoutes;
            
            case 'put': return $this->PutRoutes;
            
            case 'delete': return $this->DeleteRoutes;
        }

        return null;
    }

    private function CompareRoutesParts (array $route_url_parts,array $request_url_parts,Route $route): string 
    {
        $route_url = $route->url;
        foreach ($route_url_parts as $key => $route_url_part) {
            if($route_url_part === $request_url_parts[$key]){
                unset($route_url_parts[$key], $request_url_parts[$key]);
                continue;
            }

            preg_match('/\{([^}]+)\}/',$route_url_part,$matches);
            if($matches){
                $route->params[$matches[1]] = $request_url_parts[$key];
                $route_url = str_replace($matches[0], $request_url_parts[$key], $route_url);
                continue;
            }

            break;
        }

        return $route_url;
    }
}