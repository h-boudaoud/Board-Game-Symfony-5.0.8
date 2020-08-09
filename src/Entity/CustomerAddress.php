<?php

namespace App\Entity;

use App\Repository\CustomerAddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CustomerAddressRepository::class)
 */
class CustomerAddress
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $deliveryAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $streetAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $streetAddressLine2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;
    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDeliveryAt(): ?string
    {
        return $this->deliveryAt;
    }

    public function setDeliveryAt(string $deliveryAt): self
    {
        $this->deliveryAt = $deliveryAt;

        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function setStreetAddress(string $streetAddress): self
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    public function getStreetAddressLine2(): ?string
    {
        return $this->streetAddressLine2;
    }

    public function setStreetAddressLine2(?string $streetAddressLine2): self
    {
        $this->streetAddressLine2 = $streetAddressLine2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->deliveryAt =$this->deliveryAt
            ? $this->deliveryAt
            : $customer->getTitle().' '.$customer->getFirstName().' '.$customer->getLastName();

        $this->customer = $customer;

        return $this;
    }
    public function toString(){
        return $this->deliveryAt . '. '.
            $this->streetAddress. '. '.
            ($this->streetAddressLine2?$this->streetAddressLine2. '. ':'').
//            $this->postal. ', '.$this->city. '. '.
            ($this->postal? $this->postal. ', '.$this->city   : $this->city). '. '.
            ($this->state ? $this->state. ', '.$this->country : $this->country)
            ;
    }
}
