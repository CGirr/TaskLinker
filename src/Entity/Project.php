<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 */
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'projects')]
    private Collection $members;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project')]
    private Collection $tasks;

    /**
     * @var bool|null
     */
    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $archived = false;

    /**
     *
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @param Employee $member
     * @return $this
     */
    public function addMember(Employee $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    /**
     * @param Employee $member
     * @return $this
     */
    public function removeMember(Employee $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     * @return $this
     */
    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }
}
