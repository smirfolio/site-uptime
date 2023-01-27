<?php

namespace App\Entity;

use App\Repository\SitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SitesRepository::class)]
class Sites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1020)]
    private ?string $siteAdress = null;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: Status::class, orphanRemoval: true)]
    private Collection $status;

    public function __construct()
    {
        $this->status = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteAdress(): ?string
    {
        return $this->siteAdress;
    }

    public function setSiteAdress(string $siteAdress): self
    {
        $this->siteAdress = $siteAdress;

        return $this;
    }

    /**
     * @return Collection<int, Status>
     */
    public function getStatus(): Collection
    {
        return $this->status;
    }

    public function addStatus(Status $status): self
    {
        if (!$this->status->contains($status)) {
            $this->status->add($status);
            $status->setSite($this);
        }

        return $this;
    }

    public function removeStatus(Status $status): self
    {
        if ($this->status->removeElement($status)) {
            // set the owning side to null (unless already changed)
            if ($status->getSite() === $this) {
                $status->setSite(null);
            }
        }

        return $this;
    }
}
