<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ParagraphRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParagraphExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paragraph', [ParagraphRuntime::class, 'doSomething']),
        ];
    }
}
