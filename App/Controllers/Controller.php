<?php 

namespace App\Controllers;

use App\App;
use App\Models\User;
use App\Request;
use App\Response;

class Controller {
    public function response (): Response  
    {
        return App::$app->response;
    }

    protected function authorizeUser (Request $request, int $id): array|null 
    {
        $user = $request->auth_user();

        if($user['id'] != $id && $user['role'] !== User::ADMIN) {
            $this->response()->jsonException([
                'error' => 'Unauthorized'
            ],401);
        }

        if($user['id'] != $id) {
            $user = User::find($id);
            unset($user['password'], $user['created_at'], $user['updated_at']);
        }
        return $user;
    }

}