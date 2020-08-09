<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    public const SIZE_UNITS = ['inches', 'mm', 'cm', 'dm', 'm'];
    public const WEIGHT_UNITS = ['g', 'lbs', 'Kg'];


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(1900)
     * @Assert\LessThanOrEqual(propertyPath ="year")
     *
     */
    private $yearPublished;

    /**
     * @ORM\Column(type="integer", options={"default" : 1})
     * @Assert\PositiveOrZero
     * @Assert\LessThanOrEqual(propertyPath ="maxPlayers")
     */
    private $minPlayers = 1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     */
    private $maxPlayers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     * @Assert\LessThanOrEqual(propertyPath ="maxPlaytime")
     */
    private $minPlaytime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     */
    private $maxPlaytime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     * @Assert\LessThan(100)
     */
    private $minAge;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Assert\PositiveOrZero
     */
    private $price;

    /**
     * A manufacturer's suggested retail price (MSRP)
     * Prix de détail suggéré par le fabricant (PDSF)
     *
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     * @Assert\PositiveOrZero
     */
    private $msrp;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2, nullable=true)
     * @Assert\NegativeOrZero
     */
    private $discount;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $artists = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $names = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $publishers = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private $rulesUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private $officialUrl;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank
     */
    private $gameId;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     * @Assert\Choice(
     *     choices = { true, false },
     *     message = "Allowed values of published are true ou false."
     * )
     */
    private $published;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3, nullable=true)
     * @Assert\PositiveOrZero
     */
    private $weightAmount;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Choice(
     *     choices=Game::WEIGHT_UNITS,
     *     message="Allowed values of $weight units are :lbs, g"
     * )
     */
    private $weightUnits;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3, nullable=true)
     * @Assert\PositiveOrZero
     */
    private $sizeHeight;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3, nullable=true)
     * @Assert\PositiveOrZero
     */
    private $sizeWidth;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=3, nullable=true)
     * @Assert\PositiveOrZero
     */
    private $sizeDepth;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Choice(
     *     choices=Game::SIZE_UNITS,
     *     message="Allowed values of size units are :inches, cm, m, mm, dm"
     * )
     */
    private $sizeUnits;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primaryPublisher;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message = "The image url '{{ value }}' is not a valid url")
     * @Assert\Regex(
     *     pattern="/\.(?:jpg|gif|png,ico)$/m",
     *     htmlPattern = "\.(?:jpg|gif|png,ico)$",
     *     match=true,
     *     message="The image url '{{ value }}' is not a valid url"
     * )
     */
    private $imageUrl;


    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     */
    private $inStock;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero
     */
    private $stockShortageAlarm;


    /**
     * @ORM\ManyToMany(targetEntity=Mechanic::class, inversedBy="games")
     */
    private $mechanics;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="games")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="game" , cascade={"persist"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $reviews;




    //***************//
      //    Methods    //
     //***************//



    function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->mechanics = new ArrayCollection();
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

    public function getYear(): ?int
    {
        return intval((new \DateTime())->format('Y'));;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }

    public function setYearPublished(?int $yearPublished): self
    {
        $this->yearPublished = $yearPublished;

        return $this;
    }

    public function getInStock(): ?int
    {
        return $this->inStock;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(int $minPlayers): self
    {
        $this->minPlayers = $minPlayers > 0 ? $minPlayers : null;

        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(?int $maxPlayers): self
    {
        $this->maxPlayers = $maxPlayers > 0 ? $maxPlayers : null;

        return $this;
    }

    public function getMinPlaytime(): ?int
    {
        return $this->minPlaytime;
    }

    public function setMinPlaytime(?int $minPlaytime): self
    {
        $this->minPlaytime = $minPlaytime;

        return $this;
    }

    public function getMaxPlaytime(): ?int
    {
        return $this->maxPlaytime;
    }

    public function setMaxPlaytime(?int $maxPlaytime): self
    {
        $this->maxPlaytime = $maxPlaytime;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMsrp(): ?string
    {
        return $this->msrp;
    }

    public function setMsrp(?string $msrp): self
    {
        $this->msrp = $msrp;

        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount < 0 ? $discount : null;

        return $this;
    }

    public function getArtists(): ?array
    {
        return $this->artists;
    }

    public function setArtists(?array $artists): self
    {
        $this->artists = explode(',', $artists);

        return $this;
    }

    public function artistsList(string $value = null)
    {
        $artists =[];
        if ($value) {
            $artists = explode(',',$value);
        }
        foreach ($artists as $artist) {
            if ($artist && !in_array($artist, $this->artists)) {
                $this->artists[] = $artist;
            }
        }

        $value = implode (',',$this->artists);
        return $value;
    }

    public function getNames(): ?array
    {
        return $this->names;
    }

    public function setNames(?array $names): self
    {
        $this->names = $names;

        return $this;
    }


    public function namesList(?string $value = null)
    {
        $names =[];
        if ($value) {
            $names = explode(',',$value);
        }
        foreach ($names as $name) {
            if ($name && !in_array($name, $this->names)) {
                $this->names[] = $name;
            }
        }

        $value = implode (',',$this->names);
        return $value;
    }


    public function getPublishers(): ?array
    {
        return $this->publishers;
    }


    public function publishersList(?string $value = null)
    {
        $publishers =[];
        if ($value) {
            $publishers = explode(',',$value);
        }
        foreach ($publishers as $publisher) {
            if ($publisher && !in_array($publisher, $this->publishers)) {
                $this->publishers[] = $publisher;
            }
        }
        $value = implode (',',$this->publishers);
        return $value;
    }

    public function setPublishers(?array $publishers): self
    {
        $this->publishers = $publishers;

        return $this;
    }

    public function getRulesUrl(): ?string
    {
        return $this->rulesUrl;
    }

    public function setRulesUrl(?string $rulesUrl): self
    {
        $this->rulesUrl = $rulesUrl;

        return $this;
    }

    public function getOfficialUrl(): ?string
    {
        return $this->officialUrl;
    }

    public function setOfficialUrl(?string $officialUrl): self
    {
        $this->officialUrl = $officialUrl;

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(?string $gameId): self
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getWeightAmount(): ?int
    {
        return $this->weightAmount;
    }

    public function setWeightAmount(?int $weightAmount): self
    {
        $this->weightAmount = $weightAmount;

        return $this;
    }

    public function getWeightUnits(): ?string
    {
        return $this->weightUnits;
    }

    public function setWeightUnits(?string $weightUnits): self
    {
        $this->weightUnits = $weightUnits;

        return $this;
    }

    public function getSizeHeight(): ?string
    {
        return $this->sizeHeight;
    }

    public function setSizeHeight(?string $sizeHeight): self
    {
        $this->sizeHeight = $sizeHeight;

        return $this;
    }

    public function getSizeWidth(): ?string
    {
        return $this->sizeWidth;
    }

    public function setSizeWidth(?string $sizeWidth): self
    {
        $this->sizeWidth = $sizeWidth;

        return $this;
    }

    public function getSizeDepth(): ?string
    {
        return $this->sizeDepth;
    }

    public function setSizeDepth(?string $sizeDepth): self
    {
        $this->sizeDepth = $sizeDepth;

        return $this;
    }

    public function getSizeUnits(): ?string
    {
        return $this->sizeUnits;
    }

    public function setSizeUnits(?string $sizeUnits): self
    {
        $this->sizeUnits = $sizeUnits;

        return $this;
    }

    public function getPrimaryPublisher(): ?string
    {
        return $this->primaryPublisher;
    }

    public function setPrimaryPublisher(?string $primaryPublisher): self
    {
        $this->primaryPublisher = $primaryPublisher;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }


    public function setInStock(?int $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getStockShortageAlarm(): ?int
    {
        return $this->yearPublished;
    }

    public function setStockShortageAlarm(?int $stockShortageAlarm): self
    {
        $this->stockShortageAlarm = $stockShortageAlarm;

        return $this;
    }



    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Mechanic[]
     */
    public function getMechanics(): Collection
    {
        return $this->mechanics;
    }

    public function addMechanic(Mechanic $mechanic): self
    {
        if (!$this->mechanics->contains($mechanic)) {
            $this->mechanics[] = $mechanic;
        }

        return $this;
    }

    public function removeMechanic(Mechanic $mechanic): self
    {
        if ($this->mechanics->contains($mechanic)) {
            $this->mechanics->removeElement($mechanic);
        }

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
            if ($review->getGame() === $this) {
                $review->getGame(null);
            }
        }

        return $this;
    }

    public function getAvgRating():?float
    {
        //return $this->avgRating;

        $ratings = [];
        foreach ($this->reviews as $review){
            if($review->getRating()){
                $ratings[] = $review->getRating();
            }
        }
        return (Count($ratings))? round(array_sum($ratings) / Count($ratings), 2):null;

    }

}
