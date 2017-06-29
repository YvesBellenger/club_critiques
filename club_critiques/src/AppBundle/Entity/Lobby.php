<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="lobby_user",
     *      joinColumns={@ORM\JoinColumn(name="lobby_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    public $participants;

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

    public function __construct()
    {
        parent::__construct();
        $this->participants = new ArrayCollection();
    }

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
     * Get participants
     *
     * @return ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Add participant
     *
     * @param \AppBundle\Entity\Content $content
     * @return User
     */
    public function addParticipant(\AppBundle\Entity\Content $content)
    {
        if(!$this->participants->contains($content)) {
            $this->participants[] = $content;
        }

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeParticipant(\AppBundle\Entity\Content $content)
    {
        $this->participants->removeElement($content);
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

