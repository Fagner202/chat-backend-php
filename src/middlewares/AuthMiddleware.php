<?php
namespace src\middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $key = "your_secret_key"; // Deveria ser uma variável de ambiente

    public function validateToken() {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            die(json_encode(['message' => 'Token nao fornecido']));
        }

        $authHeader = $headers['Authorization'];
        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            http_response_code(401);
            die(json_encode(['message' => 'Token invalido ou ausente']));
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            http_response_code(401);
            die(json_encode(['message' => 'Token invalido: ' . $e->getMessage()]));
        }
    }
}
