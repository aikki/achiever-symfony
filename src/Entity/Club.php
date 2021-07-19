<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ClubRepository;

/**
 * @ORM\Entity(repositoryClass=ClubRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Club
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublic;

    /**
     * @ORM\OneToMany(targetEntity=Goal::class, mappedBy="club", orphanRemoval=true)
     */
    private $goals;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=UserClub::class, mappedBy="club", orphanRemoval=true)
     */
    private $userClubs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $joinCode;

    public function __construct()
    {
        $this->goals = new ArrayCollection();
        $this->userClubs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection|Goal[]
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    public function addGoal(Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setClub($this);
        }

        return $this;
    }

    public function removeGoal(Goal $goal): self
    {
        if ($this->goals->removeElement($goal)) {
            // set the owning side to null (unless already changed)
            if ($goal->getClub() === $this) {
                $goal->setClub(null);
            }
        }

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
            $userClub->setClub($this);
        }

        return $this;
    }

    public function removeUserClub(UserClub $userClub): self
    {
        if ($this->userClubs->removeElement($userClub)) {
            // set the owning side to null (unless already changed)
            if ($userClub->getClub() === $this) {
                $userClub->setClub(null);
            }
        }

        return $this;
    }

    public function getJoinCode(): ?string
    {
        return $this->joinCode;
    }

    public function setJoinCode(?string $joinCode): self
    {
        $this->joinCode = $joinCode;

        return $this;
    }
}
