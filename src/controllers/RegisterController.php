<?php
namespace src\controllers;

require_once 'vendor/autoload.php';
require_once 'config/Database.php';

use src\Database;

class RegisterController
{
    /**
     * Método para registrar um novo usuário.
     * 
     * @param array $data Dados do usuário a ser registrado.
     * @return string Mensagem de sucesso ou erro em formato JSON.
     */
    public function register($data)
    {
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

        $database = new Database('production');
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