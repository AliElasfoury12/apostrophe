<?php 

namespace App;

use App\Data\Headers;

class Request {
    public string $method;
    public string $url;
    public array $headers;
    public array $inputs = [];
    private array $auth_user = [];

    public function __construct() {
        $this->headers = getallheaders();
        $this->inputs();
        $this->method = $this->GetMethod() ;
        $this->GetUrl();
    }

    public function GetMethod (): string  
    {
        $post_override_method = $this->inputs['_method'] ?? null;

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
        $this->inputs = $_POST;

        if($this->inputs) return;

        if($this->header(Headers::CONTENT_TYPE) === 'application/json'){
            $raw_data = file_get_contents('php://input');
            $data = json_decode($raw_data,true);
            $this->inputs = $data;
        }
    }

    public function header (string $name):string|null  
    {
        return $this->headers[$name] ?? null;
    }

    public function auth_user ():array|null  
    {
        if($this->auth_user) return $this->auth_user;
        $guard = new Guard();
        $this->auth_user = $guard->GetAuthUser();
        return $this->auth_user;
    }

    public function bearerToken (): string|null  
    {
        $bearerToken = $this->header(Headers::AUTHORIZATION); 
        if(!$bearerToken) return null;
        return str_replace('Bearer ','',$bearerToken);
    }
}