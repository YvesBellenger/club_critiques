<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BlockContent
 *
 * @ORM\Table(name="block_content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlockContentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BlockContent
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */

    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    protected $code;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content", cascade={"persist"})
     * @ORM\JoinTable(name="content_block",
     *      joinColumns={@ORM\JoinColumn(name="block_content_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")}
     * )
     */
    private $contents;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

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


    public function __construct()
    {
        $this->contents = new ArrayCollection();
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
     * Set title
     *
     *
     * @return BlockContent
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     */

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set code
     *
     *
     * @return BlockContent
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     */

    public function getCode()
    {
        return $this->code;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * Set dateAdd
     *
     *
     * @return BlockContent
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
     * @return BlockContent
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
}

