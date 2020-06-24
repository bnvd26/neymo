<?php

namespace App\Entity;

use App\Repository\GovernanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GovernanceRepository::class)
 */
class Governance
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $money_name;

    /**
     * @ORM\OneToMany(targetEntity=GovernanceUserInformation::class, mappedBy="governance")
     */
    private $governanceUserInformation;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->governanceUserInformation = new ArrayCollection();
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

    public function getMoneyName(): ?string
    {
        return $this->money_name;
    }

    public function setMoneyName(string $money_name): self
    {
        $this->money_name = $money_name;

        return $this;
    }

    /**
     * @return Collection|GovernanceUserInformation[]
     */
    public function getGovernanceUserInformation(): Collection
    {
        return $this->governanceUserInformation;
    }

    public function addGovernanceUserInformation(GovernanceUserInformation $governanceUserInformation): self
    {
        if (!$this->governanceUserInformation->contains($governanceUserInformation)) {
            $this->governanceUserInformation[] = $governanceUserInformation;
            $governanceUserInformation->setGovernance($this);
        }

        return $this;
    }

    public function removeGovernanceUserInformation(GovernanceUserInformation $governanceUserInformation): self
    {
        if ($this->governanceUserInformation->contains($governanceUserInformation)) {
            $this->governanceUserInformation->removeElement($governanceUserInformation);
            // set the owning side to null (unless already changed)
            if ($governanceUserInformation->getGovernance() === $this) {
                $governanceUserInformation->setGovernance(null);
            }
        }

        return $this;
    }
}
