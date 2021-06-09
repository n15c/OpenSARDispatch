<?php

namespace App\Entity;

use App\Repository\RescueOPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RescueOPRepository::class)
 */
class RescueOP
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $Position = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rescueOPs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Comment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $plannedDate;

    /**
     * @ORM\OneToOne(targetEntity=OPReport::class, mappedBy="Operation", cascade={"persist", "remove"})
     */
    private $oPReport;

    /**
     * @ORM\Column(type="boolean")
     */
    private $opCompleted;

    /**
     * @ORM\OneToMany(targetEntity=TravelExpense::class, mappedBy="operation")
     */
    private $travelExpenses;

    public function __construct()
    {
        $this->travelExpenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?array
    {
        return $this->Position;
    }

    public function setPosition(array $Position): self
    {
        $this->Position = $Position;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->Comment;
    }

    public function setComment(?string $Comment): self
    {
        $this->Comment = $Comment;

        return $this;
    }

    public function getPlannedDate(): ?\DateTimeInterface
    {
        return $this->plannedDate;
    }

    public function setPlannedDate(?\DateTimeInterface $plannedDate): self
    {
        $this->plannedDate = $plannedDate;

        return $this;
    }

    public function getOPReport(): ?OPReport
    {
        return $this->oPReport;
    }

    public function setOPReport(?OPReport $oPReport): self
    {
        // unset the owning side of the relation if necessary
        if ($oPReport === null && $this->oPReport !== null) {
            $this->oPReport->setOperation(null);
        }

        // set the owning side of the relation if necessary
        if ($oPReport !== null && $oPReport->getOperation() !== $this) {
            $oPReport->setOperation($this);
        }

        $this->oPReport = $oPReport;

        return $this;
    }

    public function getOpCompleted(): ?bool
    {
        return $this->opCompleted;
    }

    public function setOpCompleted(bool $opCompleted): self
    {
        $this->opCompleted = $opCompleted;

        return $this;
    }

    /**
     * @return Collection|TravelExpense[]
     */
    public function getTravelExpenses(): Collection
    {
        return $this->travelExpenses;
    }

    public function addTravelExpense(TravelExpense $travelExpense): self
    {
        if (!$this->travelExpenses->contains($travelExpense)) {
            $this->travelExpenses[] = $travelExpense;
            $travelExpense->setOperation($this);
        }

        return $this;
    }

    public function removeTravelExpense(TravelExpense $travelExpense): self
    {
        if ($this->travelExpenses->removeElement($travelExpense)) {
            // set the owning side to null (unless already changed)
            if ($travelExpense->getOperation() === $this) {
                $travelExpense->setOperation(null);
            }
        }

        return $this;
    }
}
