<?php

namespace App\Services;

use App\Entity\Article;

class ContentGenerator
{
    public function createText(Article $article): string
    {
        return $article->getKeyword()[rand(0,6)] . ' СГЕНЕРИРОВАННЫЙ ТЕКСТ СТАТЬИ СГЕНЕРИРОВАННЫЙ ТЕКСТ СТАТЬИ СГЕНЕРИРОВАННЫЙ ТЕКСТ СТАТЬИ СГЕНЕРИРОВАННЫЙ ТЕКСТ СТАТЬИ';
    }
}