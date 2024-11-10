<?php
namespace src\controllers;

require_once 'vendor/autoload.php';
require_once 'config/Database.php';

use src\Database;
use src\models\User;

class RegisterController
{
    public function register($data)
    {

        // // Verifica se a classe User existe
        // if (!class_exists(User::class)) {
        //     http_response_code(500);
        //     return json_encode(['message' => 'Model User nao encontrado']);
        // }

        // Instancia a classe User
        // $user = new User();

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
        // return $user->createUser($name, $email, $hashedPassword);

        // até o presente momento não estamos conseguindo extensiar a model du User, então 
        // por hora vamos fazer a crição do usuario diretamente pela controller, vamos usar nossa classe Database
        // para conectar ao banco de dados

        $database = new Database();
        $pdo = $database->getConnection();

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);
            return json_encode(['message' => 'Usuario criado com sucesso']);
        } catch (\PDOException $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro ao inserir usuário: ' . $e->getMessage()]);
        }



    }
}
