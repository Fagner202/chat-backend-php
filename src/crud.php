<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Configurações do banco de dados
$host = 'mysql';
$dbname = 'mydb';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

// Função para validar o token JWT
function validateToken() {
    $headers = apache_request_headers();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        die(json_encode(['message' => 'Token não fornecido']));
    }

    $authHeader = $headers['Authorization'];
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    if (!$jwt) {
        http_response_code(401);
        die(json_encode(['message' => 'Token inválido ou ausente']));
    }

    $key = "your_secret_key"; // Defina uma chave secreta segura
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        die(json_encode(['message' => 'Token inválido: ' . $e->getMessage()]));
    }
}

// Protege rotas GET, PUT e DELETE com o token JWT
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    validateToken();
}

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obter todos os usuários
    $stmt = $pdo->query("SELECT id, name, email FROM users");
    $users = $stmt->fetchAll();
    echo json_encode($users);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Esta rota está desprotegida para permitir criação de usuários
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido nesta rota']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Atualizar um usuário
    parse_str(file_get_contents("php://input"), $put_vars);
    $id = $put_vars['id'] ?? null;
    $name = $put_vars['name'] ?? null;
    $email = $put_vars['email'] ?? null;

    if (!$id || !$name || !$email) {
        http_response_code(400);
        die(json_encode(['message' => 'ID, Nome e Email são obrigatórios']));
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);

    echo json_encode(['message' => 'Usuário atualizado com sucesso']);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deletar um usuário
    parse_str(file_get_contents("php://input"), $delete_vars);
    $id = $delete_vars['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        die(json_encode(['message' => 'ID é obrigatório']));
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['message' => 'Usuário deletado com sucesso']);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido']);
}
