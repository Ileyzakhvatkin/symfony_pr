<?php

namespace App\DataFixtures;

use App\Entity\Word;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;

class UserWordFixtures extends BaseFixtures
{
    private ArticleRepository $articleRepository;

    public function __construct( ArticleRepository $articleRepository)
    {
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
