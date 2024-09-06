<?php

require 'vendor/autoload.php';

// Autoload pour le projet
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    $classFile = str_replace('\\', '/', $class) . '.php';
    $classFile = $baseDir . $classFile;

    require_once $classFile;
});

use src\Core\Db;
use src\Service\CsvImportService;

try {
    // Obtenir l'instance de la base de données
    $pdo = Db::getInstance();

    // Définir les fichiers CSV et les tables associées
    $csvFiles = [
        __DIR__ . '/data/posts.csv' => 'post',
        __DIR__ . '/data/users.csv' => 'user',
        __DIR__ . '/data/categories.csv' => 'category',
        __DIR__ . '/data/tags.csv' => 'tag',
        __DIR__ . '/data/posts_tags.csv' => 'post_tag',
        __DIR__ . '/data/roles.csv' => 'role',
        __DIR__ . '/data/comments.csv' => 'comment',
        __DIR__ . '/data/status.csv' => 'status',
    ];

    // Créer une instance de CsvImportService
    $csvImportService = new CsvImportService($pdo);

    foreach ($csvFiles as $csvFile => $tableName) {
        // Désactiver les contraintes de clé étrangère
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        
        // Importer les données CSV dans la table
        echo "Importing data from $csvFile into table $tableName...\n";
        echo $csvImportService->importCsv($csvFile, $tableName);
        echo "\n";

        // Réactiver les contraintes de clé étrangère
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo 'Error: ' . $e->getMessage();
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    echo 'Error: ' . $e->getMessage();
}

