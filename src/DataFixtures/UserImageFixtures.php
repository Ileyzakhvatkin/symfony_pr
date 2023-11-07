<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Repository\ArticleRepository;
use Carbon\Carbon;
use Doctrine\Persistence\ObjectManager;

class UserImageFixtures extends BaseFixtures
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Image::class, 200, function (Image $word) {
            $word
                ->setArticle($this->articleRepository->find(rand(1, 75)))
                ->setImgUrl('demo/img-' . rand(1, 20) . '.jpg')
            ;
        });
    }
}
