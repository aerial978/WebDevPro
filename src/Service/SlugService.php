<?php

namespace src\Service;

class SlugService
{
    /*public function generateSlug(string $string): string
    {
        // Nettoie la chaîne, converti en minuscules et remplace les espaces par des tirets
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));

        return $slug;
    }*/

    public function generateSlug($slug, string $divider = '-')
    {
        // replace non letter or digits by divider
        $slug = preg_replace('~[^\pL\d]+~u', $divider, $slug);

        if (class_exists('Normalizer')) {
            $slug = \Normalizer::normalize($slug, \Normalizer::FORM_D);  // Forme décomposée
            $slug = preg_replace('/[\pM]/u', '', $slug);  // Supprime les marques diacritiques (accents)
        }

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $slug);

        // trim
        $slug = trim($slug, $divider);

        // remove duplicate divider
        $slug = preg_replace('~-+~', $divider, $slug);

        // lowercase
        $slug = strtolower($slug);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }
}
