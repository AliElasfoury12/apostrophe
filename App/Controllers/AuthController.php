<?php 

namespace App\Controllers;

use App\App;
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
            'password' => 'required|password|confirm|max:150'
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

        unset($user['password'], $user['role']);

        return Response::json([
            'message' => 'User Created Successfully',
            'user' => $user
        ],201);
    }
    
    public function login (Request $request)  
    {   
        $inputs = Validator::check($request->inputs(), [
            'email' => 'required|email|max:150',
            'password' => 'required|password|confirm|max:150'
        ]);

        $user = User::exsits($inputs['email']);

        if(!$user) return $this->UserNotFound();

        $is_password_correct = password_verify($inputs['password'],$user['password']);

        if(!$is_password_correct) return $this->UserNotFound();

        unset($user['password'], $user['role']);

        return Response::json([
            'message' => 'User Logged In Successfully',
            'user' => $user
        ]);

    }

    private function UserNotFound () 
    {
        return Response::json([
            'errors' => [
                'email' => 'User Not Found'
            ]
        ],422);
    }
}