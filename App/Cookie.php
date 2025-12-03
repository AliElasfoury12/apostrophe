<?php 

namespace App;

class Cookie {
    private string $name;
    private string $value;
    private int $expires;
    private bool $http_only = false;
    private string $path = '/';
    private string $domain = 'localhost';
    private bool $https = false;
    private string $sameSite = 'Lax';

    public function name (string $name): static  
    {
        $this->name = $name;
        return $this;
    }

    public function value (string $value): static  
    {
        $this->value = $value;
        return $this;
    }

    public function expires (int $expires): static  
    {
        $this->expires = time() + $expires;
        return $this;
    }

    public function http_only (): static  
    {
        $this->http_only = true;
        return $this;
    }

    public function path (string $path): static  
    {
        $this->path = $path;
        return $this;
    }

    public function domain (string $domain): static  
    {
        $this->domain = $domain;
        return $this;
    }

    public function https (): static  
    {
        $this->https = true;
        return $this;
    }

    public function Strict (): static  
    {
        $this->sameSite = 'Strict';
        return $this;
    }

    public function None (): static  
    {
        $this->sameSite = 'None';
        $this->https = true;
        return $this;
    }

    private function options (): array 
    {
        return [
            'expires' => $this->expires,
            'path' => $this->path,          // allow all paths
            'domain' => $this->domain,      // your domain
            'secure' => $this->https,       // only https
            'httponly' => $this->http_only, // JS cannot access the cookie
            'samesite' => 'Lax'             // or 'Lax' for cross-site
        ];
    }

    public function send (): void  
    {
        setcookie($this->name,$this->value,$this->options());
    }

    public function delete (): void  
    {
        $this->expires = time() - 60 *60;
        setcookie($this->name,$this->value,$this->options());
    }
}