<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AddressRepository::class)
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
class Address
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $street;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="addresses")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Trip::class, mappedBy="start_address")
     */
    private $startTrips;

    /**
     * @ORM\OneToMany(targetEntity=Trip::class, mappedBy="destination_address")
     */
    private $endTrips;

    public function __construct()
    {
        $this->startTrips = new ArrayCollection();
        $this->endTrips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getStartTrips(): Collection
    {
        return $this->startTrips;
    }

    public function addStartTrip(Trip $start_trip): self
    {
        if (!$this->startTrips->contains($start_trip)) {
            $this->startTrips[] = $start_trip;
            $start_trip->setStartAddress($this);
        }

        return $this;
    }

    public function removeStartTrip(Trip $start_trip): self
    {
        if ($this->startTrips->removeElement($start_trip)) {
            // set the owning side to null (unless already changed)
            if ($start_trip->getStartAddress() === $this) {
                $start_trip->setStartAddress(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Trip>
     */
    public function getEndTrips(): Collection
    {
        return $this->endTrips;
    }

    public function addEndTrip(Trip $end_trip): self
    {
        if (!$this->endTrips->contains($end_trip)) {
            $this->endTrips[] = $end_trip;
            $end_trip->setDestinationAddress($this);
        }

        return $this;
    }

    public function removeEndTrip(Trip $end_trip): self
    {
        if ($this->endTrips->removeElement($end_trip)) {
            // set the owning side to null (unless already changed)
            if ($end_trip->getDestinationAddress() === $this) {
                $end_trip->setDestinationAddress(null);
            }
        }

        return $this;
    }
}
