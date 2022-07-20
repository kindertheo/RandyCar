<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TripRepository::class)
 */
class Trip
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $max_passenger;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_finished;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_cancelled;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $start_address;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destination_address;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="PassengerTrip")
     */
    private $passenger;

    public function __construct()
    {
        $this->passenger = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxPassenger(): ?int
    {
        return $this->max_passenger;
    }

    public function setMaxPassenger(int $max_passenger): self
    {
        $this->max_passenger = $max_passenger;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getIsFinished(): ?bool
    {
        return $this->is_finished;
    }

    public function setIsFinished(bool $is_finished): self
    {
        $this->is_finished = $is_finished;

        return $this;
    }

    public function getIsCancelled(): ?bool
    {
        return $this->is_cancelled;
    }

    public function setIsCancelled(bool $is_cancelled): self
    {
        $this->is_cancelled = $is_cancelled;

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getStartAddress(): ?Address
    {
        return $this->start_address;
    }

    public function setStartAddress(?Address $start_address): self
    {
        $this->start_address = $start_address;

        return $this;
    }

    public function getDestinationAddress(): ?Address
    {
        return $this->destination_address;
    }

    public function setDestinationAddress(?Address $destination_address): self
    {
        $this->destination_address = $destination_address;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPassenger(): Collection
    {
        return $this->passenger;
    }

    public function addPassenger(User $passenger): self
    {
        if (!$this->passenger->contains($passenger)) {
            $this->passenger[] = $passenger;
        }

        return $this;
    }

    public function removePassenger(User $passenger): self
    {
        $this->passenger->removeElement($passenger);

        return $this;
    }
}
