<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ImageSrcRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageSrcExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('imageSrc', [ImageSrcRuntime::class, 'doSomething']),
        ];
    }
}
