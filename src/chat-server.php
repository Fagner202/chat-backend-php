<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ratchet\App;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);

        if (!isset($queryParams['token'])) {
            $conn->send(json_encode(['error' => 'Token JWT ausente']));
            $conn->close();
            return;
        }

        $token = $queryParams['token'];
        $key = "your_secret_key";

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $userId = $decoded->user_id;

            $this->clients->attach($conn);
            $this->users[$conn->resourceId] = $userId;

            // Informar aos outros clientes que um novo usuário entrou
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'type' => 'user_connected',
                    'user_id' => $userId
                ]));
            }

            echo "Usuário $userId conectado ({$conn->resourceId})\n";

        } catch (\Exception $e) {
            $conn->send(json_encode(['error' => 'Token JWT inválido']));
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $userId = $this->users[$from->resourceId] ?? 'Anônimo';
        $data = json_decode($msg, true);

        $message = [
            'type' => 'chat_message',
            'user_id' => $userId,
            'message' => $data['message'] ?? ''
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $userId = $this->users[$conn->resourceId] ?? 'Anônimo';
        unset($this->users[$conn->resourceId]);
        $this->clients->detach($conn);

        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'user_disconnected',
                'user_id' => $userId
            ]));
        }

        echo "Usuário $userId desconectado ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = new App('localhost', 8081); // Use 'new App' para criar uma instância.
$server->route('/chat', new Chat(), ['*']);
$server->run();
