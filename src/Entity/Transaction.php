<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
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
    private $transfered_money;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transactions")
     */
    private $beneficiary;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transactionsTo")
     */
    private $emiter;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransferedMoney(): ?string
    {
        return $this->transfered_money;
    }

    public function setTransferedMoney(string $transfered_money): self
    {
        $this->transfered_money = $transfered_money;

        return $this;
    }

    public function getBeneficiary(): ?Account
    {
        return $this->beneficiary;
    }

    public function setBeneficiary(?Account $beneficiary): self
    {
        $this->beneficiary = $beneficiary;

        return $this;
    }

    public function getEmiter(): ?Account
    {
        return $this->emiter;
    }

    public function setEmiter(?Account $emiter): self
    {
        $this->emiter = $emiter;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
