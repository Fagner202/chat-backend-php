<?php

namespace src\controllers;

use PDO;

class UserController {
    private $pdo;

    /**
     * Construtor da classe UserController.
     * Inicializa a conexão com o banco de dados.
     */
    public function __construct() {
        $database = new \src\Database('production');
        $this->pdo = $database->getConnection();
    }

    /**
     * Método para obter todos os usuários.
     * 
     * @return array Lista de usuários.
     */
    public function getUsers() {
        $stmt = $this->pdo->query("SELECT id, name, email FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para atualizar um usuário.
     * 
     * @param array $data Dados do usuário a serem atualizados.
     * @return array Mensagem de sucesso ou erro.
     */
    public function updateUser($data) {
        if (empty($data['id']) || empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            return ['message' => 'ID, nome e email sao obrigatorios'];
        }

        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['email'], $data['id']]);

        return ['message' => 'Usuario atualizado com sucesso'];
    }

    /**
     * Método para excluir um usuário.
     * 
     * @param array $data Dados do usuário a ser excluído.
     * @return array Mensagem de sucesso ou erro.
     */
    public function deleteUser($data) {
        if (empty($data['id'])) {
            http_response_code(400);
            return ['message' => 'ID e obrigatorio'];
        }

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$data['id']]);

        return ['message' => 'Usuario deletado com sucesso'];
    }
}