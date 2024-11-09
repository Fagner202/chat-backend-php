<?php
namespace src\models;

use src\Database;

class User
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function createUser($name, $email, $password)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
            return json_encode(['message' => 'Usuário criado com sucesso']);
        } catch (\PDOException $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro ao inserir usuário: ' . $e->getMessage()]);
        }
    }
}
