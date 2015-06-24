<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PriceChange
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class PriceChange
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="priceChange")
     */
    private $car;

    /**
     * @ORM\Column(type="integer")
     */
    private $oldPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $newPrice;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;


    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * @param Car $car
     */
    public function setCar(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return Car
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $newPrice
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;
    }

    /**
     * @return mixed
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * @param mixed $oldPrice
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
    }

    /**
     * @return mixed
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }


} 