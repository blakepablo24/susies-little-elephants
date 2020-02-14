<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilyRepository")
 * @ORM\Table(name="families")
 * @UniqueEntity("name")
 */
class Family
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(message = "A family name is required")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $mum;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $dad;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $guardian;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Child", mappedBy="family", orphanRemoval=true)
     */
    private $children;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMum(): ?string
    {
        return $this->mum;
    }

    public function setMum(?string $mum): self
    {
        $this->mum = $mum;

        return $this;
    }

    public function getDad(): ?string
    {
        return $this->dad;
    }

    public function setDad(?string $dad): self
    {
        $this->dad = $dad;

        return $this;
    }

    public function getGuardian(): ?string
    {
        return $this->guardian;
    }

    public function setGuardian(?string $guardian): self
    {
        $this->guardian = $guardian;

        return $this;
    }

    /**
     * @return Collection|Child[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setFamily($this);
        }

        return $this;
    }

    public function removeChild(Child $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getFamily() === $this) {
                $child->setFamily(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
