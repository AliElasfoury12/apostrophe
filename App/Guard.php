<?php 

namespace App;

use App\Controllers\AuthController;
use App\Data\Headers;
use Exception;

class Guard {

    public function GetAuthUser ()  
    {
       return $this->JWT();
    }

    private function JWT () 
    {
        $response = App::$app->response;
        $jwt = App::$app->jwt_token;

        $token = App::$app->request->bearerToken();

        if(!$token){
            $refresh_token = App::$app->cookie->get('refresh_token');
            if(!$refresh_token) 
                throw new Exception('Invalid Token');

            $result = $jwt->CheckToken($refresh_token,JWT_Token::REFRESH_TOKEN);

            if($result['new_token']){
                $authController = new AuthController();
                $authController->RefreshTokenCookie($result['user'])->send();
            }

            $user = $result['payload']['user'];
            $access_token = $jwt->CreatToken(['user' => $user],JWT_Token::ACCESS_TOKEN);
            $response->addToResponse(['new_token' => $access_token]);

            return $user;
        }

        $result = $jwt->CheckToken($token,JWT_Token::ACCESS_TOKEN);

        if($result['new_token']){
            $response->addToResponse(['new_token' => $result['new_token']]);
        }
        
        return  $result['payload']['user'];
    }
}