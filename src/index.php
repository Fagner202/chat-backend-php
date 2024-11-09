<?php

require_once 'config/Database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/RegisterController.php'; // Incluindo o controlador de registro

// Captura a URL solicitada
$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestUri) {
    case '/api/login':
        // Lógica de autenticação
        $authController = new src\controllers\AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $response = $authController->login($email, $password);
            echo json_encode($response);
        }
        break;

    case '/api/register':
        // Lógica de registro de usuário
        $registerController = new src\controllers\RegisterController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true); // Pega os dados no formato JSON
            $response = $registerController->register($data); // Chama o método register no controller
            echo $response; // Retorna a resposta
        }
        break;

    default:
        // Caso nenhuma rota específica seja encontrada
        echo "Rota não encontrada.";
        break;
}
