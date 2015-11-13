<?php

namespace AppBundle\Command;

use AppBundle\WebSocketServer\MessageHandler;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartWebSocketCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this->setName("room:websocket");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new MessageHandler($this->getContainer())
            )
        ),
        3000
    );

    $server->run();
  }
}