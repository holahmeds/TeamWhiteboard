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

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg, true);

        if ($msg['type'] == "authenticate") {
            $decoded_jwt = (array) JWT::decode($msg['jwt'], $this->container->getParameter('jwt_secret'), array('HS256'));
            
            $manager = $this->container->get('doctrine')->getManager();
            
            $user = $manager->getRepository('AppBundle:User')->findOneByUsername($decoded_jwt['user']);
            $room = $this->container->get('room_manager')->getRoomByID($decoded_jwt['roomID']);
            
            echo "Authenticating new user " . $user->getUsername() . "\n";
            echo "Users in $decoded_jwt[roomID]\n";
            foreach ($room->getMembers() as $user) {
            	echo "	" . $user->getUsername() . "\n";
            }

            if ($room->getMembers()->contains($user) || $user == $room->getCreator()) {
            	$this->connectionRoom->attach($from, $decoded_jwt['roomID']);
            	
                if (!array_key_exists($decoded_jwt['roomID'], $this->roomUsers)) {
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
        } else if ($msg['type'] == 'stroke') {
			$roomID = $this->connectionRoom[$from];
			$roomConns = $this->roomUsers[$roomID];
			
			$color = $roomConns[$from]['color'];
			
			$roomConns->rewind();
			while ($roomConns->valid()) {
				$conn = $roomConns->current();
				
				if ($conn != $from) {
					$conn->send(json_encode(array(
							'type' => 'stroke',
							'color' => $color,
							'x1' => $msg['x1'],
							'y1' => $msg['y1'],
							'x2' => $msg['x2'],
							'y2' => $msg['y2'],
					)));
				}
				
				$roomConns->next();
			}
		} else if ($msg['type'] == 'set color') {
			$roomID = $this->connectionRoom[$from];
			$userData = $this->roomUsers[$roomID]->offsetGet($from);
			$userData['color'] = $msg['color'];
			$this->roomUsers[$roomID]->offsetSet($from, $userData);
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
}
