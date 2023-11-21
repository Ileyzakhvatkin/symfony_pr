<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ParagraphsRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParagraphsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paragraph', [ParagraphsRuntime::class, 'doSomething']),
        ];
    }
}
