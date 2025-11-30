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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|password|confirm'
        ]);

        $inputs['password'] = password_hash($inputs['password'],PASSWORD_DEFAULT);

        $user = User::create($inputs);

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