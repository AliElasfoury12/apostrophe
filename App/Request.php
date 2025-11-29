<?php 

namespace App;

class Request {

    public string $method;
    public function __construct() {
        $this->method = strtolower($_REQUEST['method']);
    }
}