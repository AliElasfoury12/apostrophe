<?php 

namespace App;

use App\Data\Headers;

class Request {
    public string $method;
    public string $url;
    public array $headers;
    public array $inputs = [];

    public function __construct() {
        $this->headers = getallheaders();
        $this->inputs();
        $this->method = $this->GetMethod() ;
        $this->GetUrl();
    }

    public function GetMethod (): string  
    {
        $post_override_method = $this->inputs['_method'];

        if($post_override_method)
            return strtolower($post_override_method);
    
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    private function GetUrl () 
    {
        $url = $_SERVER['REQUEST_URI'];
        $QuestionMarkPosition = strpos($url, '?');
        if($QuestionMarkPosition) $this->url = substr($url,0,$QuestionMarkPosition);
        else $this->url = $url;
    } 

    private function inputs (): void 
    {
        if($this->header(Headers::CONTENT_TYPE) === 'application/json'){
            $raw_data = file_get_contents('php://input');
            $data = json_decode($raw_data,true);
            $this->inputs = $data;
            return;
        }

        $this->inputs = $_POST;
    }

    public function header (string $name):string|null  
    {
        return $this->headers[$name] ?? null;
    }

    public function auth_user ():array|null  
    {
        $bearerToken = $this->header(Headers::AUTHORIZATION); 
        if(!$bearerToken) return null;
        $token = str_replace('Bearer ','',$bearerToken);
        $payload = App::$app->jwt_token->CheckToken($token);
        if(!$payload) return null;
        return $payload['user'];
    }

}