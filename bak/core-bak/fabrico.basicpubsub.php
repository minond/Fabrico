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

	public function onSubscribe(ConnectionInterface $conn, $topic) {
		echo "subscription requested: {$topic}\n";
	}

	public function onUnSubscribe(ConnectionInterface $conn, $topic) {
		echo "unsubscription requested: {$topic}\n";
	}

	public function onOpen (ConnectionInterface $conn) {
		echo "socket opened\n";
	}

	public function onClose (ConnectionInterface $conn) {
		echo "socket closed\n";
	}

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
