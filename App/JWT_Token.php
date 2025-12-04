<?php 

namespace App;

use App\Data\Time;
use Exception;

class JWT_Token {

    private string $secretKey = 'e9cac20ca310d324ca363f745bd7643394355b9aabff9aea31baed5d4b470b78';

    public function CreatToken (array $payload, int $validation_time): string 
    {
        $b64Header = $this->header_encode();
        $b64Payload = $this->payload_encode($payload, $validation_time);
        $b64Signature = $this->signature("$b64Header.$b64Payload",$this->secretKey);

        return "$b64Header.$b64Payload.$b64Signature";
    }

    public function CheckToken (string $jwt_token) 
    {
        $parts = explode('.', $jwt_token);
        if(\count($parts) !== 3) 
            App::$app->response->jsonException('Invalid Token');

        $this->check_header($parts[0]);
        $this->check_signature($parts,$this->secretKey);
        return $this->check_payload($parts[1]);
    }

    private function check_header (string $b64Header): void 
    {
        $headerJson = $this->base64url_decode($b64Header);
        $header = json_decode($headerJson, true);
        if (!isset($header['algo']) || $header['algo'] !== 'HS256') 
            App::$app->response->jsonException('Invalid Token');
    }

    private function check_payload (string $b64Payload) 
    {
        $payloadJson = $this->base64url_decode($b64Payload);
        $payload = json_decode($payloadJson, true);

        if (!isset($payload['expires_at'])) 
            App::$app->response->jsonException('Invalid Token');

        if (isset($payload['expires_at']) && time() >= $payload['expires_at']){
            $new_token = $this->updateToken($payload);
            if(!$new_token) App::$app->response->jsonException('Token expired');
        } 

        return $payload;
    }

    private function check_signature (array $parts, string $secretKey) 
    {
        [$b64Header, $b64Payload, $b64Signature] = $parts;

        $signature = $this->base64url_decode($b64Signature);

        $expectedSig = hash_hmac('sha256', "$b64Header.$b64Payload", $secretKey, true);

        if (!hash_equals($expectedSig, $signature)) 
            App::$app->response->jsonException('Invalid Token'); 
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

    public function updateToken (array $payload): string  
    {
        $refresh_token = App::$app->cookie->get('refresh_token');
        if(!$refresh_token) App::$app->response->jsonException('Invalid Token');
        $this->CheckToken($refresh_token);
        $new_token = $this->CreatToken($payload, Time::Hours(2));
        App::$app->response->addToResponse(['new_token' => $new_token]);
        return $new_token;
    }
}


// // Usage example
// $secret = 'your-very-secret-key';
// $jwt = new JWT_Token();

// $token = $jwt->CreatToken(['sub' => 123, 'role' => 'admin'], $secret, 1800);
// echo "Token: $token\n";

// try {
//     $secret = 'your-very-secret-ke';
//     $payload = $jwt->CheckToken($token, $secret);
//     print_r($payload);
// } catch (Exception $e) {
//     echo "Verify failed: " . $e->getMessage();
// }