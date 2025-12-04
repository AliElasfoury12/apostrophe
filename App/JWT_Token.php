<?php 

namespace App;

use App\Data\Time;
use Exception;

class JWT_Token {

    private string $secretKey = 'e9cac20ca310d324ca363f745bd7643394355b9aabff9aea31baed5d4b470b78';
    private int $access_token_time = 0;
    private int $refresh_token_time = 0;
    public const ACCESS_TOKEN = 'access_token';
    public const REFRESH_TOKEN = 'refresh_token';

    public function __construct() {
        $this->access_token_time = Time::Hours(2);
        $this->refresh_token_time = Time::Days(30);
    }

    public function CreatToken (array $payload, string $type): string 
    {
        if($type == self::ACCESS_TOKEN) $validation_time = $this->access_token_time;
        else $validation_time = $this->refresh_token_time;

        $b64Header = $this->header_encode();
        $b64Payload = $this->payload_encode($payload, $validation_time);
        $b64Signature = $this->signature("$b64Header.$b64Payload",$this->secretKey);

        return "$b64Header.$b64Payload.$b64Signature";
    }

    public function CheckToken (string $jwt_token, string $type) 
    {
        $parts = explode('.', $jwt_token);
        if(\count($parts) !== 3) 
            throw new Exception('Invalid Token');

        $this->check_header($parts[0]);
        $this->check_signature($parts,$this->secretKey);
        return $this->check_payload($parts[1],$type);
    }

    private function check_header (string $b64Header): void 
    {
        $headerJson = $this->base64url_decode($b64Header);
        $header = json_decode($headerJson, true);
        if (!isset($header['algo']) || $header['algo'] !== 'HS256') 
            throw new Exception('Invalid Token');
    }

    private function check_payload (string $b64Payload, string $type) 
    {
        $payloadJson = $this->base64url_decode($b64Payload);
        $payload = json_decode($payloadJson, true);
        $new_token = null;

        if (!isset($payload['expires_at'])) 
            throw new Exception('Invalid Token');

        if (isset($payload['expires_at']) && time() >= $payload['expires_at']){
            $new_token = App::$app->jwt_token->CreatToken($payload,$type);
        } 

        return ['payload' => $payload, 'new_token' => $new_token];
    }

    private function check_signature (array $parts, string $secretKey) 
    {
        [$b64Header, $b64Payload, $b64Signature] = $parts;

        $signature = $this->base64url_decode($b64Signature);

        $expectedSig = hash_hmac('sha256', "$b64Header.$b64Payload", $secretKey, true);

        if (!hash_equals($expectedSig, $signature)) 
            throw new Exception('Invalid Token'); 
    }

    private function header_encode (): string 
    {
        $header = ['algo' => 'HS256', 'type' => 'JWT'];
        return  $this->base64url_encode(json_encode($header));
    }

    private function payload_encode (array $payload, int $validationTime): string 
    {
        $now = time();
        $payload = [
            'created_at' => $now,
            'expires_at' => $now + $validationTime,
            ...$payload
        ];
        return $this->base64url_encode(json_encode($payload));
    }

    private function signature (string $data, string $secretKey) 
    {
        $signature = hash_hmac('sha256', $data, $secretKey, true);
        return $this->base64url_encode($signature);
    }

    private function base64url_encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64url_decode(string $data): string 
    {
        $remainder = \strlen($data) % 4;
        if ($remainder) $data .= str_repeat('=', 4 - $remainder);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function CreateSecretKey (): string  
    {
        return bin2hex(random_bytes(32));
    }

}