<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\MorphRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MorphExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [MorphRuntime::class, 'showMorph']),
        ];
    }
}
