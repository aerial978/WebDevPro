<?php

namespace src\Controller;

use src\Models\CommentModel;

class CommentController extends BaseController
{
    public function addComment()
    {
        // Vérifie si la requête est une requête POST et si l'utilisateur est connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_SESSION['user'])) {
            $commentModel = new CommentModel();

            // Préparation des données du commentaire à insérer
            $commentData = [
                'post_id' => $_POST['post_id'], // On récupère le post_id du formulaire
                'user_id' => $_SESSION['user']['id'], // ID de l'utilisateur connecté
                'status_id' => 3, // Statut par défaut (par exemple, 1 pour "approuvé")
                'comment_content' => trim($_POST['comment']), // Contenu du commentaire
            ];

            // Insertion du commentaire dans la base de données
            $commentModel->insertComment($commentData);

            // Redirection vers la page du post une fois le commentaire ajouté
            header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirige vers la page précédente
            exit();
        } else {
            $errorController = new ErrorController();
            $errorController->serverError();
            exit();
        }
    }
}
