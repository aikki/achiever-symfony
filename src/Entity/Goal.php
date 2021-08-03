<?php

namespace App\Entity;

use App\Repository\GoalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoalRepository::class)
 */
class Goal
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
     * @ORM\ManyToOne(targetEntity=Club::class, inversedBy="goals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $club;

    private $isAchieved = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iconClassName;

    /**
     * @ORM\ManyToMany(targetEntity=Milestone::class, mappedBy="goals")
     */
    private $milestones;

    private $isLocked;

    public function __construct()
    {
        $this->milestones = new ArrayCollection();
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

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getIsAchieved(): ?bool
    {
        return $this->isAchieved;
    }

    public function setIsAchieved(?bool $isAchieved): self
    {
        $this->isAchieved = $isAchieved;

        return $this;
    }

    public function getIconClassName(): ?string
    {
        return $this->iconClassName;
    }

    public function setIconClassName(string $iconClassName): self
    {
        $this->iconClassName = $iconClassName;

        return $this;
    }

    /**
     * @return Collection|Milestone[]
     */
    public function getMilestones(): Collection
    {
        return $this->milestones;
    }

    public function addMilestone(Milestone $milestone): self
    {
        if (!$this->milestones->contains($milestone)) {
            $this->milestones[] = $milestone;
            $milestone->addGoal($this);
        }

        return $this;
    }

    public function removeMilestone(Milestone $milestone): self
    {
        if ($this->milestones->removeElement($milestone)) {
            $milestone->removeGoal($this);
        }

        return $this;
    }

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }
}
