<?php
// Conectar ao banco de dados MySQL
$host = 'mysql';
$dbname = 'mydb';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Criar um novo usuário (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        echo "Usuário criado com sucesso!";
    }

    // Listar usuários (GET)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($users);
        echo "</pre>";
    }

} catch (PDOException $e) {
    echo "Falha na conexão: " . $e->getMessage();
}
