<?php

namespace App\Entity;

use App\Repository\OpinionRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=OpinionRepository::class)
 * @ApiResource(
 *  itemOperations={
 *      "get" ={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control"="is_granted('ROLE_USER')",
 *      },
 *      "delete"={"access_control"="is_granted('ROLE_ADMIN')"},
 *      "put" = {"access_control"="is_granted('ROLE_ADMIN')"},
 *      "patch" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *  },
 *  collectionOperations={
 *    "post" ={
 *      "access_control"="is_granted('ROLE_ADMIN')",
 *      "access_control"="is_granted('ROLE_USER')",
 *      },
*     "get" ={
*              "access_control"="is_granted('ROLE_ADMIN')",
*              "access_control"="is_granted('ROLE_USER')",
*       },
 *  }
 * )
 */
class Opinion
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
    private $notation;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="opinions")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $emitter;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="opinions")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $receptor;

    /**
     * @ORM\ManyToOne(targetEntity=Trip::class, inversedBy="opinions")
     */
    private $trip;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotation(): ?int
    {
        return $this->notation;
    }

    public function setNotation(int $notation): self
    {
        $this->notation = $notation;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getEmitter(): ?User
    {
        return $this->emitter;
    }

    public function setEmitter(?User $emitter): self
    {
        $this->emitter = $emitter;

        return $this;
    }

    public function getReceptor(): ?User
    {
        return $this->receptor;
    }

    public function setReceptor(?User $receptor): self
    {
        $this->receptor = $receptor;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): self
    {
        $this->trip = $trip;

        return $this;
    }
}
