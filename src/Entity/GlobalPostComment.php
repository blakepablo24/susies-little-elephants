<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GlobalPostCommentRepository")
 * @ORM\Table(name="global_comments")
 */
class GlobalPostComment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "A comment is required to submit")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GlobalPost", inversedBy="globalPostComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $GlobalPost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="globalPostComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function getId(): ?int
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

    public function getGlobalPost(): ?GlobalPost
    {
        return $this->GlobalPost;
    }

    public function setGlobalPost(?GlobalPost $GlobalPost): self
    {
        $this->GlobalPost = $GlobalPost;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
