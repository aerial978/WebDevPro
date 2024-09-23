<?php

namespace src\service;

class TagCloudService
{
    /**
     * Calcule la taille des tags pour un nuage de tags.
     *
     * @param array $tags Un tableau associatif où les clés sont les noms des tags et les valeurs sont les fréquences.
     * @param int $minFontSize La taille de police minimale pour les tags.
     * @param int $maxFontSize La taille de police maximale pour les tags.
     * @return array Un tableau associatif où les clés sont les noms des tags et les valeurs sont les tailles de police.
     */
    public function calculateTagSizes($tags)
    {
        // Définir les intervalles de fréquences et les tailles de police correspondantes
        $sizeMap = [
            [0, 1, 15],   // de 0 à 1 fréquence => taille de 15px
            [2, 3, 20],   // de 2 à 3 fréquences => taille de 20px
            [4, 10, 24],  // de 4 à 10 fréquences => taille de 24px
            [11, 50, 28], // de 11 à 50 fréquences => taille de 28px
            [51, PHP_INT_MAX, 32] // au-delà de 50 fréquences => taille de 32px
        ];

        $tagSizes = [];

        foreach ($tags as $tag) {
            $frequency = $tag['frequency'];  // Prendre en compte la fréquence du tag
            foreach ($sizeMap as $sizeRange) {
                if ($frequency >= $sizeRange[0] && $frequency <= $sizeRange[1]) {
                    // Utiliser le nom du tag comme clé et attribuer la taille
                    $tagSizes[$tag['name_tag']] = $sizeRange[2];
                    break;
                }
            }
        }

        return $tagSizes;
    }

}
