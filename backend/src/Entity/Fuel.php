<?php

namespace App\Entity;

use App\Repository\FuelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=FuelRepository::class)
 * @ApiResource(
 *  itemOperations={
 *      "get" ={"access_control"="is_granted('ROLE_USER')"},
 *      "delete"={"access_control"="is_granted('ROLE_ADMIN')"},
 *      "put" = {"access_control"="is_granted('ROLE_ADMIN')"},
 *      "patch" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *  },
 *  collectionOperations={
 *    "post" ={"access_control"="is_granted('ROLE_ADMIN')"},
*     "get" ={"access_control"="is_granted('ROLE_USER')"},
 *  }
 * )
 */
class Fuel
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
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $consumption;

    /**
     * @ORM\OneToMany(targetEntity=Car::class, mappedBy="fuel")
     */
    private $cars;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getConsumption(): ?float
    {
        return $this->consumption;
    }

    public function setConsumption(float $consumption): self
    {
        $this->consumption = $consumption;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): self
    {
        if (!$this->cars->contains($car)) {
            $this->cars[] = $car;
            $car->setFuel($this);
        }

        return $this;
    }

    public function removeCar(Car $car): self
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getFuel() === $this) {
                $car->setFuel(null);
            }
        }

        return $this;
    }
}
