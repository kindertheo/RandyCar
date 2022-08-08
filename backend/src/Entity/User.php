<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="`user`")
 * @ApiResource
 */
class User
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
     * @ORM\Column(type="string", length=100)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $trip_count;

    /**
     * @ORM\OneToMany(targetEntity=Car::class, mappedBy="owner", orphanRemoval=true)
     */
    private $cars;

    /**
     * @ORM\OneToMany(targetEntity=Opinion::class, mappedBy="emitter", orphanRemoval=true)
     */
    private $opinions;

    /**
     * @ORM\OneToMany(targetEntity=Mail::class, mappedBy="receiver", orphanRemoval=true)
     */
    private $mails;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="receiver", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="author", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="receiver", orphanRemoval=true)
     */
    private $ReceiverMessages;

    /**
     * @ORM\OneToMany(targetEntity=Trip::class, mappedBy="driver")
     */
    private $driverTrips;

    /**
     * @ORM\ManyToMany(targetEntity=Trip::class, mappedBy="passenger")
     */
    private $passengerTrips;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->opinions = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->ReceiverMessages = new ArrayCollection();
        $this->driverTrips = new ArrayCollection();
        $this->passengerTrips = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        $this->setCreatedAt(new \DateTimeImmutable('now'));
        $this->setTripCount(0);
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getTripCount(): ?int
    {
        return $this->trip_count;
    }

    public function setTripCount(int $trip_count): self
    {
        $this->trip_count = $trip_count;

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
            $car->setOwner($this);
        }

        return $this;
    }

    public function removeCar(Car $car): self
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getOwner() === $this) {
                $car->setOwner(null);
            }
        }

        return $this;
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
            $opinion->setEmitter($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getEmitter() === $this) {
                $opinion->setEmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mail>
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(Mail $mail): self
    {
        if (!$this->mails->contains($mail)) {
            $this->mails[] = $mail;
            $mail->setReceiver($this);
        }

        return $this;
    }

    public function removeMail(Mail $mail): self
    {
        if ($this->mails->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getReceiver() === $this) {
                $mail->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setReceiver($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getReceiver() === $this) {
                $notification->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getReceiverMessages(): Collection
    {
        return $this->ReceiverMessages;
    }

    public function addReceiverMessage(Messages $receiverMessage): self
    {
        if (!$this->ReceiverMessages->contains($receiverMessage)) {
            $this->ReceiverMessages[] = $receiverMessage;
            $receiverMessage->setReceiver($this);
        }

        return $this;
    }

    public function removeReceiverMessage(Messages $receiverMessage): self
    {
        if ($this->ReceiverMessages->removeElement($receiverMessage)) {
            // set the owning side to null (unless already changed)
            if ($receiverMessage->getReceiver() === $this) {
                $receiverMessage->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->driverTrips;
    }

    public function addTrip(Trip $trip): self
    {
        if (!$this->driverTrips->contains($trip)) {
            $this->driverTrips[] = $trip;
            $trip->setDriver($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): self
    {
        if ($this->driverTrips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getDriver() === $this) {
                $trip->setDriver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getPassengerTrips(): Collection
    {
        return $this->passengerTrips;
    }

    public function addPassengerTrips(Trip $passengerTrips): self
    {
        if (!$this->passengerTrips->contains($passengerTrips)) {
            $this->passengerTrips[] = $passengerTrips;
            $passengerTrips->addPassenger($this);
        }

        return $this;
    }

    public function removePassengerTrips(Trip $passengerTrips): self
    {
        if ($this->passengerTrips->removeElement($passengerTrips)) {
            $passengerTrips->removePassenger($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getDriverTrips(): Collection
    {
        return $this->driverTrips;
    }

    public function addDriverTrip(Trip $driverTrip): self
    {
        if (!$this->driverTrips->contains($driverTrip)) {
            $this->driverTrips[] = $driverTrip;
            $driverTrip->setDriver($this);
        }

        return $this;
    }

    public function removeDriverTrip(Trip $driverTrip): self
    {
        if ($this->driverTrips->removeElement($driverTrip)) {
            // set the owning side to null (unless already changed)
            if ($driverTrip->getDriver() === $this) {
                $driverTrip->setDriver(null);
            }
        }

        return $this;
    }

    public function addPassengerTrip(Trip $passengerTrip): self
    {
        if (!$this->passengerTrips->contains($passengerTrip)) {
            $this->passengerTrips[] = $passengerTrip;
            $passengerTrip->addPassenger($this);
        }

        return $this;
    }

    public function removePassengerTrip(Trip $passengerTrip): self
    {
        if ($this->passengerTrips->removeElement($passengerTrip)) {
            $passengerTrip->removePassenger($this);
        }

        return $this;
    }
}
