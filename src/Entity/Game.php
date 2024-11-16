<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\GameImageController;
use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\OpenApi\Model;


#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['game:read']],
)]
#[Post(
    inputFormats: ['multipart' => ['multipart/form-data']],
)]
#[Get()]
#[GetCollection()]
#[Patch()]
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

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['game:read', 'game:write'])]
    private ?string $file = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read', 'game:write'])]
    private ?string $version = null;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

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
}
