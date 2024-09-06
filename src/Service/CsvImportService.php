<?php

namespace src\Service;

use PDO;

class CsvImportService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function importCsv(string $csvFile, string $tableName)
    {
        // Vide la table
        $this->pdo->exec("DELETE FROM $tableName");

        // Lecture du fichier CSV
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $header = fgetcsv($handle, 2000, ';'); // Lecture de l'en-tête

            // Préparation de l'insertion des données
            $placeholders = implode(',', array_fill(0, count($header), '?'));
            $sql = "INSERT INTO $tableName (" . implode(',', $header) . ") VALUES ($placeholders)";
            $query = $this->pdo->prepare($sql);

            // Lecture des données et insertion dans la table
            while (($data = fgetcsv($handle, 2000, ';')) !== false) {
                // Vérification du nombre de valeurs correspondant au nombre de colonnes
                if (count($data) === count($header)) {
                    // Application des traitements spécifiques aux tables
                    $data = $this->transformData($data, $tableName, $header);
                    $query->execute($data);
                } else {
                    throw new \Exception("Le nombre de valeurs ne correspond pas au nombre de colonnes dans le fichier CSV.");
                }
            }

            fclose($handle);
        }

        return "Importation terminée avec succès pour la table $tableName !";
    }

    private function transformData(array $data, string $tableName, array $header)
    {
        // Remplacement des valeurs vides par NULL
        foreach ($data as $index => $value) {
            if ($value === '') {
                $data[$index] = null;
            }
        }

        // Traitement du password de la table 'user'
        if ($tableName === 'user') {
            $passwordIndex = array_search('password', $header);
            if ($passwordIndex !== false) {
                $data[$passwordIndex] = password_hash($data[$passwordIndex], PASSWORD_ARGON2I);
            }
        }

        return $data;
    }
}
