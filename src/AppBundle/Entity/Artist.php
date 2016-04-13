<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Artist
 *
 * @ORM\Table(name="artist")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArtistRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Artist
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
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;    

    /**
     * @ORM\OneToMany(targetEntity="Album",mappedBy="artist")
     */
    private $albums;

    public function __construct() {
    	$this->albums = new ArrayCollection;
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
     * @return Artist
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

	/**
     * Set createdAt
     *
     * @ORM\PrePersist     
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime("now");
    	$this->updated_at = $this->created_at;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate     
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime("now");        
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add album
     *
     * @param \AppBundle\Entity\Album $album
     *
     * @return Artist
     */
    public function addAlbum(\AppBundle\Entity\Album $album)
    {
        $this->albums[] = $album;

        return $this;
    }

    /**
     * Remove album
     *
     * @param \AppBundle\Entity\Album $album
     */
    public function removeAlbum(\AppBundle\Entity\Album $album)
    {
        $this->albums->removeElement($album);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlbums()
    {
        return $this->albums;
    }
}
