<?php

namespace App\Entity;

use App\Repository\MailRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=MailRepository::class)
 * @ApiResource(
 *  itemOperations={
 *      "get" ={
 *              "access_control"="is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') and object.getReceiver() == user",
 *      },
 *      "delete"={"access_control"="is_granted('ROLE_ADMIN')"},
 *      "put" = {"access_control"="is_granted('ROLE_ADMIN')"},
 *      "patch" = {"access_control"="is_granted('ROLE_ADMIN')"}
 *  },
 *  collectionOperations={
 *    "post" ={"access_control"="is_granted('ROLE_ADMIN')"},
 *    "get" ={
 *    "access_control"="is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') and object.getReceiver() == user",
 * 
 *     },
 *  }
 * )
 */
class Mail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $object;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sent_date;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

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

    public function getSentDate(): ?\DateTimeInterface
    {
        return $this->sent_date;
    }

    public function setSentDate(\DateTimeInterface $sent_date): self
    {
        $this->sent_date = $sent_date;

        return $this;
    }
}
