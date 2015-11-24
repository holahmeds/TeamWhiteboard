<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RoomListCommand extends ContainerAwareCommand {

	protected function configure() {
		$this->setName("room:list")->addArgument('creator', InputArgument::REQUIRED, '');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$manager = $this->getContainer()->get('doctrine')->getManager();
		$creator = $manager->getRepository("AppBundle:User")->findOneByUsername($input->getArgument('creator'));
		
		if (! $creator) {
			$output->writeln("Invalid Username");
		} else {
			$rooms = $creator->getCreatedRooms();
			
			foreach ( $rooms as $room ) {
				$output->writeln($room->getId());
			}
		}
	}
}