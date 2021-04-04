<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Lastname;

    /**
     * @ORM\OneToMany(targetEntity=RescueOP::class, mappedBy="creator", cascade={"remove"} )
     */
    private $rescueOPs;

    /**
     * @ORM\OneToMany(targetEntity=Standby::class, mappedBy="User", orphanRemoval=true, cascade={"remove"} )
     */
    private $standbies;

    /**
     * @ORM\ManyToOne(targetEntity=UserType::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $UserType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    public function __construct()
    {
        $this->rescueOPs = new ArrayCollection();
        $this->standbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->Firstname;
    }

    public function setFirstname(?string $Firstname): self
    {
        $this->Firstname = $Firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->Lastname;
    }

    public function setLastname(?string $Lastname): self
    {
        $this->Lastname = $Lastname;

        return $this;
    }

    /**
     * @return Collection|RescueOP[]
     */
    public function getRescueOPs(): Collection
    {
        return $this->rescueOPs;
    }

    public function addRescueOP(RescueOP $rescueOP): self
    {
        if (!$this->rescueOPs->contains($rescueOP)) {
            $this->rescueOPs[] = $rescueOP;
            $rescueOP->setCreator($this);
        }

        return $this;
    }

    public function removeRescueOP(RescueOP $rescueOP): self
    {
        if ($this->rescueOPs->removeElement($rescueOP)) {
            // set the owning side to null (unless already changed)
            if ($rescueOP->getCreator() === $this) {
                $rescueOP->setCreator(null);
            }
        }

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
            $standby->setUser($this);
        }

        return $this;
    }

    public function removeStandby(Standby $standby): self
    {
        if ($this->standbies->removeElement($standby)) {
            // set the owning side to null (unless already changed)
            if ($standby->getUser() === $this) {
                $standby->setUser(null);
            }
        }

        return $this;
    }

    public function getUserType(): ?UserType
    {
        return $this->UserType;
    }

    public function setUserType(?UserType $UserType): self
    {
        $this->UserType = $UserType;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

}
