<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMGenreRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Genre
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="genre")
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
     * Sets file
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
    	$this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
    	return $this->file;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Genre
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

    public function getAbsolutePath() {

    	return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath() {
    	return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir() {
    	return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir() {
    	return 'uploads/documents';
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Genre
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
     * Add album
     *
     * @param \AppBundle\Entity\Album $album
     *
     * @return Genre
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

    /**
     * A genre is deletable is it has no albums attached
     *
     * @return boolean
     */
    public function isDeletable() {
    	return 0 == count($this->getAlbums());
    }

    public function upload() {
    	if (null === $this->getFile()) {
    		return;
    	}

    	$newFilename = sha1(uniqid(mt_rand(), true)).'.'.$this->getFile()->guessExtension();

    	$this->getFile()->move(
    		$this->getUploadRootDir(),
    		$newFilename
    	);

    	$this->path = $newFilename;

    	$this->file = null;
    }
}
