<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;

class RoomManager {
    private $entityManager;
    
    /**
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
    	$this->entityManager = $em;
    }
    
    /**
     * 
     * @param string $rid
     * @return Room|NULL
     */
    public function getRoomByID($rid) {
    	return $this->entityManager->getRepository("AppBundle:Room")->find($rid);
    }

    /**
     * 
     * @param User $creator
     * @return Room
     */
    public function createRoom(User $creator) {
    	$room = new Room();
    	$room->setCreator($creator);
    	
    	$this->entityManager->persist($room);
    	$this->entityManager->flush();
    	
    	return $room;
    }

    /**
     * 
     * @param Room $room
     */
    public function deleteRoom(Room $room) {
        $this->entityManager->remove($room);
        $this->entityManager->flush();
    }
}