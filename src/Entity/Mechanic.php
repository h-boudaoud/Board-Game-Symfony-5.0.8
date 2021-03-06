<?php

namespace App\Entity;


use App\Repository\MechanicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MechanicRepository::class)
 * @UniqueEntity(fields="name", message="This mechanic name is already used.")
 */
class Mechanic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^([\w\-\. ]+)$/m",
     *     htmlPattern = "^([\w\-\. ]+)$",
     *     match=true,
     *     message="The mechanic name is not valid"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mechanicId;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="mechanics")
     * @ORM\JoinTable(name="game_mechanic")
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMechanicId(): ?string
    {
        return $this->mechanicId;
    }

    public function setMechanicId(string $mechanicId): self
    {
        $this->mechanicId = $mechanicId;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
        }

        return $this;
    }
}
