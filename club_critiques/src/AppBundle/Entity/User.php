<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    const CONTENT_TO_SHARE = 1;
    const CONTENT_WANTED = 2;
    const CONTENT_SHARED = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    public $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    public $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    public $description;

    /**
     * @var string
     *
     * @ORM\Column(name="nb_reports", type="integer", nullable=true)
     */
    public $nbReports;

    /**
     * @var string
     *
     * @ORM\Column(name="last_ip", type="string", nullable=true)
     */
    public $lastIp;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="user_contacts",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")}
     * )
     */
    public $contacts;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content", cascade={"persist"})
     * @ORM\JoinTable(name="user_contents_share",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")}
     * )
     */
    public $contentsToShare;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content", cascade={"persist"})
     * @ORM\JoinTable(name="user_contents_wanted",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")}
     * )
     */
    public $contentsWanted;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content", cascade={"persist"})
     * @ORM\JoinTable(name="user_contents_shared",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")}
     * )
     */
    public $contentsShared;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    public $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_update", type="datetime", nullable=true)
     */
    public $dateUpdate;

    /**
     *
     * @ORM\Column(name="status", type="boolean")
     */
    public $status;

    public function __construct()
    {
        parent::__construct();
        $this->contacts = new ArrayCollection();
        $this->contentsToShare = new ArrayCollection();
        $this->contentsWanted = new ArrayCollection();
        $this->contentsShared = new ArrayCollection();
        $this->status = 1;
    }

    public function getId() {
        return $this->id;
    }

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
     * Set lastIp
     *
     *
     * @return User
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;
        return $this;
    }

    /**
     * Get lastIp
     *
     */

    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * Set description
     *
     * $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nbReports
     *
     * @return User
     */
    public function setNbReports($nbReports)
    {
        $this->nbReports = $nbReports;

        return $this;
    }

    /**
     * Get nbReports
     *
     */
    public function getNbReports()
    {
        return $this->nbReports;
    }

    public static function IncreaseReportNumber($user) {
        if ($user->nbReports == null) {
            $user->nbReports = 1;
        } else {
            $user->nbReports = $user->nbReports + 1;
        }
        return $user;
    }

    /**
     * Set firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     */
    public function getLastName()
    {
        return $this->lastName;
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
     * Get contacts
     *
     * @return User
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add contacts
     *
     * @param \AppBundle\Entity\User $contact
     * @return User
     */
    public function addContact(\AppBundle\Entity\User $contact)
    {
        if(!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
        }

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \AppBundle\Entity\User $contact
     */
    public function removeContact(\AppBundle\Entity\User $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contentsToShare
     *
     * @return ArrayCollection
     */
    public function getContentsToShare()
    {
        return $this->contentsToShare;
    }

    /**
     * Add contentToShare
     *
     * @param \AppBundle\Entity\Content $content
     * @return User
     */
    public function addContentToShare(\AppBundle\Entity\Content $content)
    {
        if(!$this->contentsToShare->contains($content)) {
            $this->contentsToShare[] = $content;
        }

        return $this;
    }

    /**
     * Remove contentToShare
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContentToShare(\AppBundle\Entity\Content $content)
    {
        $this->contentsToShare->removeElement($content);
    }

    /**
     * Get contentsShared
     *
     * @return ArrayCollection
     */
    public function getContentsShared()
    {
        return $this->contentsShared;
    }

    /**
     * Add contentShared
     *
     * @param \AppBundle\Entity\Content $content
     * @return User
     */
    public function addContentShared(\AppBundle\Entity\Content $content)
    {
        if(!$this->contentsShared->contains($content)) {
            $this->contentsShared[] = $content;
        }

        return $this;
    }

    /**
     * Remove contentShared
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContentShared(\AppBundle\Entity\Content $content)
    {
        $this->contentsShared->removeElement($content);
    }

    /**
     * Get contentsWanted
     *
     * @return ArrayCollection
     */
    public function getContentsWanted()
    {
        return $this->contentsWanted;
    }

    /**
     * Add contentWanted
     *
     * @param \AppBundle\Entity\Content $content
     * @return User
     */
    public function addContentWanted(\AppBundle\Entity\Content $content)
    {
        if(!$this->contentsWanted->contains($content)) {
            $this->contentsWanted[] = $content;
        }

        return $this;
    }

    /**
     * Remove contentWanted
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContentWanted(\AppBundle\Entity\Content $content)
    {
        $this->contentsWanted->removeElement($content);
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