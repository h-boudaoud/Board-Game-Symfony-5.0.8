<?php

namespace App\Entity;

use App\Repository\UserRepository;
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
        'ROLE_USER',
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
        'ROLE_MODERATOR',
        'ROLE_OPERATOR', //operator ou packaging_operator
        'ROLE_STOREKEEPER', // storekeeper   ou inventory_manager
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
     *
     * @ORM\Column(type="string", length=255)
     *
     * Assert\Length(min=8, minMessage="The password is short, it must contain at least 8 characters",
     *     max="50", maxMessage="The password is long, it must contain a maximum of 50 characters"
     * )
     * Assert\Regex(
     *     pattern="/^(((?=[.\S]*\d)(?=[.\S]*[a-z])(?=[.\S]*[A-Z])(?=[.\S]*[<>&@$#%_~¤£!§\*\(\[\)\]\/\.\|\*\-\=])).{8,})$/m",
     *     htmlPattern="/^(((?=[.\S]*\d)(?=[.\S]*[a-z])(?=[.\S]*[A-Z])(?=[.\S]*[<>&@$#%_~¤£!§\*\(\[\)\]\/\.\|\*\-\=])).{8,})$",
     *     match=true,
     *     message="The password is not valid, it must contain at least one lower case letter, a capital letter, a digital and a character: <, >,  &,  @, $, #, %, _, ~, ¤, £, !, §, *, (, [, ), ], /, ., |, *, -, ="
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Password confirmation is invalid")
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
        $this->userName = $userName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function setConfirmPassword(string $password): self
    {
        $this->confirmPassword = $password;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function encodePassword(UserPasswordEncoderInterface $encoder): self
    {

        $this->password = $encoder->encodePassword($this, $this->password);

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

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
    }

}
