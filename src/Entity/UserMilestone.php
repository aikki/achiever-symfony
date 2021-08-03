<?php

namespace App\Entity;

use App\Repository\UserMilestoneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserMilestoneRepository::class)
 */
class UserMilestone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userMilestones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $achiever;

    /**
     * @ORM\ManyToOne(targetEntity=Milestone::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $milestone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAchieved;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAchiever(): ?User
    {
        return $this->achiever;
    }

    public function setAchiever(?User $achiever): self
    {
        $this->achiever = $achiever;

        return $this;
    }

    public function getMilestone(): ?Milestone
    {
        return $this->milestone;
    }

    public function setMilestone(?Milestone $milestone): self
    {
        $this->milestone = $milestone;

        return $this;
    }

    public function getIsAchieved(): ?bool
    {
        return $this->isAchieved;
    }

    public function setIsAchieved(bool $isAchieved): self
    {
        $this->isAchieved = $isAchieved;

        return $this;
    }
}
