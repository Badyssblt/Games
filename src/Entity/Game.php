<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\GameImageController;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['game:read']],
)]
#[Post(
    inputFormats: ['multipart' => ['multipart/form-data']],
    security: "is_granted('ROLE_ADMIN')",
)]
#[Post(
    inputFormats: ['multipart' => ['multipart/form-data']],
    controller: GameImageController::class,
    uriTemplate: "/games/{id}/image",
    security: "is_granted('ROLE_ADMIN')",

)]
#[Get()]
#[GetCollection()]
#[Patch(
    security: "is_granted('ROLE_ADMIN')",
)]
#[Delete(
    security: "is_granted('ROLE_ADMIN')",
)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game:read', 'game:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read', 'game:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['game:read', 'game:write'])]
    private ?string $description = null;


    #[ORM\Column]
    #[Groups(['game:read', 'game:write'])]
    private ?string $size = null;


    #[Vich\UploadableField(mapping: 'games', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Groups(['game:write'])]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['game:read', 'game:write'])]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Version>
     */
    #[ORM\OneToMany(targetEntity: Version::class, mappedBy: 'game', cascade: ['PERSIST', 'REMOVE'])]
    #[Groups(['game:read'])]
    private Collection $version;

    public function __construct()
    {
        $this->version = new ArrayCollection();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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


    public function getSize(): ?string
    {

        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @return Collection<int, Version>
     */
    public function getVersion(): Collection
    {
        return $this->version;
    }

    public function addVersion(Version $version): static
    {
        if (!$this->version->contains($version)) {
            $this->version->add($version);
            $version->setGame($this);
        }

        return $this;
    }

    public function removeVersion(Version $version): static
    {
        if ($this->version->removeElement($version)) {
            // set the owning side to null (unless already changed)
            if ($version->getGame() === $this) {
                $version->setGame(null);
            }
        }

        return $this;
    }
}
