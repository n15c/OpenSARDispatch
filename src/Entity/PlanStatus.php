<?php

namespace App\Entity;

use App\Repository\PlanStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanStatusRepository::class)
 */
class PlanStatus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statusName;

    /**
     * @ORM\OneToMany(targetEntity=Standby::class, mappedBy="status")
     */
    private $standbies;

    public function __construct()
    {
        $this->standbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusName(): ?string
    {
        return $this->statusName;
    }

    public function setStatusName(string $statusName): self
    {
        $this->statusName = $statusName;

        return $this;
    }

    /**
     * @return Collection|Standby[]
     */
    public function getStandbies(): Collection
    {
        return $this->standbies;
    }

    public function addStandby(Standby $standby): self
    {
        if (!$this->standbies->contains($standby)) {
            $this->standbies[] = $standby;
            $standby->setStatus($this);
        }

        return $this;
    }

    public function removeStandby(Standby $standby): self
    {
        if ($this->standbies->removeElement($standby)) {
            // set the owning side to null (unless already changed)
            if ($standby->getStatus() === $this) {
                $standby->setStatus(null);
            }
        }

        return $this;
    }
}
