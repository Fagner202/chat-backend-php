<?php
namespace src\controllers;

require_once 'vendor/autoload.php';
require_once 'config/Database.php';

use src\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $pdo;
    private $key = "your_secret_key"; // Melhor se extraído de uma variável de ambiente

    /**
     * Construtor da classe AuthController.
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $database = new Database('production');
        $this->pdo = $database->getConnection();
    }

    /**
     * Método para realizar o login de um usuário.
     * 
     * @param string $email Email do usuário.
     * @param string $password Senha do usuário.
     * @return array Mensagem de sucesso ou erro com o token JWT.
     */
    public function login($email, $password) {
        if (!$email || !$password) {
            http_response_code(400);
            return ['message' => 'Email e senha sao obrigatorios'];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'iss' => "http://localhost:8080",
                'aud' => "http://localhost:8080",
                'iat' => time(),
                'exp' => time() + (60 * 60), // 1 hora
                'user_id' => $user['id']
            ];
            $jwt = JWT::encode($payload, $this->key, 'HS256');
            return ['token' => $jwt];
        } else {
            http_response_code(401);
            return ['message' => 'Credenciais invalidas'];
        }
    }
}