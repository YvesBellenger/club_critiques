<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Author
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Author
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    protected $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_update", type="datetime", nullable=true)
     */
    protected $dateUpdate;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * Set dateAdd
     *
     *
     * @return User
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
        return $this;
    }

    /**
     * Get dateAdd
     *
     */

    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set data_update
     *
     *
     * @return User
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    /**
     * Get dateUpdate
     *
     */

    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }


    /**
     * Set status
     *
     * @param string $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */

    public function getStatus()
    {
        return $this->status;
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
     * Set name
     *
     * @param string $name
     *
     * @return Author
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

