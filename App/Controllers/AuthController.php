<?php 

namespace App\Controllers;

use App\Cookie;
use App\Data\Time;
use App\DB;
use App\Models\User;
use App\Request;
use App\Validator;
use PDOException;

class AuthController extends Controller {
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
                return $this->response()->errorJson(['email' => 'Email Must Be Unique']);
        }

        unset($user['password'],$user['role'],$user['created_at'],$user['updated_at']);

        return $this->response()->json([
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

        return $this->response()->json([
            'message' => 'User Logged In Successfully',
            'user' => $user,
            'token' => $access_token
        ]);

    }

    public function changePassword (Request $request,int $user_id)  
    {
        $user = User::find($user_id);

        $this->authorizeUser($request,$user_id);

        $new_password_hash = $this->validatePassword($request,$user);

        $is_updated = User::update(['password' => $new_password_hash],$user_id);

        if(!$is_updated) $this->response()->jsonException(['something went wrong']) ;

        unset($user['password'], $user['created_at'], $user['updated_at']);

        $user['role'] = User::ROLES[$user['role']];

        return $this->response()->json([
            'message' => 'user password updated successfully',
            'user' => $user
        ]);
    }

    public function isValidUser (array $inputs): array|bool  
    {
        $user = User::exsits($inputs['email']);

        if(!$user) 
            $this->response()->jsonException(['email' => 'User Not Found']);

        $is_password_correct = password_verify($inputs['password'],$user['password']);

        if(!$is_password_correct) 
            $this->response()->jsonException(['email' => 'User Not Found']);

        return $user;
    }

    public function RefreshTokenCookie (array $user): Cookie 
    {
        $time = Time::Days(30);
        $payload = ['type' => 'refresh_token', 'user' => $user];
        $refresh_token = User::CreateToken($payload,$time);
        $cookie = new Cookie();
        $cookie->name('refresh_token')->value($refresh_token)
        ->expires($time)->http_only();

        return $cookie;
    }

    public function createNewUserTokens (array $user): string  
    {
        $this->RefreshTokenCookie($user)->send();
        return User::CreateToken(['user' => $user, 'type' => 'access_token'],Time::Hours(2));
    }

    private function validatePassword (Request $request, array $user): string 
    {
        $inputs = Validator::check($request->inputs, [
            'password' => 'required|password|max:150',
            'new_password' => 'required|password|confirm|max:150'
        ]);

        $is_password_correct = password_verify($inputs['password'],$user['password']);

        if(!$is_password_correct) 
            $this->response()->jsonException(['password' => 'Wrong Password!']);

        $is_same_password = password_verify($inputs['new_password'],$user['password']);

        if($is_same_password) $this->response()->jsonException(["New Password Can't be Old Password"]) ;

        return password_hash($inputs['new_password'],PASSWORD_DEFAULT);
    }
}