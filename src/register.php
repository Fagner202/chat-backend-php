<?php
require_once 'vendor/autoload.php';  // Certifique-se de que o autoload está correto
require_once 'Database.php';         // Inclui a classe Database

use src\Database;

try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$name || !$email || !$password) {
        http_response_code(400);
        die(json_encode(['message' => 'Todos os campos são obrigatórios']));
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        echo json_encode(['message' => 'Usuario criado com sucesso']);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['message' => 'Erro ao inserir usuario: ' . $e->getMessage()]));
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Metodo nao permitido']);
}
