<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=TripRepository::class)
 * @ApiResource(
 * itemOperations={
 *      "get" = { "access_control"="is_granted('ROLE_USER')" },
 *      "delete"= {
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control"="is_granted('ROLE_USER') and object.getDriver() == user",
 *      },
 *      "put" = {
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control"="is_granted('ROLE_USER') and object.getDriver() == user",
 *      },
 *      "patch"= {
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control"="is_granted('ROLE_USER') and object.getDriver() == user",
 *      },
 *  },
 *  collectionOperations={
 *    "get" ={"access_control"="is_granted('ROLE_USER')"},
 *    "post" ={"access_control"="is_granted('ROLE_USER')"},
 *  }
 * )
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
    private $finished;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cancelled;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="startTrips")
     * @ORM\JoinColumn(nullable=true)
     */
    private $start_address;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="endTrips")
     * @ORM\JoinColumn(nullable=true)
     */
    private $destination_address;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="PassengerTrip")
     */
    private $passenger;

    /**
     * @ORM\OneToMany(targetEntity=Opinion::class, mappedBy="trip")
     */
    private $opinions;

    public function __construct()
    {
        $this->passenger = new ArrayCollection();
        $this->opinions = new ArrayCollection();
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
        return $this->finished;
    }

    public function setIsFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getIsCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setIsCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

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

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function isCancelled(): ?bool
    {
        return $this->cancelled;
    }

    /**
     * @return Collection<int, Opinion>
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): self
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions[] = $opinion;
            $opinion->setTrip($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getTrip() === $this) {
                $opinion->setTrip(null);
            }
        }

        return $this;
    }
}
