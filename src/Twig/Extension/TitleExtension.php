<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TitleRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TitleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('title', [TitleRuntime::class, 'doSomething']),
        ];
    }
}
