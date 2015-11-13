<?php

namespace AppBundle\WebSocketServer;

use Firebase\JWT\JWT;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MessageHandler implements MessageComponentInterface {
    private $container;
    private $connections = array();

    public function __construct($cont) {
        $this->container = $cont;
    }

    public function onOpen(ConnectionInterface $conn) {
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg, true);

        if ($msg['type'] == "authenticate") {
            $decoded_jwt = (array) JWT::decode($msg['jwt'], $this->container->getParameter('jwt_secret'), array('HS256'));


            $room = $this->container->get('room_manager')->getRoom($decoded_jwt['roomID']);

            // TODO remove test
            if (($room != null && in_array($decoded_jwt['user'], $room->getUsers())) || $decoded_jwt['roomID'] == 'test') {
                if (!array_key_exists($decoded_jwt['roomID'], $this->connections)) {
                    $this->connections[$decoded_jwt['roomID']] = array();
                }

                $color = '#' . dechex(rand(0, 2**24 - 1));

                $this->connections[$decoded_jwt['roomID']][] = array(
                    'user' => $decoded_jwt['user'],
                    'conn' => $from,
                    'color' => $color
                );

                $from->send(json_encode(array(
                    'type' => 'set color',
                    'color' => $color
                )));

                echo "$decoded_jwt[user] connected\n";
            }
        }


    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}