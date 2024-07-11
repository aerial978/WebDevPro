<?php

namespace src\Service;

class PostService
{
    /**
     * Extrait un extrait du contenu autour du terme de recherche.
     *
     * @param string $content Le contenu complet.
     * @param string $search Le terme de recherche.
     * @return string L'extrait de contenu.
     */
    public function getExcerpt($content, $search)
    {
        $length = 200; // Longueur de l'extrait
        $search = strtolower($search); // Conversion du terme de recherche en minuscule
        $contentLower = strtolower($content); // Conversion du contenu en minuscule
        $position = stripos($contentLower, $search); // Recherche insensible à la casse

        if ($position !== false) {
            $start = max(0, $position - ($length / 2));
            $excerpt = substr($content, $start, $length); // Utilisation du contenu original pour l'extrait
            return '...' . $excerpt . '...';
        } else {
            return substr($content, 0, $length) . '...';
        }
    }
}
