<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GlobalPostRepository")
 * @ORM\Table(name="global_posts")
 */
class GlobalPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message = "A subject is required")
     */
    private $subject;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message = "Post details are required")
     */
    private $content;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFileName;

    /**
     * @ORM\Column(type="time")
     */
    private $Time;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GlobalPostComment", mappedBy="GlobalPost")
     */
    private $globalPostComments;

    public function __construct()
    {
        $this->globalPostComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->Time;
    }

    public function setTime(\DateTimeInterface $Time): self
    {
        $this->Time = $Time;

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
            $globalPostComment->setGlobalPost($this);
        }

        return $this;
    }

    public function removeGlobalPostComment(GlobalPostComment $globalPostComment): self
    {
        if ($this->globalPostComments->contains($globalPostComment)) {
            $this->globalPostComments->removeElement($globalPostComment);
            // set the owning side to null (unless already changed)
            if ($globalPostComment->getGlobalPost() === $this) {
                $globalPostComment->setGlobalPost(null);
            }
        }

        return $this;
    }

}