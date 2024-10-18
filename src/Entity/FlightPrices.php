<?php

namespace App\Entity;

use App\Repository\FlightPricesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightPricesRepository::class)]
class FlightPrices
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $flight_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $recorded_at = null;

    #[ORM\Column(length: 255)]
    private ?string $currency = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFlightId(): ?int
    {
        return $this->flight_id;
    }

    public function setFlightId(?int $flight_id): static
    {
        $this->flight_id = $flight_id;

        return $this;
    }

    public function getRecordedAt(): ?\DateTimeInterface
    {
        return $this->recorded_at;
    }

    public function setRecordedAt(\DateTimeInterface $recorded_at): static
    {
        $this->recorded_at = $recorded_at;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
