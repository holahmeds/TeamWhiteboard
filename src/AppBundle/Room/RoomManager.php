<?php

namespace AppBundle\Room;

class RoomManager
{
    private $rooms = array();

    public function createRoom($creator)
    {
        $r = new Room();
        $r->addUser($creator);

        $rid = dechex(rand(65536, 1048576));

        $this->rooms[$rid] = $r;
        return $rid;
    }

    public function getRoom($rid)
    {
        return (array_key_exists($rid, $this->rooms)) ? $this->rooms[$rid] : null;
    }

    public function deleteRoom($rid)
    {
        unset($this->rooms[$rid]);
    }
}