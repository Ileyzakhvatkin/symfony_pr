<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\MorthRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MorthExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('morth', [MorthRuntime::class, 'doSomething']),
        ];
    }

}
