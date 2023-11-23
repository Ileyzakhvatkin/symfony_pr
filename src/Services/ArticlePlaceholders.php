<?php

namespace App\Services;

use App\Entity\Article;
use Twig\Environment;

class ArticlePlaceholders
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function create(Article $article = null): array
    {
        if ($article) {
            $texts = [];
            $parApp = explode('. ', $article->getContent());
            for ($i = 1; $i <= 3; $i++) {
                $subParrArr = [];
                for ($k = 0; $k <= 3; $k++) {
                    if ($i*$k < count($parApp)) {
                        $subParrArr[] = $parApp[$i*$k];
                    }
                }
                $texts[] = implode(' ', $subParrArr);
            }

            $sameImage = count($article->getImages()) > 0
                ? $this->twig->render('placeholders/imageSrc.html.twig', [
                    'sameImage' => $article->getImages()[rand(0, count($article->getImages()) - 1)]->getImgUrl(),
                ]) : $this->twig->render('placeholders/imageSrcNull.html.twig');

            return [
                'title' => $article->getTitle(),
                'theme' => $article->getTheme(),
                'keyword' =>  $article->getKeyword()[0],
                'paragraph' => $article->getContent(),
                'paragraphs' => $this->twig->render('placeholders/paragraphs.html.twig', [ 'texts' => $texts ]),
                'imageSrc' => $sameImage,
            ];
        }


        return [];
    }
}