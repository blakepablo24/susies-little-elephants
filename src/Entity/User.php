<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email already exists!"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Please enter an email address")
     * @Assert\Email(message = "Please enter a valid email address")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\NotBlank(message = "Please enter a valid password")
     * @Assert\Length(max=4096)
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message = "Please enter a First Name")
     * @ORM\Column(type="string", length=45)
     */
    private $name;

    /**
     * @Assert\NotBlank(message = "Please enter a Last Name")
     * @ORM\Column(type="string", length=45)
     */
    private $last_name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user_id")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GlobalPostComment", mappedBy="User")
     */
    private $globalPostComments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->globalPostComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUserId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUserId() === $this) {
                $comment->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GlobalPostComment[]
     */
    public function getGlobalPostComments(): Collection
    {
        return $this->globalPostComments;
    }

    public function addGlobalPostComment(GlobalPostComment $globalPostComment): self
    {
        if (!$this->globalPostComments->contains($globalPostComment)) {
            $this->globalPostComments[] = $globalPostComment;
            $globalPostComment->setUser($this);
        }

        return $this;
    }

    public function removeGlobalPostComment(GlobalPostComment $globalPostComment): self
    {
        if ($this->globalPostComments->contains($globalPostComment)) {
            $this->globalPostComments->removeElement($globalPostComment);
            // set the owning side to null (unless already changed)
            if ($globalPostComment->getUser() === $this) {
                $globalPostComment->setUser(null);
            }
        }

        return $this;
    }
}
