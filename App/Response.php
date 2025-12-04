<?php 

namespace App;

class Response 
{
    private array $extra_params = [];
    public function json (array $response,int $code = 200): bool|string  
    {
        if($this->extra_params) $response = [...$response, ...$this->extra_params]; 
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode($response,JSON_PRETTY_PRINT);
    }

    public function errorJson ($errors,int $code = 422): bool|string  
    {
        return $this->json(['errors' => $errors],$code);
    }

    public function jsonException ($errors,int $code = 422): never
    {
        exit($this->errorJson($errors,$code));
    }

    public function addToResponse (array $params)  
    {
        $this->extra_params = [...$this->extra_params, ...$params]; 
    }
}