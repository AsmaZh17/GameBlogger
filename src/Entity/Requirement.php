<?php

namespace App\Entity;

use App\Repository\RequirementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequirementRepository::class)]
class Requirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $os = null;

    #[ORM\Column(length: 255)]
    private ?string $processor = null;

    #[ORM\Column(length: 255)]
    private ?string $memory = null;

    #[ORM\Column(length: 255)]
    private ?string $graphics = null;

    #[ORM\Column(length: 255)]
    private ?string $directX = null;

    #[ORM\Column(length: 255)]
    private ?string $network = null;

    #[ORM\Column(length: 255)]
    private ?string $hardDrive = null;

    #[ORM\Column(length: 255)]
    private ?string $soundCard = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'minimumRequirements')]
    private Collection $gamesMinimum;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'recommendedRequirements')]
    private Collection $gamesRecommended;

    public function __construct()
    {
        $this->gamesMinimum = new ArrayCollection();
        $this->gamesRecommended = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }

    public function setProcessor(string $processor): static
    {
        $this->processor = $processor;

        return $this;
    }

    public function getMemory(): ?string
    {
        return $this->memory;
    }

    public function setMemory(string $memory): static
    {
        $this->memory = $memory;

        return $this;
    }

    public function getGraphics(): ?string
    {
        return $this->graphics;
    }

    public function setGraphics(string $graphics): static
    {
        $this->graphics = $graphics;

        return $this;
    }

    public function getDirectX(): ?string
    {
        return $this->directX;
    }

    public function setDirectX(string $directX): static
    {
        $this->directX = $directX;

        return $this;
    }

    public function getNetwork(): ?string
    {
        return $this->network;
    }

    public function setNetwork(string $network): static
    {
        $this->network = $network;

        return $this;
    }

    public function getHardDrive(): ?string
    {
        return $this->hardDrive;
    }

    public function setHardDrive(string $hardDrive): static
    {
        $this->hardDrive = $hardDrive;

        return $this;
    }

    public function getSoundCard(): ?string
    {
        return $this->soundCard;
    }

    public function setSoundCard(string $soundCard): static
    {
        $this->soundCard = $soundCard;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->gamesMinimum;
    }

    public function addGame(Game $game): static
    {
        if (!$this->gamesMinimum->contains($game)) {
            $this->gamesMinimum->add($game);
            $game->setMinimumRequirements($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->gamesMinimum->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getMinimumRequirements() === $this) {
                $game->setMinimumRequirements(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesRecommended(): Collection
    {
        return $this->gamesRecommended;
    }

    public function addGamesRecommended(Game $gamesRecommended): static
    {
        if (!$this->gamesRecommended->contains($gamesRecommended)) {
            $this->gamesRecommended->add($gamesRecommended);
            $gamesRecommended->setRecommendedRequirements($this);
        }

        return $this;
    }

    public function removeGamesRecommended(Game $gamesRecommended): static
    {
        if ($this->gamesRecommended->removeElement($gamesRecommended)) {
            // set the owning side to null (unless already changed)
            if ($gamesRecommended->getRecommendedRequirements() === $this) {
                $gamesRecommended->setRecommendedRequirements(null);
            }
        }

        return $this;
    }
}
