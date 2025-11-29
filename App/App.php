<?php 

namespace App;

class App {
    public Request $request;
    public Routes $routes;
    public Router $router;
    public static App $app;
    public function __construct() {
        $this->request = new Request();
        $this->router = new Router();
        $this->routes = new Routes();
        self::$app = $this;
    }

    public function start ()  
    {
        echo $this->router->resolve();
    }
}