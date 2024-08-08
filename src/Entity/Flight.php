<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $flightNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $departure = null;

    #[ORM\Column(length: 255)]
    private ?string $departureName = null;

    #[ORM\Column(length: 255)]
    private ?string $destination = null;

    #[ORM\Column(length: 255)]
    private ?string $destinationName = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateArriv = null;

    #[ORM\Column(length: 255)]
    private ?string $currency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $returnFlight = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): static
    {
        $this->departure = $departure;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(?\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDateArriv(): ?\DateTimeInterface
    {
        return $this->dateArriv;
    }

    public function setDateArriv(?\DateTimeInterface $dateArriv): static
    {
        $this->dateArriv = $dateArriv;

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

    public function getDepartureName(): ?string
    {
        return $this->departureName;
    }

    public function setDepartureName(string $departureName): static
    {
        $this->departureName = $departureName;

        return $this;
    }

    public function getDestinationName(): ?string
    {
        return $this->destinationName;
    }

    public function setDestinationName(string $destinationName): static
    {
        $this->destinationName = $destinationName;

        return $this;
    }

    public function getReturnFlight(): ?string
    {
        return $this->returnFlight;
    }

    public function setReturnFlight(?string $returnFlight): static
    {
        $this->returnFlight = $returnFlight;

        return $this;
    }

}
