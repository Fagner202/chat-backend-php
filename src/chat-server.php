<?php
require __DIR__ . '/../vendor/autoload.php'; // Ajuste o caminho conforme necessário
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ratchet\App;

    // 1.
    // {
    // "action": "create_room",
    // "room_name": "Sala de Teste"
    // }

    // 2.
    // {
    // "action": "join_room",
    // "room_id": "unique_room_id"
    // }

    // 3.
    // {
    // "action": "send_message",
    // "message": "Olá, sala!"
    // }

    // 4.
    // {
    // "type": "user_disconnected",
    // "user_id": "user_id"
    // }

/**
 * Classe Chat que implementa a interface MessageComponentInterface do Ratchet.
 */
class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;
    protected $rooms;

    /**
     * Construtor da classe Chat.
     * Inicializa as variáveis de clientes, usuários e salas.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        $this->rooms = [];
    }

    /**
     * Método chamado quando uma nova conexão é aberta.
     * 
     * @param ConnectionInterface $conn A nova conexão.
     */
    public function onOpen(ConnectionInterface $conn)
    {
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
            $this->users[$conn->resourceId] = ['user_id' => $userId, 'room_id' => null];

            $conn->send(json_encode(['type' => 'welcome', 'message' => 'Conexão estabelecida']));
            echo "Usuário $userId conectado ({$conn->resourceId})\n";

        } catch (\Exception $e) {
            $conn->send(json_encode(['error' => 'Token JWT inválido']));
            $conn->close();
        }
    }

    /**
     * Método chamado quando uma mensagem é recebida.
     * 
     * @param ConnectionInterface $from A conexão que enviou a mensagem.
     * @param string $msg A mensagem recebida.
     */
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

    /**
     * Método chamado quando uma conexão é fechada.
     * 
     * @param ConnectionInterface $conn A conexão que foi fechada.
     */
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

    /**
     * Método chamado quando ocorre um erro na conexão.
     * 
     * @param ConnectionInterface $conn A conexão onde ocorreu o erro.
     * @param \Exception $e A exceção lançada.
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Cria uma nova sala de chat.
     * 
     * @param ConnectionInterface $conn A conexão que solicitou a criação da sala.
     * @param string $roomName O nome da sala a ser criada.
     */
    private function createRoom(ConnectionInterface $conn, $roomName)
    {
        $roomId = uniqid(); // Gerar um ID único para a sala
        $this->rooms[$roomId] = ['name' => $roomName, 'clients' => []];

        $conn->send(json_encode([
            'type' => 'room_created',
            'room_id' => $roomId,
            'room_name' => $roomName
        ]));
        echo "Sala criada: $roomName ($roomId)\n";
    }

    /**
     * Adiciona um usuário a uma sala de chat.
     * 
     * @param ConnectionInterface $conn A conexão do usuário.
     * @param string $roomId O ID da sala.
     */
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

    /**
     * Envia uma mensagem para todos os usuários de uma sala.
     * 
     * @param ConnectionInterface $from A conexão que enviou a mensagem.
     * @param string $message A mensagem a ser enviada.
     */
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

    /**
     * Envia uma mensagem para todos os clientes de uma sala.
     * 
     * @param string $roomId O ID da sala.
     * @param array $message A mensagem a ser enviada.
     */
    private function broadcastToRoom($roomId, $message)
    {
        foreach ($this->rooms[$roomId]['clients'] as $client) {
            $client->send(json_encode($message));
        }
    }
}

$server = new App('localhost', 8081); // Use 'new App' para criar uma instância.
$server->route('/chat', new Chat(), ['*']);
$server->run();