<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Album
 *
 * @ORM\Table(name="albums")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AlbumRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Album
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     * @Assert\NotBlank()          
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $description;

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
     * @ORM\ManyToOne(targetEntity="Genre",inversedBy="albums")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity="Artist",inversedBy="albums")
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $artist;

    /**
     * @Assert\File(
     *		maxSize="5M",
	 *		mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
	 *		maxSizeMessage = "Max file size is 5mb.",
	 *		mimeTypesMessage = "Only image files are allowed."
     *		)
     * @Assert\NotBlank(groups={"Create"})
     */
    private $file;

    /**
     * @var string
     *
     * Stores the previous image path, comes in handy when deleting the previous image
     */
    private $temp;

    /**
     * @ORM\OneToMany(targetEntity="Orderdetail", mappedBy="album")     
     */
    private $orderdetails;

    public function __construct() {
    	$this->orderdetails = new ArrayCollection();
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
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set price in cents
     *
     * @param integer $price
     *
     * @return Post
     */
    public function setPrice($price)
    {
        $this->price = $price * 100;

        return $this;
    }

    /**
     * Get price in dollar
     *
     * @return int
     */
    public function getPrice()
    {
        return round($this->price/100, 2);
    }    

	/**
     * Set createdAt
     *
     * @ORM\PrePersist     
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime("now");
    	$this->updatedAt = $this->createdAt;
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
     * Set description
     *
     * @param string $description
     *
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set genre
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return Album
     */
    public function setGenre(\AppBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \AppBundle\Entity\Genre
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set artist
     *
     * @param \AppBundle\Entity\Artist $artist
     *
     * @return Album
     */
    public function setArtist(\AppBundle\Entity\Artist $artist = null)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return \AppBundle\Entity\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    public function getAbsolutePath() {
    	return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath() {
    	return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir() {
    	return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir() {
    	return 'uploads/documents';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
    	$this->file = $file;

    	if (isset($this->path)) {
    		$this->temp = $this->path;
    		$this->path = null;
    	} else {
    		$this->path = 'initial';
    	}
    }

    /**
     * Get file
	 *
     * @return UploadedFile | null
     */
    public function getFile() {
    	return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
    	if ( null !== $this->getFile()) {
    		$filename = sha1(uniqid(mt_rand(), true));
    		$this->path = $filename.'.'.$this->getFile()->guessExtension();
    	}
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
    	if (null === $this->getFile()) {
    		return;
    	}
    	$this->getFile()->move($this->getUploadRootDir(), $this->path);

    	if (isset($this->temp)) {
    		unlink($this->getUploadRootDir().'/'.$this->temp);
    		$this->temp = null;
    	}
    	$this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
    	$file = $this->getAbsolutePath();
    	if ($file) {
    		unlink($file);
    	}
    }

    /**
     * Add orderdetail
     *
     * @param \AppBundle\Entity\Orderdetail $orderdetail
     *
     * @return Order
     */
    public function addOrderdetail(\AppBundle\Entity\Orderdetail $orderdetail)
    {
        $this->orderdetails[] = $orderdetail;

        return $this;
    }

    /**
     * Remove orderdetail
     *
     * @param \AppBundle\Entity\Orderdetail $orderdetail
     */
    public function removeOrderdetail(\AppBundle\Entity\Orderdetail $orderdetail)
    {
        $this->orderdetails->removeElement($orderdetail);
    }

    /**
     * Get orderdetails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderdetails()
    {
        return $this->orderdetails;
    }

    /**
     * An album is deletable only if no orders were place for it
     *
     * @return boolean
     */
    public function isDeletable() {
    	return (0 === count($this->getOrderdetails()));
    }

}
