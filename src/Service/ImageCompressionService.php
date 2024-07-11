<?php

namespace src\Service;

/**
 * Service pour compresser une image avec GD.
 */
class ImageCompressionService
{
    /**
     * Compresser une image avec GD.
     * @param string $source Chemin de l'image source.
     * @param string $destination Chemin de l'image de destination.
     * @param int $quality Qualité de compression de l'image (de 0 à 100).
     */
    public static function compressImage($image_tmp_name, $upload_path, $filename)
    {
        $quality = 75;

        // Récupérer les dimensions de l'image
        list($width, $height) = getimagesize($image_tmp_name);

        // Créer une image à partir du fichier uploadé
        $source = imagecreatefromstring(file_get_contents($image_tmp_name));

        // Créer une nouvelle image avec les dimensions réduites
        $newWidth = $width; // Modifier la largeur de l'image selon vos besoins
        $newHeight = $height; // Modifier la hauteur de l'image selon vos besoins
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Copier et redimensionner l'image originale vers la nouvelle image
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Enregistrer l'image compressée dans le dossier d'upload
        $compressedImagePath = $upload_path . $filename;

        // Enregistrer l'image compressée au format JPEG avec la qualité définie
        imagejpeg($destination, $compressedImagePath, $quality);

        // Libérer la mémoire
        imagedestroy($source);
        imagedestroy($destination);

        // Retourner le chemin de l'image compressée
        return $compressedImagePath;
    }
}
