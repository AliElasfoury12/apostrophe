<?php 

namespace App\Controllers;

use App\Models\User;
use App\Request;
use App\Validator;

class AuthController {
    public function register (Request $request)  
    {
        $inputs = Validator::check($request->inputs(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|password|confirm'
        ]);

        User::create();

        return 'register'. json_encode($inputs);
    }
    
    public function login ()  
    {
        return 'login';
    }
}