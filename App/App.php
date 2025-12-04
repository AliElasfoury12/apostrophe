<?php 

namespace App;

class App {
    public Request $request;
    public Routes $routes;
    public Router $router;
    public DB $db;
    public JWT_Token $jwt_token;
    public Cookie $cookie;
    public Response $response;
    public static App $app;
    
    public function __construct() {
        $this->request = new Request();
        $this->router = new Router();
        $this->routes = new Routes();
        $this->db = new DB();
        $this->jwt_token = new JWT_Token();
        $this->cookie = new Cookie();
        $this->response = new Response();
        self::$app = $this;
    }

    public function start ()  
    {
        try {
           echo $this->router->resolve();
        } catch (\Throwable $th) {
            echo '<pre>';
            var_dump($th);
            echo'</pre>';
        }
    }
}