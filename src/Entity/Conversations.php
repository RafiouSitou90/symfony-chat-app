<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\ConversationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass=ConversationsRepository::class)
 * @ORM\Table(name="tab_conversations", indexes={@Index(name="last_message_id_index", columns={"last_message_id"})})
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Conversations
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
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="conversations")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=ConversationsUsers::class, mappedBy="conversation", orphanRemoval=true)
     */
    private $conversationsUsers;

    /**
     * @ORM\OneToOne(targetEntity=Messages::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="last_message_id", referencedColumnName="id")
     */
    private $lastMessage = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->conversationsUsers = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Collection|Messages[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversations($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getConversations() === $this) {
                $message->setConversations(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ConversationsUsers[]
     */
    public function getConversationsUsers(): Collection
    {
        return $this->conversationsUsers;
    }

    public function addConversationsUser(ConversationsUsers $conversationsUser): self
    {
        if (!$this->conversationsUsers->contains($conversationsUser)) {
            $this->conversationsUsers[] = $conversationsUser;
            $conversationsUser->setConversation($this);
        }

        return $this;
    }

    public function removeConversationsUser(ConversationsUsers $conversationsUser): self
    {
        if ($this->conversationsUsers->contains($conversationsUser)) {
            $this->conversationsUsers->removeElement($conversationsUser);
            // set the owning side to null (unless already changed)
            if ($conversationsUser->getConversation() === $this) {
                $conversationsUser->setConversation(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Messages
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Messages $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }
}
