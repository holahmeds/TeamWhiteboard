<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;

class RoomCreateCommand extends ContainerAwareCommand {
	protected function configure() {
    	$this->setName("room:create")
    	     ->addArgument('creator',InputArgument::REQUIRED,'');
  	}

  	protected function execute(InputInterface $input, OutputInterface $output) {
  		$manager = $this->getContainer()->get('doctrine')->getManager();
  		$creator = $manager->getRepository("AppBundle:User")->findOneByUsername($input->getArgument('creator'));
  		
  		if (!$creator) {
  			$output->writeln("Invalid username");
  		} else {
  			$roomID = $this->getContainer()->get('room_manager')->createRoom($creator)->getId();
  			$output->writeln("Room created: $roomID");
  		}
  	}
}