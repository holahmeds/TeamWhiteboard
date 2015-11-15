<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Room", mappedBy="creator")
     */
    protected $createdRooms;
    
    /**
     * @ManyToMany(targetEntity="AppBundle\Entity\Room", mappedBy="members")
     */
    protected $memberOfRooms;
    
    public function __construct() {
        parent::__construct();
    	$this->createdRooms = new ArrayCollection();
    	$this->memberOfRooms = new ArrayCollection();
    }

    /**
     * Add createdRoom
     *
     * @param \AppBundle\Entity\Room $createdRoom
     *
     * @return User
     */
    public function addCreatedRoom(\AppBundle\Entity\Room $createdRoom)
    {
        $this->createdRooms[] = $createdRoom;

        return $this;
    }

    /**
     * Remove createdRoom
     *
     * @param \AppBundle\Entity\Room $createdRoom
     */
    public function removeCreatedRoom(\AppBundle\Entity\Room $createdRoom)
    {
        $this->createdRooms->removeElement($createdRoom);
    }

    /**
     * Get createdRooms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreatedRooms()
    {
        return $this->createdRooms;
    }

    /**
     * Add memberOfRoom
     *
     * @param \AppBundle\Entity\Room $memberOfRoom
     *
     * @return User
     */
    public function addMemberOfRoom(\AppBundle\Entity\Room $memberOfRoom)
    {
        $this->memberOfRooms[] = $memberOfRoom;

        return $this;
    }

    /**
     * Remove memberOfRoom
     *
     * @param \AppBundle\Entity\Room $memberOfRoom
     */
    public function removeMemberOfRoom(\AppBundle\Entity\Room $memberOfRoom)
    {
        $this->memberOfRooms->removeElement($memberOfRoom);
    }

    /**
     * Get memberOfRooms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberOfRooms()
    {
        return $this->memberOfRooms;
    }
}
