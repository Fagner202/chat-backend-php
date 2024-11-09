<?php
namespace src\controllers;

require_once 'vendor/autoload.php';

use src\models\User;

class RegisterController
{
    public function register($data)
    {
        // var_dump(User::class); // Exibe o nome completo da classe com namespace
        // die(); // Interrompe a execução para inspecionar o valor


        // Verifica se a classe User existe
        if (!class_exists(User::class)) {
            http_response_code(500);
            return json_encode(['message' => 'Model User nao encontrado']);
        }

        // Instancia a classe User
        $user = new User();

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Validações
        if (!$name || !$email || !$password) {
            http_response_code(400);
            return json_encode(['message' => 'Todos os campos devem ser preenchidos']);
        }

        // Criptografa a senha
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Chama o método para criar o usuário
        return $user->createUser($name, $email, $hashedPassword);
    }
}
