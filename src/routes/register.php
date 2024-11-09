<?php
use src\controllers\RegisterController;
$registerController = new RegisterController();
// require_once 'vendor/autoload.php';  // Certifique-se de que o autoload está correto

$data = json_decode(file_get_contents('php://input'), true);  // Pegando os dados da requisição

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $registerController->register($data);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido']);
}
