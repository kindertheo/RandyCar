<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     */
    private $seat_number;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $license_plate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cars")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity=Fuel::class, inversedBy="cars")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fuel_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seat_number;
    }

    public function setSeatNumber(int $seat_number): self
    {
        $this->seat_number = $seat_number;

        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->license_plate;
    }

    public function setLicensePlate(string $license_plate): self
    {
        $this->license_plate = $license_plate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getFuelId(): ?Fuel
    {
        return $this->fuel_id;
    }

    public function setFuelId(?Fuel $fuel_id): self
    {
        $this->fuel_id = $fuel_id;

        return $this;
    }
}
