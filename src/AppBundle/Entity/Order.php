<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DoctrineORMOrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Order
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
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Orderdetail", mappedBy="order")     
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
     * Set total in cents
     *
     * @param integer $total
     *
     * @return Orders
     */
    public function setTotal($total)
    {
        $this->total = $total * 100; /* store cost in cents */

        return $this;
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return round($this->total/100, 2);
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Order
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
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
}
