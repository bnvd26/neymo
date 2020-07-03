<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavoriteRepository::class)
 */
class Favorite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Directory::class, inversedBy="favorites")
     */
    private $directory;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="favorites")
     */
    private $account;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirectory(): ?Directory
    {
        return $this->directory;
    }

    public function setDirectory(?Directory $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}
