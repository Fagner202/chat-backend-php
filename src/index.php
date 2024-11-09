<?php
// Incluindo o arquivo Database.php corretamente
require_once 'Database.php';  // Sai de src/ para acessar Database.php

// Conectando diretamente ao banco de dados
try {
    // Cria uma instância da classe Database
    $database = new src\Database();

    // Testa a conexão
    $pdo = $database->getConnection();
    
    // Se a conexão for bem-sucedida, imprime uma mensagem
    echo "Conexão bem-sucedida ao MySQL utilizando a classe Database!";
} catch (PDOException $e) {
    // Se ocorrer um erro ao conectar, exibe a mensagem de erro
    echo "Falha na conexão: " . $e->getMessage();
}
