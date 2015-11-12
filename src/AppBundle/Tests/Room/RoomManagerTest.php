<?php

namespace AppBundle\Tests\Room;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomManagerTest extends WebTestCase
{
    public function testCreateRoom()
    {
        $manager = static::createClient()->getContainer()->get('room_manager');
        $rid = $manager->createRoom('holahmeds');

        $this->assertEquals(array('holahmeds'), $manager->getRoom($rid)->getUsers());
    }
}