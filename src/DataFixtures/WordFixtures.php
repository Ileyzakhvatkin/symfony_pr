<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Word;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class WordFixtures extends BaseFixtures
{
    private $passwordHasher;
    private ArticleRepository $articleRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ArticleRepository $articleRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->articleRepository = $articleRepository;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Word::class, 200, function (Word $word) {
            $word
                ->setArticle($this->articleRepository->find(rand(1, 75)))
                ->setTitle($this->faker->word)
                ->setCount($this->faker->numberBetween(2, 6))
            ;
        });
    }
}
