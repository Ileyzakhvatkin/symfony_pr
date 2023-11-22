<?php

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class MorthRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function showMorth($value)
    {
        // ...
    }
}
