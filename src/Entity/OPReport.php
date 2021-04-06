<?php

namespace App\Entity;

use App\Repository\OPReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OPReportRepository::class)
 */
class OPReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=RescueOP::class, inversedBy="oPReport", cascade={"persist", "remove"})
     */
    private $Operation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ReportText;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Rating;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private $Operators;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $OPStart;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $OPEnd;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reportClosed;

    public function __construct()
    {
        $this->Operators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperation(): ?RescueOP
    {
        return $this->Operation;
    }

    public function setOperation(?RescueOP $Operation): self
    {
        $this->Operation = $Operation;

        return $this;
    }

    public function getReportText(): ?string
    {
        return $this->ReportText;
    }

    public function setReportText(?string $ReportText): self
    {
        $this->ReportText = $ReportText;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->Rating;
    }

    public function setRating(?int $Rating): self
    {
        $this->Rating = $Rating;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getOperators(): Collection
    {
        return $this->Operators;
    }

    public function addOperator(User $operator): self
    {
        if (!$this->Operators->contains($operator)) {
            $this->Operators[] = $operator;
        }

        return $this;
    }

    public function removeOperator(User $operator): self
    {
        $this->Operators->removeElement($operator);

        return $this;
    }

    public function getOPStart(): ?\DateTimeInterface
    {
        return $this->OPStart;
    }

    public function setOPStart(?\DateTimeInterface $OPStart): self
    {
        $this->OPStart = $OPStart;

        return $this;
    }

    public function getOPEnd(): ?\DateTimeInterface
    {
        return $this->OPEnd;
    }

    public function setOPEnd(?\DateTimeInterface $OPEnd): self
    {
        $this->OPEnd = $OPEnd;

        return $this;
    }

    public function getReportClosed(): ?bool
    {
        return $this->reportClosed;
    }

    public function setReportClosed(bool $reportClosed): self
    {
        $this->reportClosed = $reportClosed;

        return $this;
    }
}
