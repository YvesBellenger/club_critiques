<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CMSRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CMS
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    public $code;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $status;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    public $position;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    public $column_footer;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean")
     */
    public $footer;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean")
     */
    public $nav;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    public $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    public $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime", nullable=true)
     */
    public $dateUpdate;

    const COLUMN_ONE = 1;
    const COLUMN_TWO = 2;
    const COLUMN_THREE = 3;

    public static $columns = array("Colonne 1" => self::COLUMN_ONE,
                                    "Colonne 2" => self::COLUMN_TWO ,
                                    "Colonne 3"=> self::COLUMN_THREE);

    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
 * @return int
 */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return PAge
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnFooter()
    {
        return $this->column_footer;
    }

    /**
     * @return CMS
     */
    public function setColumnFooter($column_footer)
    {
        $this->column_footer = $column_footer;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
        return $this;
    }

    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    public function getDateUpdated()
    {
        return $this->dateUpdate;
    }

    public function setDateUpdated($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    public function getNav()
    {
        return $this->nav;
    }

    public function setNav($nav)
    {
        $this->nav = $nav;
    }

    public function __toString()
    {
        return ''.$this->code;
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
