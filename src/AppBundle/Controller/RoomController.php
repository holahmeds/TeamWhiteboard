<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use Firebase\JWT\JWT;

class RoomController extends Controller {

	/**
	 * @Route("/room/{id}", name="room")
	 */
	public function room($id) {
		$manager = $this->get('room_manager');
		$room = $manager->getRoomByID($id);
		
		if (! $room || ! ($this->getUser() == $room->getCreator() || $room->getMembers()->contains($this->getUser()))) {
			$this->addFlash('error', 'Room either does not exist or you do not have access to it');
			return $this->redirectToRoute('user_rooms');
		}
		
		$payload = array(
				'user' => $this->getUser()->getUsername(),
				'roomID' => $id 
		);
		
		return $this->render('room/room.html.twig', array(
				'room' => $room,
				'jwt' => JWT::encode($payload, $this->getParameter('jwt_secret')) 
		));
	}

	/**
	 * @Route("/my-rooms", name="user_rooms")
	 */
	public function myRooms() {
		return $this->render('room/user_rooms.html.twig');
	}

	/**
	 * @Route("/create-room", name="create_room")
	 */
	public function createRoom() {
		$manager = $this->get('room_manager');
		$logger = $this->get('logger');
		
		$rid = $manager->createRoom($this->getUser())->getId();
		
		$logger->info("Created room $rid");
		
		return $this->redirectToRoute('user_rooms');
	}

	/**
	 * @Route("/delete-room/{rid}", name="delete_room")
	 */
	public function deleteRoom($rid) {
		$manager = $this->get('room_manager');
		
		$room = $manager->getRoomByID($rid);
		
		if (! $room || $room->getCreator() != $this->getUser()) {
			$this->addFlash('error', 'This room either does not exist or is not created by you.');
		} else {
			$manager->deleteRoom($room);
		}
		
		return $this->redirectToRoute('user_rooms');
	}

	/**
	 * @Route("/add-user/{rid}/{username}", name="add_user_to_room")
	 */
	public function addUserToRoom($rid, $username) {
		$manager = $this->get('room_manager');
		$entityManager = $this->getDoctrine()->getManager();
		
		$room = $manager->getRoomByID($rid);
		$user = $entityManager->getRepository('AppBundle:User')->findOneByUsername($username);
		
		if (! $room || $room->getCreator() != $this->getUser()) {
			$this->addFlash('error', 'This room either does not exist or is not created by you.');
		} else if (! $user) {
			$this->addFlash('error', 'This user does not exist');
		} else if ($room->getMembers()->contains($user)) {
			$this->addFlash('error', 'User is already a member of the room');
		} else if ($user == $room->getCreator()) {
			$this->addFlash('error', 'You are the creator. You do not need to explicitly add yourself as a member');
		} else {
			$room->addMember($user);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('user_rooms');
	}
}
