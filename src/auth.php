<?php
require_once 'vendor/autoload.php';
require_once 'Database.php';  // Inclui a classe Database

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use src\Database;

try {
    $database = new Database();
    $pdo = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['message' => 'Email e senha sao obrigatorios']);
            exit;
        }

        // Consultar usuario no banco de dados
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Gerar o token JWT
            $key = "your_secret_key"; // Use uma chave secreta forte e unica
            $payload = [
                'iss' => "http://localhost:8080",
                'aud' => "http://localhost:8080",
                'iat' => time(),
                'exp' => time() + (60 * 60), // Expira em 1 hora
                'user_id' => $user['id']
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Credenciais invalidas']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Metodo nao permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Erro de conexao: ' . $e->getMessage()]);
}