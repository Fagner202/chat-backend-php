<?php

namespace src;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $connection = null;

    // Constructor para inicializar o ambiente
    public function __construct($environment = 'development')
    {
        if ($environment === 'production') {
            // Configurações para o ambiente de produção
            $this->host = 'sql10.freemysqlhosting.net';
            $this->dbname = 'sql10743789';
            $this->username = 'sql10743789';
            $this->password = 'YacGutjHpg';
        } else {
            // Configurações para o ambiente de desenvolvimento
            $this->host = 'mysql'; // Nome do serviço no Docker ou localhost
            $this->dbname = 'mydb';
            $this->username = 'user';
            $this->password = 'password';
        }
    }

    // Método que cria e retorna a conexão com o banco de dados
    public function getConnection()
    {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname}",
                    $this->username,
                    $this->password
                );

                // Configurações adicionais para melhor controle de erros
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                echo "Erro ao conectar com o banco de dados: " . $e->getMessage();
                exit;
            }
        }

        return $this->connection;
    }
}