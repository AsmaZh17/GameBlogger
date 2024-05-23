<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    private ?Order $ordeer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?Game
    {
        return $this->product;
    }

    public function setProduct(?Game $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrdeer(): ?Order
    {
        return $this->ordeer;
    }

    public function setOrdeer(?Order $ordeer): static
    {
        $this->ordeer = $ordeer;

        return $this;
    }

}
