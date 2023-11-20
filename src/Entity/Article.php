<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Validator\CheckRusNounArr;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Выберете тему")]
    private ?string $theme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column]
    private array $keyword = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: "Задайте минимальный размер")]
    #[Assert\GreaterThan(value: 999, message: "Объем стать должен быть не менее 1000 символов" )]
    #[Assert\LessThan(value: 5001, message: "Объем стать должен быть не более 5000 символов")]
    private ?int $size = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(value: 999, message: "Объем стать должен быть не менее 1000 символов" )]
    #[Assert\LessThan(value: 5009, message: "Объем стать должен быть не более 5000 символов")]
    private ?int $maxsize = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Module $module = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Word::class, cascade: ['persist', 'remove'] )]
    private Collection $words;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Image::class)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->words = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getKeyword(): array
    {
        return $this->keyword;
    }

    public function setKeyword(array $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMaxSize(): ?int
    {
        return $this->maxsize;
    }

    public function setMaxSize(int $maxsize): static
    {
        $this->maxsize = $maxsize;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setArticle($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getArticle() === $this) {
                $image->setArticle(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return Collection<int, Word>
     */
    public function getWords(): Collection
    {
        return $this->words;
    }

    public function addWord(Word $word): static
    {
        if (!$this->words->contains($word)) {
            $this->words->add($word);
            $word->setArticle($this);
        }

        return $this;
    }

    public function removeWord(Word $word): static
    {
        if ($this->words->removeElement($word)) {
            // set the owning side to null (unless already changed)
            if ($word->getArticle() === $this) {
                $word->setArticle(null);
            }
        }

        return $this;
    }
}
