<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;


// ROLE_USER can only DELETE if its his car
// ROLE_USER can only PUT if its his car
// TODO Add ROLE_ADMIN to delete
// TODO Create tests

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 * @ApiResource(
 *  itemOperations={
 *      "get"={"access_control"="is_granted('ROLE_USER')"},
 *      "delete"={
 *              "access_control"="is_granted('ROLE_USER') and object.getOwner() == user",
 *              "access_control"="is_granted('ROLE_ADMIN')"        
 *      },
 *      "put" = {"access_control"="is_granted('ROLE_ADMIN')"},
 *      "patch" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *  },
 *  collectionOperations={
 *    "get"={"access_control"="is_granted('ROLE_USER')"},
 *    "post" ={"access_control"="is_granted('ROLE_ADMIN')"},
 *  }
 * )
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
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Fuel::class, inversedBy="cars")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $fuel;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getFuel(): ?Fuel
    {
        return $this->fuel;
    }

    public function setFuel(?Fuel $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }
}
