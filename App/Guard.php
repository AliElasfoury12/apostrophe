<?php 

namespace App;

use App\Controllers\AuthController;
use App\Models\User;
use Exception;

class Guard {

    public function GetAuthUser ()  
    {
       return $this->JWT();
    }

    private function JWT ():array 
    {
        $jwt = App::$app->jwt_token;

        $refresh_token_payload = $this->CheckRefreshToken($jwt);
        $user = $refresh_token_payload['user'];

        $access_token = App::$app->request->bearerToken();

        $this->CheckAccessToken($access_token, $jwt,$user);
        
        return $user;       
    }

    private function CheckRefreshToken (JWT_Token $jwt)
    {
        $refresh_token = App::$app->cookie->get('refresh_token');
        if(!$refresh_token) throw new Exception('Invalid Token');

        $result = $jwt->CheckToken($refresh_token,JWT_Token::REFRESH_TOKEN);
        $user = $result['payload']['user'];

        if($result['new_token']){
            $authController = new AuthController();
            $refresh_token_cookie = $authController->RefreshTokenCookie($user);

            $user_exsist = User::find($user['id']);
            if(!$user_exsist) {
                $refresh_token_cookie->delete();
                throw new Exception("User Doesn't Exsists");
            }

            $refresh_token_cookie->send();
        }

        return $result['payload'];
    }

    private function CheckAccessToken (string $token,JWT_Token $jwt, array $user): void 
    {
        $response = App::$app->response;

        if($token) {
            $result = $jwt->CheckToken($token,JWT_Token::ACCESS_TOKEN);

            if($result['new_token'])
                $response->addToResponse(['new_token' => $result['new_token']]);            
        }else{
            $access_token = $jwt->CreatToken(['user' => $user],JWT_Token::ACCESS_TOKEN);
            $response->addToResponse(['new_token' => $access_token]);
        }
    }
}