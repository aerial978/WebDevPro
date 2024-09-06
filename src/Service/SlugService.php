<?php

namespace src\Service;

class SlugService
{
    public function generateSlug(string $string): string
    {
        // Nettoyer la chaîne, convertir en minuscules, remplacer les espaces par des tirets
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
        return $slug;
    }
}
