<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
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
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=GovernanceUserInformation::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $governanceUserInformation;

    /**
     * @ORM\ManyToMany(targetEntity=Company::class, mappedBy="user")
     */
    private $companies;

    /**
     * @ORM\OneToOne(targetEntity=Particular::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $particular;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
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

    public function getGovernanceUserInformation(): ?GovernanceUserInformation
    {
        return $this->governanceUserInformation;
    }

    public function getGovernanceId() 
    {
        return $this->getGovernanceUserInformation()->getGovernance()->getId();
    }

    public function setGovernanceUserInformation(GovernanceUserInformation $governanceUserInformation): self
    {
        $this->governanceUserInformation = $governanceUserInformation;

        // set the owning side of the relation if necessary
        if ($governanceUserInformation->getUser() !== $this) {
            $governanceUserInformation->setUser($this);
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
            $company->addUser($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            $company->removeUser($this);
        }

        return $this;
    }

    public function getParticular(): ?Particular
    {
        return $this->particular;
    }

    public function setParticular(?Particular $particular): self
    {
        $this->particular = $particular;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $particular ? null : $this;
        if ($particular->getUser() !== $newUser) {
            $particular->setUser($newUser);
        }

        return $this;
    }
}
