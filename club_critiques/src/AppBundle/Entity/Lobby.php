<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lobby
 *
 * @ORM\Table(name="lobby")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LobbyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Lobby
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

