<?php

namespace App\Services;

use App\Entity\Article;
use App\Services\Constants\RussianText;

class ArticleTextGenerator
{
    public function createText(Article $article): string
    {
        $content = '';

        switch ($article->getTheme()) {
            case 'PHP':
                $content = RussianText::getPHPText();
                break;
            case 'JS':
                $content = RussianText::getJSText();
                break;
            case "FOOD":
                $content = RussianText::getFOODText();
                break;
        }

        $contentParArr = explode('. ', $content);
        foreach ($contentParArr as &$paragraph) {
            if (rand(1,3) > 2) {
                $parApp = explode(' ', $paragraph);
                $randPoz = array_rand($parApp);
                $randPoz = 0 ? 1 : $randPoz;
                $paragraph = implode(' ', array_merge(array_slice($parApp, 0, $randPoz),
                        ['<strong>' . $article->getKeyword()[0] . '</strong>'],
                        array_slice($parApp, $randPoz, count($parApp) - 1))
                );
            }
        }
        $content = implode('. ', $contentParArr);


        if ($article->getMaxSize()) {
            $content = mb_strimwidth($content, 0, rand($article->getSize(), $article->getMaxSize()), '...');
        } else {
            $content = mb_strimwidth($content, 0, $article->getSize(), '...');
        }

        foreach ($article->getWords() as $word) {
            for ($i = 1; $i <= $word->getCount(); $i++) {
                $contentArr = explode(' ', $content);
                $randPoz = array_rand($contentArr);
                $randPoz = 0 ? 1 : $randPoz;

                $content = implode(' ', array_merge(array_slice($contentArr, 0, $randPoz),
                        ['<em>' . $word->getTitle() . '</em>'],
                        array_slice($contentArr, $randPoz, count($contentArr) - 1))
                );
            }
        }

        return $content;
    }
}