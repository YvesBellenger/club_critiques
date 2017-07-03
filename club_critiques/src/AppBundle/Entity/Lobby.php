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
     *
     * @ORM\Column(name="max_participants", type="integer")
     */
    public $max_participants = 20;

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
     * @var string
     *
     * @ORM\Column(name="history", type="text", nullable=true)
     */
    public $history;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    public $status;

    /**
     *
     * @ORM\Column(name="date_end", type="datetime")
     */
    public $date_end;

    public function __construct()
    {
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
    public function addParticipant(\AppBundle\Entity\User $user)
    {
        if(!$this->participants->contains($user)) {
            $this->participants[] = $user;
        }

        return $this;
    }

    public function checkParticipant(\AppBundle\Entity\USer $user)
    {
        return $this->participants->contains($user);
    }

    /**
     * Remove participant
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeParticipant(\AppBundle\Entity\User $user)
    {
        $this->participants->removeElement($user);
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

    /**
     * Get date_end
     *
     */

    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set date_end
     *
     */

    public function setDateEnd($date_end)
    {
        $this->date_end = $date_end;
        return $this->date_end;
    }

    /**
     * Set max_participants
     *
     *
     * @return Lobby
     */
    public function setMaxParticipants($max_participants)
    {
        $this->max_participants = $max_participants;
        return $this;
    }

    /**
     * Get max_participants
     *
     */

    public function getMaxParticipant()
    {
        return $this->max_participants;
    }

    /**
     * Set history
     *
     *
     * @return Lobby
     */
    public function setHistory($history)
    {
        $this->history = serialize($history);
        return $this;
    }

    /**
     * Get history
     *
     */

    public function getHistory()
    {
        return unserialize($this->history);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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

