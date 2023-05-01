// server.php
require 'vendor/autoload.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use YourGameNamespace\WebSocketController;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketController()
        )
    ),
    8080
);

$server->run();
