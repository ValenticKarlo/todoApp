<?php

namespace App\Entity;

use App\Repository\ListEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListEntityRepository::class)]
class ListEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $totalTasks = null;

    #[ORM\Column]
    private ?int $completedTasks = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
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

    public function getTotalTasks(): ?int
    {
        return $this->totalTasks;
    }

    public function setTotalTasks(int $totalTasks): self
    {
        $this->totalTasks = $totalTasks;

        return $this;
    }

    public function getCompletedTasks(): ?int
    {
        return $this->completedTasks;
    }

    public function setCompletedTasks(int $completedTasks): self
    {
        $this->completedTasks = $completedTasks;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
