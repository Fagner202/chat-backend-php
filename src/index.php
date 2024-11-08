<?php
// Conectar ao banco de dados MySQL
$host = 'mysql'; // Nome do serviço no docker-compose
$dbname = 'mydb';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Conexão bem-sucedida ao MySQL!";
} catch (PDOException $e) {
    echo "Falha na conexão: " . $e->getMessage();
}
