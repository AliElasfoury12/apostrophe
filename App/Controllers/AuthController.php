<?php 

namespace App\Controllers;

use App\Models\User;
use App\Request;
use App\Response;
use App\Validator;

class AuthController {
    public function register (Request $request)  
    {
        $inputs = Validator::check($request->inputs(), [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|password|confirm'
        ]);

        $inputs['password'] = password_hash($inputs['password'],PASSWORD_DEFAULT);

        try {
            $user = User::create($inputs);
        } catch (\Throwable $th) {
            if(str_contains($th->getMessage(),'Duplicate entry')){
                return Response::json([
                    'errors' => [
                        'email' => 'Email Must Be Unique'
                    ]
                ],422);
            }
        }

        var_dump($user);
        unset($user['password'], $user['role']);

        return Response::json([
            'message' => 'User Created Successfully',
            'user' => $user
        ],201);
    }
    
    public function login ()  
    {   
        return 'login';
    }
}