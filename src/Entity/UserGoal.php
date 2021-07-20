<?php

namespace App\Entity;

use App\Repository\UserGoalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserGoalRepository::class)
 */
class UserGoal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $achiever;

    /**
     * @ORM\ManyToOne(targetEntity=Goal::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $goal;

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

    public function getGoal(): ?Goal
    {
        return $this->goal;
    }

    public function setGoal(?Goal $goal): self
    {
        $this->goal = $goal;

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
