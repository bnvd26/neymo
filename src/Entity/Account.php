<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
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
    private $account_number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $available_cash;

    /**
     * @ORM\OneToOne(targetEntity=Company::class, mappedBy="account", cascade={"persist", "remove"})
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    public function setAccountNumber(string $account_number): self
    {
        $this->account_number = $account_number;

        return $this;
    }

    public function getAvailableCash(): ?string
    {
        return $this->available_cash;
    }

    public function setAvailableCash(string $available_cash): self
    {
        $this->available_cash = $available_cash;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        // set (or unset) the owning side of the relation if necessary
        $newAccount = null === $company ? null : $this;
        if ($company->getAccount() !== $newAccount) {
            $company->setAccount($newAccount);
        }

        return $this;
    }
}
