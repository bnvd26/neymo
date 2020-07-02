<?php

namespace App\Entity;

use App\Repository\DirectoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DirectoryRepository::class)
 */
class Directory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, inversedBy="directory", cascade={"persist", "remove"})
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Contacts::class, mappedBy="directory")
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity=Favorite::class, mappedBy="directory")
     */
    private $favorites;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Contacts[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contacts $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setDirectory($this);
        }

        return $this;
    }

    public function removeContact(Contacts $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getDirectory() === $this) {
                $contact->setDirectory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setDirectory($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->contains($favorite)) {
            $this->favorites->removeElement($favorite);
            // set the owning side to null (unless already changed)
            if ($favorite->getDirectory() === $this) {
                $favorite->setDirectory(null);
            }
        }

        return $this;
    }
}
