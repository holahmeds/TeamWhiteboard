<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RoomController extends Controller
{
    /**
     * @Route("/room/{id}", name="room")
     */
    public function indexAction($id)
    {
        $context = $this->get('security.token_storage');

        return $this->render('room.php', array(
            'roomID' => $id,
            'user' => $context->getToken()->getUsername(),
            'key' => $this->getParameter('jwt_secret')
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
    public function createRoom()
    {
        $manager = $this->get('room_manager');
        $logger = $this->get('logger');

        $rid = $manager->createRoom($this->getUser())->getId();
        
        $logger->info("Created room $rid");

        return $this->redirect('/my-rooms');
    }
    
    /**
     * @Route("/delete-room/{rid}", name="delete_room")
     */
    public function deleteRoom($rid) {
    	$manager = $this->get('room_manager');
    	
    	$room = $manager->getRoomByID($rid);
    	
    	if (!$room) {
    		//TODO make error page
    	} else if ($room->getCreator() != $this->getUser()) {
    		//error
    	} else {
    		$manager->deleteRoom($room);
    		return $this->redirect('/my-rooms');
    	}
    }
}
