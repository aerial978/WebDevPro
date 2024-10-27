<?php

namespace src\Controller;

use src\Models\CommentModel;
use src\Service\StatusService;
use src\Constants\ErrorMessage;
use src\Models\CommentHistoryModel;
use src\Models\ModerationReasonModel;

class CommentController extends BaseController
{
    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_SESSION['user'])) {
            $commentModel = new CommentModel();

            $commentData = [
                'post_id' => $_POST['post_id'],
                'user_id' => $_SESSION['user']['id'],
                'status_id' => 3,
                'comment_content' => trim($_POST['comment']),
            ];

            $commentModel->insertComment($commentData);

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            $errorController = new ErrorController();
            $errorController->serverError();
            exit();
        }
    }

    public function index()
    {
        $statusFilter = isset($_GET['status']) ? (int)$_GET['status'] : null; // Récupère le statut sélectionné

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Récupère la page actuelle

        $commentsModel = new CommentModel();
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $sortColumn = $_GET['sortColumn'] ?? null; // Colonne à trier
        $sortOrder = $_GET['sortOrder'] ?? 'desc'; // Ordre du tri (asc/desc)

        if ($statusFilter) {
            // Si un statut est sélectionné, récupérer uniquement les commentaires ayant ce statut
            if ($sortColumn) {
                $indexComments = $commentsModel->findAllByStatusWithSorting($statusFilter, $offset, $limit, $sortColumn, $sortOrder);
            } else {
                $indexComments = $commentsModel->findAllByStatus($statusFilter, $offset, $limit);
            }
            $totalComments = $commentsModel->countCommentsByStatus($statusFilter);
        } else {
            // Sinon, récupérer tous les commentaires
            if ($sortColumn) {
                $indexComments = $commentsModel->findAllComment($offset, $limit, $sortColumn, $sortOrder);
            } else {
                $indexComments = $commentsModel->findAllComment($offset, $limit);
            }
            $totalComments = $commentsModel->countComments();
        }

        $totalPages = ceil($totalComments / $limit);

        // Affichage de la vue
        $this->twig->display('admin/comments/index.html.twig', [
            'indexComments' => $indexComments,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
            'currentStatus' => $statusFilter,
        ]);
    }

    public function edit(int $commentId)
    {
        $errors = [];

        $commentsModel = new CommentModel();
        $comment = $commentsModel->findCommentById($commentId);

        $statusService = new StatusService();
        $statusOptions = $statusService->getStatusOptions();

        $moderationReasonModel = new ModerationReasonModel();
        $reasonsData = $moderationReasonModel->getAllReasons();

        $reasons = [];
        foreach ($reasonsData as $reason) {
            $reasons[$reason['id']] = $reason['refusal_reason'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentContent = $_POST['comment-content'];
            $commentStatus = $_POST['status-comment'] ?? null;
            $moderationReasonId = $_POST['moderation-reason'] ?? null;
            $refusalDetails = $_POST['detail-refus'] ?? null;

            if (empty($commentContent)) {
                $errors['comment-content'] = ErrorMessage::CONTENT_INVALID;
            }

            if ($commentStatus == '5' && empty($moderationReasonId)) {
                $errors['moderation-reason-id'] = ErrorMessage::REFUSAL_INVALID;
            }

            if ($commentStatus == '5' && $moderationReasonId == '6') {
                if (empty($refusalDetails)) {
                    $errors['refusal-reasons'] = ErrorMessage::SPECIFYREASON_INVALID;
                }
            }

            if (empty($errors)) {
                $commentsModel = new CommentModel();
                // 1. Mettre à jour le  commentaire dans la table `comment`
                $commentsModel->updateComment($commentId, $commentContent, $commentStatus);

                $commentHistoryModel = new CommentHistoryModel();

                $commentHistoryModel->setCommentId($commentId)

                ->setStatusId($commentStatus)
                ->setUserId($comment['user_id'])
                ->setPostId($comment['post_id'])
                ->setModeratedByComment($_SESSION['user']['id'])
                ->setModerationReasonId($moderationReasonId)
                ->setRefusalDetails(!empty($refusalDetails) ? $refusalDetails : null)
                ->setCreatedAtModerationComment(date('Y-m-d H:i:s'));

                $commentHistoryModel->create();

                // Rediriger l'utilisateur avec un message de succès
                header('Location: ../index');
                exit;
            }
        }

        $commentHistoryModel = new CommentHistoryModel();
        $moderationActions = $commentHistoryModel->findModerationHistoryByCommentId($commentId);

        $selectedValue = isset($moderationActions[0]['historyReasonId']) ? $moderationActions[0]['historyReasonId'] : null;

        $refusalDetails = isset($moderationActions[0]['historyRefusalDetails']) ? $moderationActions[0]['historyRefusalDetails'] : null;

        $this->twig->display('admin/comments/edit.html.twig', [
            'comment' => $comment,
            'statusOptions' => $statusOptions,
            'reasons' => $reasons,
            'errors' => $errors,
            'selectedValue' => $selectedValue,
            'refusalDetails' => $refusalDetails,
        ]);
    }

    public function historyComment($commentId)
    {
        $commentsModel = new CommentModel();
        $commentHistory = $commentsModel->findHistoryCommentById($commentId);

        $commentsHistoryModel = new CommentHistoryModel();
        $moderationActions = $commentsHistoryModel->findModerationHistoryByCommentId($commentId);

        $this->twig->display('admin/comments/history.html.twig', [
            'commentHistory' => $commentHistory,
            'moderationActions' => $moderationActions,
        ]);
    }
}
