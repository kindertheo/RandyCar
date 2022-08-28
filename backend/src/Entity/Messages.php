<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use DateTimeImmutable;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 * @ApiResource(
 *  itemOperations={
 *      "get" ={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user",
 *              "access_control"="is_granted('ROLE_USER') and object.getReceiver() == user",
 *      },
 *      "delete"={"access_control"="is_granted('ROLE_ADMIN')"},
 *      "put" = {"access_control"="is_granted('ROLE_ADMIN')"},
 *      "patch" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *  },
 *  collectionOperations={
 *    "post" ={"access_control"="is_granted('ROLE_ADMIN')"},
 *    "get" ={
 *             "access_control"="is_granted('ROLE_ADMIN')",
 *             "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user",
 *             "access_control"="is_granted('ROLE_USER') and object.getReceiver() == user",
 *      },
 *  }
 * )
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ReceiverMessages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receiver;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $is_read;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getIsRead(): ?DateTime
    {
        return $this->is_read;
    }

    public function setIsRead(DateTime $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }
}
