<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\KeywordRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class KeywordExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('keyword', [KeywordRuntime::class, 'doSomething']),
        ];
    }
}
