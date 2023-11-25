<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Пользователь с таким email уже зарегистрирован' )]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('main')]
    #[Assert\NotBlank(message: "Заполните имя")]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups('main')]
    #[Assert\NotBlank(message: "Заполните email")]
    #[Assert\Email(message: "Неверный формат email")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Задайте пароль")]
    #[Assert\Length(min: 6, minMessage: "Пароль должен быть не меньше 6 символов")]
    private ?string $password1 = null;

    #[Assert\NotBlank(message: "Повторите пароль")]
    #[Assert\Length(min: 6, minMessage: "Пароль должен быть не меньше 6 символов")]
    private ?string $password2 = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Module::class)]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ApiToken::class, orphanRemoval: true)]
    private Collection $apiTokens;

    #[ORM\Column(length: 255)]
    private ?string $regLink = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setUser($this);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getUser() === $this) {
                $module->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }

    public function getRegLink(): ?string
    {
        return $this->regLink;
    }

    public function setRegLink(string $regLink): static
    {
        $this->regLink = $regLink;

        return $this;
    }

    public function getPassword1(): string|null
    {
        return $this->password1;
    }

    public function setPassword1(string $password1): static
    {
        $this->password1 = $password1;

        return $this;
    }

    public function getPassword2(): string|null
    {
        return $this->password2;
    }

    public function setPassword2(string $password2): static
    {
        $this->password2 = $password2;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ( $this->getPassword1() && $this->getPassword2() && $this->getPassword1() !== $this->getPassword2()) {
            $context->buildViolation('Пароли не совпадают')
                ->atPath('password')
                ->addViolation()
            ;
        }
    }
}
