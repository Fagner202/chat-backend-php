<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$host = 'mysql';
$dbname = 'mydb';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Consultar usuário no banco de dados
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Gerar o token JWT
            $key = "your_secret_key"; // Use uma chave secreta forte e única
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
            echo json_encode(['message' => 'Credenciais inválidas']);
        }
    }
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
