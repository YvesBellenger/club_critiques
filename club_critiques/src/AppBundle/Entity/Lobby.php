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
     * @var date
     *
     * @ORM\Column(name="date_start", type="datetime")
     */
    public $date_start;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Content", cascade={"merge", "persist"})
     */
    public $content;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    public $status;


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
     * Set date_start
     *
     *
     * @return Lobby
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;
        return $this;
    }

    /**
     * Get date_start
     *
     */

    public function getDateStart()
    {
        return $this->date_start;
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

