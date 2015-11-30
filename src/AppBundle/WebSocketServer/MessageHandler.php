<?php

namespace AppBundle\WebSocketServer;

use Firebase\JWT\JWT;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MessageHandler implements MessageComponentInterface {
	private $container;
	private $roomUsers;
	private $connectionRoom;

	public function __construct($cont) {
		$this->container = $cont;
		$this->roomUsers = array();
		$this->connectionRoom = new \SplObjectStorage();
	}

	public function onOpen(ConnectionInterface $conn) {
	}

	public function onMessage(ConnectionInterface $from, $msgJSON) {
		$msg = json_decode($msgJSON, true);
		
		switch ($msg['type']) {
			case 'authenticate' :
				$decoded_jwt = (array) JWT::decode($msg['jwt'], $this->container->getParameter('jwt_secret'), array(
						'HS256' 
				));
				
				$manager = $this->container->get('doctrine')->getManager();
				
				$user = $manager->getRepository('AppBundle:User')->findOneByUsername($decoded_jwt['user']);
				$room = $this->container->get('room_manager')->getRoomByID($decoded_jwt['roomID']);
				
				echo "Authenticating new user " . $user->getUsername() . "\n";
				echo "Users in $decoded_jwt[roomID]\n";
				foreach ( $room->getMembers() as $user ) {
					echo "	" . $user->getUsername() . "\n";
				}
				
				if ($room->getMembers()->contains($user) || $user == $room->getCreator()) {
					$this->connectionRoom->attach($from, $decoded_jwt['roomID']);
					
					if (! array_key_exists($decoded_jwt['roomID'], $this->roomUsers)) {
						$this->roomUsers[$decoded_jwt['roomID']] = new \SplObjectStorage();
					}
					
					$this->roomUsers[$decoded_jwt['roomID']]->attach($from, array(
							'user' => $decoded_jwt['user'],
							'color' => '#000000' 
					));
					
					echo "User $decoded_jwt[user] connected to $decoded_jwt[roomID]\n";
				} else {
					echo "Authorisation failed for $decoded_jwt[user] connecting to $decoded_jwt[roomID]\n";
				}
				
				break;
			case 'stroke' :
			case 'image' :
				$roomID = $this->connectionRoom[$from];
				$this->sendToEach($this->roomUsers[$roomID], $msgJSON, $from);
				break;
			case 'chat' :
				$roomID = $this->connectionRoom[$from];
				$username = $this->roomUsers[$roomID][$from]['user'];
				$msg['user'] = $username;
				$this->sendToEach($this->roomUsers[$roomID], json_encode($msg), $from);
				break;
		}
	}

	public function onClose(ConnectionInterface $conn) {
		$roomID = $this->connectionRoom[$conn];
		
		unset($this->roomUsers[$roomID]);
		$this->connectionRoom->detach($conn);
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		$roomID = $this->connectionRoom[$conn];
		$user = $this->roomUsers[$roomID][$conn]['user'];
		
		echo "Exception from user $user\n";
		echo $e->getTraceAsString();
	}

	/**
	 * Sends a message to each of the specified ConnectionInterfaces.
	 *
	 * @param \SplObjectStorage $conns
	 *        	An object containing the ConnectionInterfaces the message is to be sent to.
	 * @param string $msg
	 *        	The message to be sent.
	 * @param ConnectionInterface $exclude
	 *        	A ConnectionInterface to be excluded from $conns when sending the message.
	 */
	private function sendToEach(\SplObjectStorage $conns, $msg, ConnectionInterface $exclude = null) {
		$conns->rewind();
		while ($conns->valid()) {
			$conn = $conns->current();
			
			if ($conn != $exclude) {
				$conn->send($msg);
			}
			
			$conns->next();
		}
	}
}
