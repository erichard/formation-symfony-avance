<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('price', [$this, 'formatPrice'], ['is_safe' => ['html']]),
        ];
    }

    public function formatPrice($value)
    {
        return number_format($value / 100, 2, ',', ' ') . '&nbsp;€';
    }
}
