<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\Room;

class RoomDeleteCommand extends ContainerAwareCommand {

	protected function configure() {
		$this->setName("room:delete")->addArgument('room id', InputArgument::REQUIRED, '');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$manager = $this->getContainer()->get('room_manager');
		$room = $manager->getRoomByID($input->getArgument('room id'));
		
		if (! $room) {
			$output->writeln("Invalid Room Id");
		} else {
			$manager->deleteRoom($room);
			$output->writeln("Room deleted");
		}
	}
}