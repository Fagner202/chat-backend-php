<?php
require_once 'vendor/autoload.php';
require_once 'config/Database.php'; 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use src\Database;

try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

// Funcao para validar o token JWT
function validateToken() {
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

    $key = "your_secret_key"; // Defina uma chave secreta segura
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        die(json_encode(['message' => 'Token invalido: ' . $e->getMessage()]));
    }
}

// Protege rotas GET, PUT e DELETE com o token JWT
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    validateToken();
}

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obter todos os usuarios
    $stmt = $pdo->query("SELECT id, name, email FROM users");
    $users = $stmt->fetchAll();
    echo json_encode($users);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Esta rota esta desprotegida para permitir criacao de usuarios
    http_response_code(405);
    echo json_encode(['message' => 'Metodo nao permitido nesta rota']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Atualizar um usuario
    parse_str(file_get_contents("php://input"), $put_vars);
    $id = $put_vars['id'] ?? null;
    $name = $put_vars['name'] ?? null;
    $email = $put_vars['email'] ?? null;

    if (!$id || !$name || !$email) {
        http_response_code(400);
        die(json_encode(['message' => 'ID, Nome e Email sao obrigatorios']));
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);

    echo json_encode(['message' => 'Usuario atualizado com sucesso']);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deletar um usuario
    parse_str(file_get_contents("php://input"), $delete_vars);
    $id = $delete_vars['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        die(json_encode(['message' => 'ID e obrigatorio']));
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['message' => 'Usuario deletado com sucesso']);

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Metodo nao permitido']);
}