<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    public const TITLES = ["Mr.","Mrs.","Ms."];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="date")
     *
     * age restriction
     * @Assert\LessThanOrEqual("-18 years", message="An age restriction applies to register on this site. You must be 18 years old or over to enregister. ")
     * Une restriction d’âge s’applique  pour s'inscrire sur ce site.
     */
    private $birthday;

    /**
     * @ORM\OneToMany(targetEntity=CustomerAddress::class, mappedBy="customer", orphanRemoval=true)
     */
    private $addresses;

     /**
     * @ORM\OneToOne(targetEntity=CustomerAddress::class)
      * @ORM\JoinColumn(name="main_address_id")
     */
    private $mainAddress;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="customer")
     * ORM\JoinColumn(nullable=true)
     */
    private $user;

    public function __construct(?User $user)
    {
        if($user) {
            $this->setUser($user);
        }
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }


    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection|CustomerAddress[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }


//    public function setAddress(?CustomerAddress $address=null): self
//    {
//        if($address) {
//            $this->addAddress($address);
//        }
//    }


    public function addAddress(CustomerAddress $address): self
    {
        if(!$this->mainAddress){
            $this->mainAddress = $address;
        }
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setCustomer($this);
        }

        return $this;
    }

    public function removeAddress(CustomerAddress $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getCustomer() === $this) {
                $address->setCustomer(null);
            }
        }

        return $this;
    }

    public function getMainAddress(): ?CustomerAddress
    {
        return $this->mainAddress;
    }

    public function setMainAddress(?CustomerAddress $mainAddress): self
    {
        $mainAddress->setCustomer($this);
        $this->addAddress($mainAddress);
        $this->mainAddress = $mainAddress;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        if($user) {
            $this->firstName = $user->getFirstName();
            $this->lastName = $user->getLastName();
            $this->birthday = $user->getBirthday();
        }

        return $this;
    }

}
