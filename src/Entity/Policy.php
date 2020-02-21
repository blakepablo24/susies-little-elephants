<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolicyRepository")
 * @ORM\Table(name="policies")
 */
class Policy
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $policy_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $policy_file_name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPolicyName(): ?string
    {
        return $this->policy_name;
    }

    public function setPolicyName(string $policy_name): self
    {
        $this->policy_name = $policy_name;

        return $this;
    }

    public function getPolicyFileName(): ?string
    {
        return $this->policy_file_name;
    }

    public function setPolicyFileName(string $policy_file_name): self
    {
        $this->policy_file_name = $policy_file_name;

        return $this;
    }
}
