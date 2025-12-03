<?php 

namespace App\Controllers;

use App\DB;
use App\Models\User;
use App\Request;
use App\Response;
use App\Validator;
use PDOException;

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
    
    public function update (Request $request,int $id)  
    {
        $user = $this->authorizeUser($request,$id);

        $inputs = $this->validateInputs($request,$user);

        $user = $this->updateRecordInDB($inputs,$user,$id);

        $access_token = null;
        if ($request->auth_user()['id'] == $id) {
            $authController = new AuthController();
            $access_token = $authController->createNewUserTokens($user);
        }
        
        return Response::json([
            'message' => 'User Updated Successfully',
            'user' => $user,
            'new_token' => $access_token
        ]);
    }

    public function delete (Request $request, int $id)  
    {
        $user = $this->authorizeUser($request,$id);
    }

    private function authorizeUser (Request $request, int $id): array|null 
    {
        $user = $request->auth_user();

        if($user['id'] != $id && $user['role'] !== User::ADMIN) {
            Response::jsonException([
                'error' => 'Unauthorized'
            ],401);
        }

        if($user['id'] != $id) {
            $user = User::find($id);
            unset($user['password'], $user['created_at'], $user['updated_at']);
        }
        return $user;
    }

    private function validateInputs (Request $request, array $user): array 
    {
        $inputs = Validator::check($request->inputs, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
        ]);

        foreach ($inputs as $name => $value) {
            if($value === @$user[$name]) unset($inputs[$name]);
        }

        return $inputs;
    }

    private function updateRecordInDB (array $inputs,array $user,int $id) 
    {
        $is_updated = false;
        try {
            $is_updated = User::update($inputs,$id);
        } catch (PDOException $e) {
            if(DB::DuplicateEntery($e))
                Response::jsonException(['email' => 'Email already Taken']);
        }

        if(!$is_updated)  
            Response::jsonException(['message' => 'No records Effected']);

        foreach ($inputs as $name => $value) {
            if(@$user[$name]) $user[$name] = $value;
        }

        return $user;
    }
}