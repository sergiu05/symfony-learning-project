<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orderdetail
 *
 * @ORM\Table(name="orderdetail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderdetailRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Orderdetail
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

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
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderdetails")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="orderdetails")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Orderdetail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price in cents
     *
     * @param integer $price
     *
     * @return Orderdetail
     */
    public function setPrice($price)
    {
        $this->price = $price * 100;

        return $this;
    }

    /**
     * Get price
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
     * Set order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return Orderdetail
     */
    public function setOrder(\AppBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \AppBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set album
     *
     * @param \AppBundle\Entity\Album $album
     *
     * @return Orderdetail
     */
    public function setAlbum(\AppBundle\Entity\Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \AppBundle\Entity\Album
     */
    public function getAlbum()
    {
        return $this->album;
    }
}
