<?php 

namespace App;

class Request {
    public string $method;
    public string $url;
    public function __construct() {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->GetUrl();
    }

    private function GetUrl () 
    {
        $url = $_SERVER['REQUEST_URI'];
        $QuestionMarkPosition = strpos($url, '?');
        if($QuestionMarkPosition) $this->url = substr($url,0,$QuestionMarkPosition);
        else $this->url = $url;
    } 

}