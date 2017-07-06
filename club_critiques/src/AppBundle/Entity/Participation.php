<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParticipationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    public $status;

    /**
     *
     * @ORM\Column(name="room", type="integer", nullable=true)
     */
    public $room;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"merge", "persist"})
     */
    public $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Lobby", cascade={"merge", "persist"})
     */
    public $lobby;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set room
     *
     *
     * @return Participation
     */
    public function setRoom($room)
    {
        $this->room = $room;
        return $this;
    }

    public function getRoom()
    {
        return $this->room;
    }


    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getLobby()
    {
        return $this->lobby;
    }

    public function setLobby($lobby)
    {
        $this->lobby = $lobby;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status=$status;
        return $this;
    }

    /*** LIFE CYCLE EVENTS ***/

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->dateAdd = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->dateUpdate = new \DateTime();
    }
}

