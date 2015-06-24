<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Car
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Car
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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $link;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string")
     */
    private $comparisId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $comparisCreated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $carType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $valto;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modified;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $performance;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $benzin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $co2;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    /**
     * @ORM\OneToMany(targetEntity="PriceChange", mappedBy="car")
     */
    private $priceChange;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->priceChange = new ArrayCollection();
        $this->deleted = 0;
    }

    /**
     * @param mixed $benzin
     */
    public function setBenzin($benzin)
    {
        $this->benzin = $benzin;
    }

    /**
     * @return mixed
     */
    public function getBenzin()
    {
        return $this->benzin;
    }

    /**
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param mixed $carType
     */
    public function setCarType($carType)
    {
        $this->carType = $carType;
    }

    /**
     * @return mixed
     */
    public function getCarType()
    {
        return $this->carType;
    }

    /**
     * @param mixed $co2
     */
    public function setCo2($co2)
    {
        $this->co2 = $co2;
    }

    /**
     * @return mixed
     */
    public function getCo2()
    {
        return $this->co2;
    }

    /**
     * @param mixed $comparisCreated
     */
    public function setComparisCreated($comparisCreated)
    {
        $this->comparisCreated = $comparisCreated;
    }

    /**
     * @return mixed
     */
    public function getComparisCreated()
    {
        return $this->comparisCreated;
    }

    /**
     * @param mixed $comparisId
     */
    public function setComparisId($comparisId)
    {
        $this->comparisId = $comparisId;
    }

    /**
     * @return mixed
     */
    public function getComparisId()
    {
        return $this->comparisId;
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
     * @param mixed $km
     */
    public function setKm($km)
    {
        $this->km = $km;
    }

    /**
     * @return mixed
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $modified
     */
    public function setModified()
    {
        $this->modified = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $performance
     */
    public function setPerformance($performance)
    {
        $this->performance = $performance;
    }

    /**
     * @return mixed
     */
    public function getPerformance()
    {
        return $this->performance;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $valto
     */
    public function setValto($valto)
    {
        $this->valto = $valto;
    }

    /**
     * @return mixed
     */
    public function getValto()
    {
        return $this->valto;
    }

    public function addPriceChange(PriceChange $priceChange)
    {
        $this->priceChange->add($priceChange);
    }

    public function setPriceChange($priceChange)
    {
        $this->priceChange = $priceChange;
    }

    public function getPriceChange()
    {
        return $this->priceChange;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }


}