<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\ConversationsUsersRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationsUsersRepository::class)
 * @ORM\Table(name="tab_conversations_users")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class ConversationsUsers
{
    use Timestamps;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     *
     * @var string
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Conversations::class, inversedBy="conversationsUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conversation;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="conversationsUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $readAt;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getConversation(): ?Conversations
    {
        return $this->conversation;
    }

    public function setConversation(?Conversations $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReadAt(): ?DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(?DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }
}
