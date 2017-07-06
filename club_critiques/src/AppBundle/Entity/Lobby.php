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
     *
     * @ORM\Column(name="max_participations", type="integer")
     */
    public $max_participations = 20;

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
     * Get participations
     *
     * @return ArrayCollection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * Add participation
     *
     * @param \AppBundle\Entity\Content $content
     * @return User
     */
    public function addParticipation(\AppBundle\Entity\Participation $participation)
    {
        if(!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
        }

        return $this;        // Répartition des participants dans les salles en fonction de leurs notes pour avoir une salle ayant des avis différents
        $notes = $this->getDoctrine()->getRepository('AppBundle:Note')->findByUser($user_ids);
        $repartition = array();
        $nb_user_per_room = count($notes)/$nb_rooms;
        for ($i = 0; $i < $nb_rooms; $i++) {
            for ($j = 0; $j <= $nb_user_per_room; $j++) {
                if ($j % 2 == 0) {
                    $repartition[$i] = $notes[$j];
                    unset($notes[$j]);
                } else {
                    $repartition[$i] = $notes[count($notes) - $j];
                    unset($notes[count($notes) - $j]);
                }
            }
            array_values($notes);
        }

        $user_note = $this->getDoctrine()->getRepository('AppBundle:Note')->findBy(array('content' => $lobby->content, 'user' => $user));
        $user_room = array_search($user_note, $repartition);
    }

    public function checkParticipation(\AppBundle\Entity\Participation $participation)
    {
        return $this->participations->contains($participation);
    }

    /**
     * Remove participation
     *
     * @param \AppBundle\Entity\Participation $participation
     */
    public function removeParticipation(\AppBundle\Entity\Participation $participation)
    {
        $this->participations->removeElement($participation);
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
     * Set max_participations
     *
     *
     * @return Lobby
     */
    public function setMaxParticipations($max_participations)
    {
        $this->max_participations = $max_participations;
        return $this;
    }

    /**
     * Get max_participations
     *
     */

    public function getMaxParticipation()
    {
        return $this->max_participations;
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

