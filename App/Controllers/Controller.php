<?php 

namespace App\Controllers;

use App\App;
use App\Response;

class Controller {
    public function response (): Response  
    {
        return App::$app->response;
    }
}