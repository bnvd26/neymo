<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToOne(targetEntity=Particular::class, mappedBy="account", cascade={"persist", "remove"})
     */
    private $particular;

    /**
     * @ORM\OneToOne(targetEntity=Company::class, mappedBy="account", cascade={"persist", "remove"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="beneficiary")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="emiter")
     */
    private $transactionsTo;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

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

    public function getParticular(): ?Particular
    {
        return $this->particular;
    }

    public function setParticular(?Particular $particular): self
    {
        $this->particular = $particular;

        // set (or unset) the owning side of the relation if necessary
        $newAccount = null === $particular ? null : $this;
        if ($particular->getAccount() !== $newAccount) {
            $particular->setAccount($newAccount);
        }

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

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setBeneficiary($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getBeneficiary() === $this) {
                $transaction->setBeneficiary(null);
            }
        }

        return $this;
    }

    public function addMoneyToBeneficiary($value)
    {
        return $this->setAvailableCash((int) $this->getAvailableCash() + (int) $value);
    }

    public function removeMoneyToEmiter($value)
    {
        return $this->setAvailableCash((int) $this->getAvailableCash() - (int) $value);
    }
}
