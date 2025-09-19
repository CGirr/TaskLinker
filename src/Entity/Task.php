<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TaskStatus;

/**
 *
 */
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
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
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var string|null
     */
    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date = null;

    /**
     * @var TaskStatus|null
     */
    #[Assert\NotNull]
    #[ORM\Column(enumType: taskstatus::class)]
    private ?taskstatus $status = null;

    /**
     * @var Employee|null
     */
    #[Assert\Type(Employee::class)]
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Employee $member = null;

    /**
     * @var Project|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param \DateTimeImmutable|null $date
     * @return $this
     */
    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return TaskStatus|null
     */
    public function getStatus(): ?taskstatus
    {
        return $this->status;
    }

    /**
     * @param TaskStatus $status
     * @return $this
     */
    public function setStatus(taskstatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Employee|null
     */
    public function getMember(): ?Employee
    {
        return $this->member;
    }

    /**
     * @param Employee|null $member
     * @return $this
     */
    public function setMember(?Employee $member): static
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * @param Project|null $project
     * @return $this
     */
    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
