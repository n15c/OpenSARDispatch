<?php

namespace App\Entity;

use App\Repository\TravelExpenseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TravelExpenseRepository::class)
 */
class TravelExpense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="travelExpenses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $kilometers;

    /**
     * @ORM\ManyToOne(targetEntity=RescueOp::class, inversedBy="travelExpenses")
     */
    private $operation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getKilometers(): ?float
    {
        return $this->kilometers;
    }

    public function setKilometers(float $kilometers): self
    {
        $this->kilometers = $kilometers;

        return $this;
    }

    public function getOperation(): ?RescueOp
    {
        return $this->operation;
    }

    public function setOperation(?RescueOp $operation): self
    {
        $this->operation = $operation;

        return $this;
    }
}
