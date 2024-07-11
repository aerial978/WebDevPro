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
            [0, 1, 15],  // de 0 à 10 fréquences => taille de 12px
            [2, 3, 20], // de 11 à 20 fréquences => taille de 16px
            [21, 30, 20], // de 21 à 30 fréquences => taille de 20px
            [31, 50, 24], // de 31 à 50 fréquences => taille de 24px
            [51, PHP_INT_MAX, 28] // au-delà de 50 fréquences => taille de 28px
        ];

        $tagSizes = [];

        foreach ($tags as $tag => $freq) {
            foreach ($sizeMap as $sizeRange) {
                if ($freq >= $sizeRange[0] && $freq <= $sizeRange[1]) {
                    $tagSizes[$tag] = $sizeRange[2];
                    break;
                }
            }
        }

        return $tagSizes;
    }
}
