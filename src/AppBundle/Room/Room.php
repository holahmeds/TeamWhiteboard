<?php

namespace AppBundle\Room;

class Room
{
    private $users = array();

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser($user)
    {
        array_push($this->users, $user);
    }

    public function removeUser($user)
    {
        $this->users = array_diff($this->users, array($user));
    }
}