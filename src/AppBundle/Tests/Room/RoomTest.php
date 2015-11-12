<?php

namespace AppBundle\Tests\Room;

use AppBundle\Room\Room;

class RoomTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRemoveUsers()
    {
        $room = new Room();
        $room->addUser('holahmeds');
        $room->addUser('ahmed');

        $this->assertEquals(array('holahmeds', 'ahmed'), $room->getUsers());

        $room->removeUser('ahmed');

        $this->assertEquals(array('holahmeds'), $room->getUsers());
    }
}