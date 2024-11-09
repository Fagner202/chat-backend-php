<?php

namespace src;

use PDO;
use PDOException;

class Database
{
    private $host = 'mysql'; // Nome do serviço no Docker
    private $dbname = 'mydb';
    private $username = 'user';
    private $password = 'password';
    private $connection = null;

    // Método que cria e retorna a conexão com o banco de dados
    public function getConnection()
    {
        if ($this->connection === null) {
            try {
                // Cria a conexão PDO com o banco de dados
                $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname}",
                    $this->username,
                    $this->password
                );

                // Configurações adicionais para melhor controle de erros
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                // Caso falhe, exibe o erro e encerra o script
                echo "Erro ao conectar com o banco de dados: " . $e->getMessage();
                exit;
            }
        }

        return $this->connection;
    }
}
