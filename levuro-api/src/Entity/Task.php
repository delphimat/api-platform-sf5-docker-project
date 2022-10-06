<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'post'  => [
            'normalization_context' => ['groups' => ['task:view']],
            'denormalization_context' => [ 'groups' => ['task:create']]
        ],
        'get'
    ],
    itemOperations: [
        'patch' => [
            'normalization_context' => ['groups' => ['task:view']],
            'denormalization_context' => [ 'groups' => ['task:update']],
        ],
        'get' => [
            'normalization_context' => ['groups' => ['task:view']],
        ]
    ],
    security: 'is_granted("ROLE_USER")'
)]
class Task implements UserOwnedInterface
{
    public const ALLOWED_STATUS = [
        self::STATUS_TODO,
        self::STATUS_IN_PROGRESS,
        self::STATUS_DONE
    ];

    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'progress';
    public const STATUS_DONE = 'done';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['task:view'])]
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank(groups: ['task:create'])]
    #[Assert\Length(max: 255, groups: ["task:create", "task:update"])]
    #[Groups(['task:view', 'task:create', 'task:update'])]
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\Length(max: 255)]
    #[Assert\Choice(choices: Task::ALLOWED_STATUS, message: 'Choose a valid status.')]
    #[Groups(['task:view', 'task:update'])]
    private string $status = self::STATUS_TODO;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks", cascade={"persist"})
     */
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param ?User $user
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
