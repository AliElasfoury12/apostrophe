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
        try {
            $request->auth_user();
        } catch (\Throwable $th) {
            App::$app->response->jsonException('Unauthorized',401);
        }
    }
}