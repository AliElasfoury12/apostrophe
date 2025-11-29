<?php 

namespace App;

class App {
    public Request $request;
    public Router $router;
    public static App $app;
    public function __construct() {
        $this->request = new Request();
        $this->router = new Router();
        self::$app = $this;
    }

    public function start ()  
    {
        echo $this->router->resolve();
    }
}