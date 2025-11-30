<?php 

namespace App\Controllers;

use App\Request;
use App\Validator;

class AuthController {
    public function register (Request $request)  
    {
        Validator::check($request->inputs(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|password|confirm'
        ]);

        return 'register'. json_encode($request->inputs());
    }
    
    public function login ()  
    {
        return 'login';
    }
}