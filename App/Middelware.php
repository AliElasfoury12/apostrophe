<?php 

namespace App;

class Middelware {

    public array $middelwares = [];

    public function __construct() {
        $this->middelwares = $this->define();
    }

    public function define ()  
    {
        return [
            'auth:jwt' => 'auth_jwt'
        ];
    }

    public function auth_jwt (Request $request)  
    {
        if($request->auth_user()) return;
        App::$app->response->jsonException('Unauthorized',401);
    }
}