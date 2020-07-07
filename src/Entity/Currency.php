<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $shortName;

    /**
     * @ORM\OneToOne(targetEntity=Governance::class, inversedBy="currency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $governance;

    /**
     * @ORM\Column(type="integer")
     */
    private $exchangeRate;

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

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getGovernance(): ?Governance
    {
        return $this->governance;
    }

    public function setGovernance(Governance $governance): self
    {
        $this->governance = $governance;

        return $this;
    }

    public function getExchangeRate(): ?int
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(int $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }
}
