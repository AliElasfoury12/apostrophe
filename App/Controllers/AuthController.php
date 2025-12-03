<?php 

namespace App\Controllers;

use App\Cookie;
use App\Data\Time;
use App\DB;
use App\Models\User;
use App\Request;
use App\Response;
use App\Validator;
use PDOException;

class AuthController {
    public function register (Request $request)  
    {
        $inputs = Validator::check($request->inputs, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|password|confirm|max:150'
        ]);

        $inputs['password'] = password_hash($inputs['password'],PASSWORD_DEFAULT);

        try {
            $user = User::create($inputs);
        } catch (PDOException $e) {
            if(DB::DuplicateEntery($e))
                return Response::errorJson(['email' => 'Email Must Be Unique']);
        }

        unset($user['password'],$user['role'],$user['created_at'],$user['updated_at']);

        return Response::json([
            'message' => 'User Created Successfully',
            'user' => $user
        ],201);
    }
    
    public function login (Request $request)  
    {   
        $inputs = Validator::check($request->inputs, [
            'email' => 'required|email|max:150',
            'password' => 'required|password|max:150'
        ]);

        $user = $this->isValidUser($inputs);
       
        $user['role'] = User::ROLES[$user['role']];

        unset($user['password'],$user['created_at'],$user['updated_at']);

       $access_token = $this->createNewUserTokens($user);

        return Response::json([
            'message' => 'User Logged In Successfully',
            'user' => $user,
            'token' => $access_token
        ]);

    }

    public function isValidUser (array $inputs): array|bool  
    {
        $user = User::exsits($inputs['email']);

        if(!$user) 
            Response::jsonException(['email' => 'User Not Found']);

        $is_password_correct = password_verify($inputs['password'],$user['password']);

        if(!$is_password_correct) 
            Response::jsonException(['email' => 'User Not Found']);

        return $user;
    }
    private function SendRefreshTokenCookie (array $user) 
    {
        $time = Time::Days(30);
        $payload = ['type' => 'refresh_token', 'user' => $user];
        $refresh_token = User::CreateToken($payload,$time);
        $cookie = new Cookie();
        $cookie->name('refresh_token')->value($refresh_token)
        ->expires($time)->http_only()->send();
    }
    public function changePassword (Request $request)  
    {
        $inputs = Validator::check($request->inputs, [
            'password' => 'required|password|max:150',
            'new_password' => 'required|password|confirm|max:150'
        ]);
    }

    public function createNewUserTokens (array $user): string  
    {
        $this->SendRefreshTokenCookie($user);
        return User::CreateToken(['user' => $user, 'type' => 'access_token'],Time::Hours(2));
    }
}