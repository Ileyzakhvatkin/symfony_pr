<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imgUrl = null;

    #[ORM\Column(length: 255)]
//    #[Assert\Image(
//        minWidth: 600,
//        maxWidth: 1200,
//        maxHeight: 600,
//        minHeight: 1200,
//        maxWidthMessage: "Изображение должно быть менее 1200px по ширине",
//        minWidthMessage: "Изображение должно быть более 600px по ширине",
//        maxHeightMessage: "Изображение должно быть менее 1200px по высоте",
//        minHeightMessage: "Изображение должно быть более 600px по высоте",
//    )]
    #[Assert\File(
        maxSize: '2M',
        maxSizeMessage: 'Пожалуйста, загрузите картинку размером до 2M',
        extensions: ['jpg', 'png'],
        extensionsMessage: 'Пожалуйста, загрузите картинку типа JPG или PNG',
    )]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(File $image = null): static
    {
        $this->image = $image;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

}
