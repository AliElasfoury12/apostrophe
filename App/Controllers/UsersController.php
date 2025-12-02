<?php 

namespace App\Controllers;

use App\App;
use App\Models\User;
use App\Response;

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
    
    public function update ()  
    {
    
    }

    public function delete ()  
    {
    
    }
}