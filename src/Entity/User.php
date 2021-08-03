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
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=UserClub::class, mappedBy="member", orphanRemoval=true)
     */
    private $userClubs;

    /**
     * @ORM\OneToMany(targetEntity=UserMilestone::class, mappedBy="achiever", orphanRemoval=true)
     */
    private $userMilestones;

    public function __construct()
    {
        $this->userClubs = new ArrayCollection();
        $this->userMilestones = new ArrayCollection();
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
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return Collection|UserClub[]
     */
    public function getUserClubs(): Collection
    {
        return $this->userClubs;
    }

    public function addUserClub(UserClub $userClub): self
    {
        if (!$this->userClubs->contains($userClub)) {
            $this->userClubs[] = $userClub;
            $userClub->setMember($this);
        }

        return $this;
    }

    public function removeUserClub(UserClub $userClub): self
    {
        if ($this->userClubs->removeElement($userClub)) {
            // set the owning side to null (unless already changed)
            if ($userClub->getMember() === $this) {
                $userClub->setMember(null);
            }
        }

        return $this;
    }

    public function isMember(Club $club): bool
    {
        return !$this->getUserClubs()->forAll(function($key, $userClub) use ($club) {
            return $userClub->getClub() !== $club;
        });
    }

    /**
     * @return Collection|UserMilestone[]
     */
    public function getUserMilestones(): Collection
    {
        return $this->userMilestones;
    }

    public function addUserMilestone(UserMilestone $userMilestone): self
    {
        if (!$this->userMilestones->contains($userMilestone)) {
            $this->userMilestones[] = $userMilestone;
            $userMilestone->setAchiever($this);
        }

        return $this;
    }

    public function removeUserMilestone(UserMilestone $userMilestone): self
    {
        if ($this->userMilestones->removeElement($userMilestone)) {
            // set the owning side to null (unless already changed)
            if ($userMilestone->getAchiever() === $this) {
                $userMilestone->setAchiever(null);
            }
        }

        return $this;
    }
}
