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
                if ($randPoz === 0) $randPoz = 1;
                if ($randPoz === count($parApp)) $randPoz = count($parApp) - 1;
                $paragraph = implode(' ', array_merge(array_slice($parApp, 0, $randPoz),
                        ['<strong>' . $article->getKeyword()[0] . '</strong>'],
                        array_slice($parApp, $randPoz, count($parApp) - 1))
                );
            }
        }

        $size = $article->getSize();
        $size = $article->getMaxSize() ? rand($article->getSize(), $article->getMaxSize())  : $size;
        $newContent = '';
        foreach ($contentParArr as $el) {
            if (strlen($newContent) === 0 ) {
                $newContent = $el;
            } elseif (strlen($newContent) <= $size) {
                $newContent = $newContent . '. ' . $el;
            }
        }
        $newContent = $newContent . '.';

        foreach ($article->getWords() as $word) {
            for ($i = 1; $i <= $word->getCount(); $i++) {
                $contentArr = explode(' ', $newContent);
                $randPoz = array_rand($contentArr);
                if ($randPoz === 0) $randPoz = 1;
                if ($randPoz === count($contentArr)) $randPoz = count($contentArr) - 1;
                $newContent = implode(' ', array_merge(array_slice($contentArr, 0, $randPoz),
                        ['<em>' . $word->getTitle() . '</em>'],
                        array_slice($contentArr, $randPoz, count($contentArr) - 1))
                );
            }
        }

        return $newContent;
    }
}