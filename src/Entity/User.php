<?php

namespace App\Entity;

use App\Repository\UserRepository;

use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Core\Annotation\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\{PasswordAuthenticatedUserInterface, UserInterface};
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['user.read']],
    denormalizationContext: ['groups' => ['user.write']],
    collectionOperations: ['GET']
)]
#[UniqueEntity(
    'email',
    message: 'The email {{ value }} is not already taken.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(["user.read", "user.write"])]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.'
    )]
    // #[Assert\Unique(
    //     message: 'The email {{ value }} is already taken.'
    // )]
    private string $email;

    #[ORM\Column(type: 'json')]
    #[Groups(["user.read", "user.write"])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(["user.write"])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: 'Your password must be at least {{ limit }} characters long',
        maxMessage: 'Your password cannot be longer than {{ limit }} characters',
    )]
    private string $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return (string) $this->email;
    }

    public function getUsername(): ?string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
