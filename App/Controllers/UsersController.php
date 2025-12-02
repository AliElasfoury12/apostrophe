<?php 

namespace App\Controllers;

use App\Models\User;
use App\Request;
use App\Response;
use App\Validator;

class UsersController
{
    public function index ()  
    {
        $users = User::all('id,name,email,role');

        foreach ($users as &$user) {
            $user['role'] = User::ROLES[$user['role']];
        }

        return Response::json(['users' => $users]);
    }
    
    public function update (Request $request)  
    {
        $inputs = Validator::check($request->inputs(), [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
        ]);
    }

    public function delete ()  
    {
    
    }
}