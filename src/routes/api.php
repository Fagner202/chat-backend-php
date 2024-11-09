<?php
use src\controllers\AuthController;

require_once 'vendor/autoload.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/login') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $response = $authController->login($email, $password);
    echo json_encode($response);
    exit;
}

http_response_code(404);
echo json_encode(['message' => 'Rota nao encontrada']);
