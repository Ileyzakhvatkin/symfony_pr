<?php

namespace App\Twig\Runtime;

use App\Entity\Article;
use Twig\Extension\RuntimeExtensionInterface;

class ParagraphRuntime implements RuntimeExtensionInterface
{
    private Article $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function doSomething($value)
    {
        return $this->article->getContent();
    }
}
