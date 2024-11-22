<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VersionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: VersionRepository::class)]
#[ApiResource(security: "is_granted('ROLE_ADMIN')",)]
class Version
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['game:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['game:read'])]
    private ?string $file = null;

    #[ORM\ManyToOne(inversedBy: 'version')]
    private ?Game $game = null;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
