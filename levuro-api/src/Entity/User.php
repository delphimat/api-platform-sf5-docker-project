<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\RegisterController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'register' => [
            'method' => 'post',
            'path' => '/register',
            'controller' => RegisterController::class,
            'normalization_context' => ['groups' => ['user:view']],
            'denormalization_context' => [ 'groups' => ['user:create']],
            'write' => false
        ]
    ],
    itemOperations: [
        'login' => [
            'method' => 'get',
        ]
    ],
    normalizationContext: ['groups' => ['user:read']],
)]
class User implements UserInterface, JWTUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[
        Groups(['user:view'])
    ]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[
        Length(
            min: 4,
            max: 255,
            minMessage: 'Your username must be at least {{ limit }} characters long',
            maxMessage: 'Your username cannot be longer than {{ limit }} characters',
        ),
        Groups(['user:view', 'user:create'])
    ]
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[
        Groups(['user:view'])
    ]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    #[
        Length(
            min: 4,
            max: 255,
            minMessage: 'Your password must be at least {{ limit }} characters long',
            maxMessage: 'Your password cannot be longer than {{ limit }} characters',
        ),
        Groups(['user:create'])
    ]
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="user")
     */
    private Collection $tasks;

    public function __construct()
    {
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
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    /**
     * @param array $roles
     * @return $this
     */
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
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
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

    /**
     * @param $id
     * @param array $payload
     * @return User
     */
    public static function createFromPayload($id, array $payload): User
    {
        return (new User())->setId($id)->setUsername($payload['username'] ?? '');
    }

    /**
     * @return Collection
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param $tasks
     * @return $this
     */
    public function setTasks($tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param ?string $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
