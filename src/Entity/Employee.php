<?php

namespace App\Entity;

use App\Enum\EmployeeStatus;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[UniqueEntity('email')]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface
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
    #[Assert\Type('string')]
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Assert\Email(message: 'Cet email n\'est pas valide')]
    #[ORM\Column(name: 'email', length: 255, unique: true)]
    private ?string $email = null;

    /**
     * @var EmployeeStatus|null
     */
    #[Assert\NotNull]
    #[ORM\Column(enumType: EmployeeStatus::class)]
    private ?EmployeeStatus $status = null;

    /**
     * @var \DateTimeImmutable|null
     */
    #[Assert\NotNull]
    #[ORM\Column]
    private ?\DateTimeImmutable $entry_date = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'member')]
    private Collection $tasks;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'members')]
    private Collection $projects;

    /**
     * @var string|null
     */
    #[Assert\NotNull]
    #[ORM\Column(length: 255, nullable: true, options: ['default' => null])]
    private ?string $password = null;

    /**
     * @var array
     */
    #[ORM\Column(type: Types::JSON, options: ['default' => '["ROLE_USER"]'])]
    private array $roles = [];

    /**
     *
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->projects = new ArrayCollection();
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
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return EmployeeStatus|null
     */
    public function getStatus(): ?EmployeeStatus
    {
        return $this->status;
    }

    /**
     * @param EmployeeStatus $status
     * @return $this
     */
    public function setStatus(EmployeeStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEntryDate(): ?\DateTimeImmutable
    {
        return $this->entry_date;
    }

    /**
     * @param \DateTimeImmutable $entry_date
     * @return $this
     */
    public function setEntryDate(\DateTimeImmutable $entry_date): static
    {
        $this->entry_date = $entry_date;

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
            $task->setMember($this);
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
            if ($task->getMember() === $this) {
                $task->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addMember($this);
        }

        return $this;
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeMember($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
