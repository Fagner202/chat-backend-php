<?php
require_once 'vendor/autoload.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$name || !$email || !$password) {
        http_response_code(400);
        die(json_encode(['message' => 'Todos os campos são obrigatórios']));
    }

    // Criptografa a senha
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insere o usuário no banco
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);

    echo json_encode(['message' => 'Usuário criado com sucesso']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido']);
}
