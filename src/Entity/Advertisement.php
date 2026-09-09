<?php

namespace App\Entity;

use App\Repository\AdvertisementRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AdvertisementRepository::class)]
class Advertisement
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, max: 255,minMessage: 'The title must be at least 5 characters long', maxMessage: 'The title cannot be longer than 255 characters')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, max: 5000,minMessage: 'The description must be at least 5 characters long', maxMessage: 'The description cannot be longer than 5000 characters')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1,max: 9,minMessage:'The price should be at least 1 digit',maxMessage:'The price cannot be longer than 9 digits')]
    private ?int $price = null;

    #[ORM\Column]
    private ?\DateTime $created = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favorited')]
    private Collection $favoritedBy;

    #[ORM\ManyToOne(inversedBy: 'advertisements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, AdvertisementImage>
     */
    #[ORM\OneToMany(targetEntity: AdvertisementImage::class, mappedBy: 'advertisement', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $locationName = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Invalid latitude')]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Invalid longitude')]
    private ?float $longitude = null;

    public function __construct()
    {
        $this->favoritedBy = new ArrayCollection();
        $this->created = new DateTime();
        $this->images = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = abs($price);

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): static
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoritedBy(): Collection
    {
        return $this->favoritedBy;
    }

    public function addFavoritedBy(User $favoritedBy): static
    {
        if (!$this->favoritedBy->contains($favoritedBy)) {
            $this->favoritedBy->add($favoritedBy);
        }

        return $this;
    }

    public function removeFavoritedBy(User $favoritedBy): static
    {
        $this->favoritedBy->removeElement($favoritedBy);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, AdvertisementImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(AdvertisementImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAdvertisement($this);
        }

        return $this;
    }

    public function removeImage(AdvertisementImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAdvertisement() === $this) {
                $image->setAdvertisement(null);
            }
        }

        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): static
    {
        $this->locationName = $locationName;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}
