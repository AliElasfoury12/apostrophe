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
        $auth_id = $user['id'];

        if($auth_id != $id && $user['role'] == User::USER) {
            $this->response()->jsonException([
                'error' => 'Unauthorized'
            ],401);
        }

        if($user['role'] == User::ADMIN){
            $admin_user = User::find($auth_id);
            if(!$admin_user){
                $this->response()->jsonException([
                    'error' => 'Unauthorized'
                ],401);
            }
        }

        if($auth_id != $id) {
            $user = User::find($id);
            $user['role'] = User::ROLES[$user['role']];
            unset($user['password'], $user['created_at'], $user['updated_at']);
        }
        return $user;
    }

}