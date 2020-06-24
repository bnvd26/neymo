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

    /**
     * @ORM\OneToMany(targetEntity=Company::class, mappedBy="governance")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity=Particular::class, mappedBy="governance")
     */
    private $particulars;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->governanceUserInformation = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->particulars = new ArrayCollection();
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

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setGovernance($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getGovernance() === $this) {
                $company->setGovernance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Particular[]
     */
    public function getParticulars(): Collection
    {
        return $this->particulars;
    }

    public function addParticular(Particular $particular): self
    {
        if (!$this->particulars->contains($particular)) {
            $this->particulars[] = $particular;
            $particular->setGovernance($this);
        }

        return $this;
    }

    public function removeParticular(Particular $particular): self
    {
        if ($this->particulars->contains($particular)) {
            $this->particulars->removeElement($particular);
            // set the owning side to null (unless already changed)
            if ($particular->getGovernance() === $this) {
                $particular->setGovernance(null);
            }
        }

        return $this;
    }
}
