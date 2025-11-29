<?php 

namespace App;

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
        $request = App::$app->request;
        $method = $request->method;
        $url = $request->url;
        $route = '';

        switch ($method) {
            case 'get':
                $route = $this->GetRoutes[$url];
                break;

            case 'post':
                $route = $this->PostRoutes[$url];
                break;
            
            case 'put':
                $route = $this->PutRoutes[$url];
                break;
            
            case 'delete':
                $route = $this->DeleteRoutes[$url];
                break;
        }
    }
}