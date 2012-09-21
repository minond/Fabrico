<?php

require '/var/www/Dropbox/apache/Ratchet/vendor/autoload.php';

use Ratchet\Wamp\WampServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\ConnectionInterface;

class FabricoPubSub implements Ratchet\Wamp\WampServerInterface {
	public function onPublish (ConnectionInterface $conn, $topic, $event, array $exclude, array $eligable) {
		$topic->broadcast($event);
	}

	public function onCall (ConnectionInterface $conn, $id, $topic, array $params) {
		$conn->callError($id, $topic, 'RPC not supported');
	}

	public function onSubscribe(ConnectionInterface $conn, $topic) {}
	public function onUnSubscribe(ConnectionInterface $conn, $topic) {}
	public function onOpen (ConnectionInterface $conn) {}
	public function onClose (ConnectionInterface $conn) {}
	public function onError (ConnectionInterface $conn, Exception $error) {}
}

$server = IoServer::factory(
	new WsServer(
		new WampServer(
			new FabricoPubSub
		)
	), 9000
);

$server->run();
