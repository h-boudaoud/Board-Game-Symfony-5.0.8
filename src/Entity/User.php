<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields="userName", message="This userName is already used.")
 * @UniqueEntity(fields="email", message="This email is already registered.")
 */
class User implements UserInterface
{

    public const ROLES = [
        'ROLE_SUPER_ADMIN',
        'ROLE_ADMIN',
        'ROLE_MODERATOR',
        'ROLE_OPERATOR', //operator ou packaging_operator
        'ROLE_STOREKEEPER', // storekeeper   ou inventory_manager
        'ROLE_USER_MANAGER',
        'ROLE_USER',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(
     *     pattern="/^([a-zA-Z0-9\- _\.]+)$/m",
     *     htmlPattern = "^([a-zA-Z0-9\- _\.]+)$",
     *     match=true,
     *     message="The userName is not valid"
     * )
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]([a-z0-9\- _\.]+)@([a-z0-9\- _\.]+)(\.[a-z]+)$/m",
     *     htmlPattern = "^[a-z0-9]([a-z0-9\- _\.]+)@([a-z0-9\- _\.]+)(\.[a-z]+)$",
     *     match=true,
     *     message="The email is not valid"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Password confirmation is invalid")
     * @Assert\Length(min=8, minMessage="The password is short, it must contain at least 8 characters",
     *     max="50", maxMessage="The password is long, it must contain a maximum of 50 characters"
     * )
     * @Assert\Regex(
     *     pattern="/^(((?=[.\S]*\d)(?=[.\S]*[a-z])(?=[.\S]*[A-Z])(?=[.\S]*[<>&@$#%_~¤£!§\*\(\[\)\]\/\.\|\*\-\=])).{8,})$/m",
     *     htmlPattern="/^(((?=[.\S]*\d)(?=[.\S]*[a-z])(?=[.\S]*[A-Z])(?=[.\S]*[<>&@$#%_~¤£!§\*\(\[\)\]\/\.\|\*\-\=])).{8,})$",
     *     match=true,
     *     message="The password is not valid, it must contain at least one lower case letter, a capital letter, a digital and a character: <, >,  &,  @, $, #, %, _, ~, ¤, £, !, §, *, (, [, ), ], /, ., |, *, -, ="
     * )
     *
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^([\w\-\. ]+)$/m",
     *     htmlPattern = "^([\w\-\. ]+)$",
     *     match=true,
     *     message="The firstName is not valid"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^([\w\-\. ]+)$/m",
     *     htmlPattern = "^([\w\-\. ]+)$",
     *     match=true,
     *     message="The lastName is not valid"
     * )
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
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $reviews;

    /**
     * @ORM\OneToOne(targetEntity=Customer::class, mappedBy="user")
     * @ORM\JoinTable(name="customer")
     * ORM\JoinColumn(name="user_id",nullable=true)
     */
    private $customer;

//    /**
//     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="reviews")
//     */
//    private $orders;
//


    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = strtolower($userName);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $password): self
    {
        $this->confirmPassword = $password;

        return $this;
    }

    public function setPassword(?string $password): self
    {
        if ($password) {
            $this->password = $password;
        }

        return $this;
    }


    public function encodePassword(UserPasswordEncoderInterface $encoder): self
    {
        if ($this->confirmPassword == $this->password) {
            $this->password = $encoder->encodePassword($this, $this->password);
        }

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $roles = $this::ROLES;
        }

        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = $this::ROLES;
            array_shift($roles);
        }
        $this->roles = $roles;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = ucwords(strtolower($firstName));

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = strtoupper($lastName);

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
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setGame($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }


    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
        $this->setConfirmPassword(null);
    }

}
