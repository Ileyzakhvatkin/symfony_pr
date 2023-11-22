<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [AppUploadedAsset::class, 'asset'])
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('morth', [MorthRuntime::class, 'showMorth']),
        ];
    }
}