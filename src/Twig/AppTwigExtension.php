<?php

namespace src\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppTwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('truncate_words', [$this, 'truncateWords']),
        ];
    }

    public function truncateWords($text, $limit = 50, $ellipsis = '...')
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . $ellipsis;
        }

        return $text;
    }
}
