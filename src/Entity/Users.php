<?php

namespace App\Entity;

use App\Entity\Traits\Timestamps;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @ORM\Table(name="tab_users")
 * @UniqueEntity(fields={"username"}, message="There is already user with this username")
 * @UniqueEntity(fields={"email"}, message="There is already user with this email")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Users implements UserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @var string
     */
    private string $username = '';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private string $password;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private bool $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="user")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=ConversationsUsers::class, mappedBy="user", orphanRemoval=true)
     */
    private $conversationsUsers;

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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
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
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
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
            $conversationsUser->setUser($this);
        }

        return $this;
    }

    public function removeConversationsUser(ConversationsUsers $conversationsUser): self
    {
        if ($this->conversationsUsers->contains($conversationsUser)) {
            $this->conversationsUsers->removeElement($conversationsUser);
            // set the owning side to null (unless already changed)
            if ($conversationsUser->getUser() === $this) {
                $conversationsUser->setUser(null);
            }
        }

        return $this;
    }
}
