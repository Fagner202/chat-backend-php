<?php
require 'vendor/autoload.php'; // Carregar o autoloader do Composer
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ratchet\App;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;
    protected $rooms;

    public function __construct()
    {
        // Inicialização de variáveis
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        $this->rooms = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);

        if (!isset($queryParams['token'])) {
            $conn->send(json_encode(['error' => 'Token JWT ausente']));
            $conn->close();
            return;
        }

        // Validar JWT
        $token = $queryParams['token'];
        $key = "your_secret_key";

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $userId = $decoded->user_id;

            $this->clients->attach($conn);
            $this->users[$conn->resourceId] = ['user_id' => $userId, 'room_id' => null];

            $conn->send(json_encode(['type' => 'welcome', 'message' => 'Conexão estabelecida']));
            echo "Usuário $userId conectado ({$conn->resourceId})\n";

        } catch (\Exception $e) {
            $conn->send(json_encode(['error' => 'Token JWT inválido']));
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!isset($data['action'])) {
            $from->send(json_encode(['error' => 'Ação inválida']));
            return;
        }

        switch ($data['action']) {
            case 'create_room':
                $this->createRoom($from, $data['room_name']);
                break;

            case 'join_room':
                $this->joinRoom($from, $data['room_id']);
                break;

            case 'send_message':
                $this->sendMessageToRoom($from, $data['message']);
                break;

            default:
                $from->send(json_encode(['error' => 'Ação desconhecida']));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $user = $this->users[$conn->resourceId] ?? null;

        if ($user && $user['room_id']) {
            $this->broadcastToRoom($user['room_id'], [
                'type' => 'user_disconnected',
                'user_id' => $user['user_id']
            ]);
        }

        unset($this->users[$conn->resourceId]);
        $this->clients->detach($conn);

        echo "Conexão {$conn->resourceId} encerrada\n";
    }

    private function createRoom(ConnectionInterface $conn, $roomName)
    {
        // Criar a sala em memória
        $roomId = uniqid(); // Gerar um ID único para a sala
        $this->rooms[$roomId] = ['name' => $roomName, 'clients' => []];

        $conn->send(json_encode([
            'type' => 'room_created',
            'room_id' => $roomId,
            'room_name' => $roomName
        ]));
        echo "Sala criada: $roomName ($roomId)\n";
    }

    private function joinRoom(ConnectionInterface $conn, $roomId)
    {
        if (!isset($this->rooms[$roomId])) {
            $conn->send(json_encode(['error' => 'Sala não encontrada']));
            return;
        }

        $user = &$this->users[$conn->resourceId];
        $user['room_id'] = $roomId;
        $this->rooms[$roomId]['clients'][$conn->resourceId] = $conn;

        $this->broadcastToRoom($roomId, [
            'type' => 'user_joined',
            'user_id' => $user['user_id']
        ]);
    }

    private function sendMessageToRoom(ConnectionInterface $from, $message)
    {
        $user = $this->users[$from->resourceId];
        $roomId = $user['room_id'];

        if (!$roomId) {
            $from->send(json_encode(['error' => 'Você não está em uma sala']));
            return;
        }

        $this->broadcastToRoom($roomId, [
            'type' => 'chat_message',
            'user_id' => $user['user_id'],
            'message' => $message
        ]);
    }

    private function broadcastToRoom($roomId, $message)
    {
        foreach ($this->rooms[$roomId]['clients'] as $client) {
            $client->send(json_encode($message));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = new App('localhost', 8081); // Use 'new App' para criar uma instância.
$server->route('/chat', new Chat(), ['*']);
$server->run();
