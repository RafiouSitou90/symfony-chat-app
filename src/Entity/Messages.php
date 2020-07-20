<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 * @ORM\Table(name="tab_messages")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Messages
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
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Conversations::class, inversedBy="messages")
     */
    private $conversations;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getConversations(): ?Conversations
    {
        return $this->conversations;
    }

    public function setConversations(?Conversations $conversations): self
    {
        $this->conversations = $conversations;

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
}
