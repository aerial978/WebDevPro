<?php

namespace src\Models;

use src\Models\Model;

class CommentModel extends Model
{
    protected $id;
    protected $post_id;
    protected $user_id;
    protected $status_id;
    protected $comment_content;
    protected $created_at_comment;

    public function __construct()
    {
        $this->table = "Comment";
    }

    // PostFrontController => singlePost()
    public function getCommentByPost($postId, $offset, $limit)
    {
        $offset = intval($offset);
        $limit = intval($limit);

        $sql = "SELECT *, DATE_FORMAT(created_at_comment, '%d/%m %H:%i:%s') AS date_create FROM {$this->table} 
        JOIN user ON comment.user_id = user.id 
        WHERE post_id = :postId AND status_id = 1 ORDER BY date_create DESC LIMIT $offset, $limit";
        $query = $this->request($sql, [':postId' => $postId]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    // PostFrontController => singlePost()
    public function countCommentsByPost($postId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE post_id = :postId AND status_id = 1";
        $query = $this->request($sql, [':postId' => $postId]);

        return $query->fetchColumn();
    }

    public function getLatestComments($limit = 5)
    {
        $sql = "SELECT c.comment_content, c.post_id, u.username, p.title AS post_title
                FROM {$this->table} c
                JOIN user u ON c.user_id = u.id
                JOIN post p ON c.post_id = p.id
                ORDER BY c.created_at_comment ASC
                LIMIT $limit";

        $query = $this->request($sql);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insertComment($commentData)
    {
        $sql = "INSERT INTO {$this->table} (post_id, user_id, status_id, comment_content, created_at_comment)
                VALUES (:post_id, :user_id, :status_id, :comment_content, NOW())";

        $attributs = [
            ':post_id' => $commentData['post_id'],
            ':user_id' => $commentData['user_id'],
            ':status_id' => $commentData['status_id'],
            ':comment_content' => $commentData['comment_content'],
        ];

        $query = $this->request($sql, $attributs);

        return $query;
    }

    public function findAllComment($offset, $limit, $sortColumn = null, $sortOrder = 'desc')
    {
        // Liste des colonnes autorisées et leurs équivalents dans la base de données pour éviter les injections SQL
        $allowedColumns = [
            'id' => 'comment.id',
            'comment_content' => 'comment.comment_content',
            'username' => 'user.username',
            'title' => 'post.title',
            'name_status' => 'status.name_status',
            'created_at_comment' => 'comment.created_at_comment'
        ];

        // Vérifie si la colonne à trier est autorisée, sinon utilise 'comment.id' comme colonne par défaut
        $sortColumn = isset($allowedColumns[$sortColumn]) ? $allowedColumns[$sortColumn] : 'comment.id';
        // Vérifie si l'ordre est 'asc' ou 'desc', sinon utilise 'desc' par défaut
        $sortOrder = ($sortOrder === 'asc' || $sortOrder === 'desc') ? $sortOrder : 'desc';

        $sql = "SELECT *, DATE_FORMAT(created_at_comment, '%d/%m/%Y %H:%i:%s') AS date_create, comment.id AS commentId, post.id AS postId, status.id AS statusId
                FROM {$this->table}
                JOIN user ON comment.user_id = user.id
                JOIN post ON comment.post_id = post.id 
                JOIN status ON comment.status_id = status.id
                ORDER BY $sortColumn $sortOrder 
                LIMIT $offset, $limit";

        $query = $this->request($sql);

        return $query->fetchAll();
    }

    public function countComments()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $query = $this->request($sql);

        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function findAllByStatus(string $statusId, int $offset, int $limit)
    {
        // Concaténation des valeurs d'offset et de limit directement dans la requête
        $sql = "SELECT *, DATE_FORMAT(created_at_comment, '%d/%m/%Y %H:%i:%s') AS date_create, comment.id AS commentId, post.id AS postId, status.id AS statusId
        FROM {$this->table}
        JOIN user ON comment.user_id = user.id
        JOIN post ON comment.post_id = post.id 
        JOIN status ON comment.status_id = status.id
        WHERE status.id = :statusId 
        LIMIT $offset, $limit";

        $query = $this->request($sql, [
            'statusId' => $statusId
        ]);

        return $query->fetchAll();
    }

    public function countCommentsByStatus(string $status_id)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status_id = :status_id";
        $query = $this->request($sql, ['status_id' => $status_id]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function findAllByStatusWithSorting(int $statusId, int $offset, int $limit, string $sortColumn, string $sortOrder)
    {
        // Sécurité pour éviter les injections SQL - Assurez-vous que les colonnes autorisées sont spécifiées
        $allowedColumns = [
            'id' => 'comment.id',
            'comment_content' => 'comment.comment_content',
            'username' => 'user.username',
            'title' => 'post.title',
            'name_status' => 'status.name_status',
            'created_at_comment' => 'comment.created_at_comment'
        ];

        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'comment.id'; // Par défaut, tri sur l'id si la colonne spécifiée n'est pas dans la liste autorisée
        }

        // Sécurité pour s'assurer que le tri est soit ascendant soit descendant
        $sortOrder = ($sortOrder === 'asc' || $sortOrder === 'desc') ? $sortOrder : 'desc';

        // Construction de la requête avec tri et limitation des résultats
        // Notez ici que nous insérons `LIMIT` et `OFFSET` directement dans la requête
        $sql = "SELECT *, DATE_FORMAT(created_at_comment, '%d/%m/%Y %H:%i:%s') AS date_create, comment.id AS commentId, post.id AS postId, status.id AS statusId
        FROM {$this->table}
        JOIN user ON comment.user_id = user.id
        JOIN post ON comment.post_id = post.id 
        JOIN status ON comment.status_id = status.id
        WHERE status.id = :statusId
        ORDER BY $sortColumn $sortOrder
        LIMIT $offset, $limit";

        // Exécution de la requête
        $query = $this->request($sql, [
            'statusId' => $statusId
        ]);

        return $query->fetchAll();
    }

    public function findCommentById($commentId)
    {
        $sql = "SELECT *, DATE_FORMAT(created_at_comment, '%d/%m/%Y %H:%i:%s') AS date_create, comment.id AS commentId, status.id AS statusId
                FROM {$this->table}
                JOIN user ON comment.user_id = user.id
                JOIN post ON comment.post_id = post.id 
                JOIN status ON comment.status_id = status.id
                WHERE comment.id = :commentId";

        $query = $this->request($sql, ['commentId' => $commentId]);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateComment(int $commentId, string $comment_content, string $status_id): void
    {
        $sql = "UPDATE {$this->table} SET comment_content = :comment_content, status_id = :status_id WHERE id = :commentId";
        $this->request($sql, [
            'comment_content' => $comment_content,
            'status_id' => $status_id,
            'commentId' => $commentId
        ]);
    }

    public function findHistoryCommentById($commentId)
    {
        $sql = "SELECT *, 
                    DATE_FORMAT(comment.created_at_comment, '%d/%m/%Y %H:%i:%s') AS dateCreateComment,
                    DATE_FORMAT(post.created_at_post, '%d/%m/%Y %H:%i:%s') AS dateCreatePost,
                    comment.id AS commentUniqueId,
                    comment.comment_content AS commentContent,
                    user.id AS commentAuthorUserId,
                    user.username AS commentAuthorUsername, 
                    user.email AS commentAuthorEmail, 
                    user.profile_picture AS commentAuthorProfilePicture,
                    role.name_role AS commentAuthorRole,
                    post.id AS postId,
                    post.title AS postTitle,
                    post_user.id AS postAuthorUserId,
                    post_user.username AS postAuthorUsername, 
                    post_user.email AS postAuthorEmail,
                    post_user.profile_picture AS postAuthorProfilePicture,
                    post_role.name_role AS postAuthorRole,
                    status.name_status AS commentStatus,
                    comment.status_id AS commentStatusId
                FROM {$this->table}
                JOIN user ON comment.user_id = user.id
                JOIN role ON user.role_id = role.id
                JOIN post ON comment.post_id = post.id
                JOIN user AS post_user ON post.user_id = post_user.id
                JOIN role AS post_role ON post_user.role_id = post_role.id
                JOIN status ON comment.status_id = status.id
                WHERE comment.id = :commentId
                GROUP BY comment.id";

        $query = $this->request($sql, ['commentId' => $commentId]);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
        * Get the value of id
        */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of comment_content
     */
    public function getcommentContent()
    {
        return $this->comment_content;
    }

    /**
     * Set the value of comment_content
     *
     * @return  self
     */
    public function setCommentContent($comment_content)
    {
        $this->comment_content = $comment_content;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getCreated_at_comment()
    {
        return $this->created_at_comment;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setCreated_at_comment()
    {
        $this->created_at_comment = date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Get the value of post_id
     */
    public function getPost_id(): int
    {
        return $this->post_id;
    }

    /**
     * Set the value of post_id
     *
     * @return  self
     */
    public function setPost_id(int $post_id)
    {
        $this->post_id = $post_id;

        return $this;
    }

    /**
     * Get the value of user_id
     */
    public function getUser_id(): int
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */
    public function setUser_id(int $user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of status_id
     */
    public function getStatus_id(): int
    {
        return $this->status_id;
    }

    /**
     * Set the value of status_id
     *
     * @return  self
     */
    public function setStatus_id(int $status_id)
    {
        $this->status_id = $status_id;

        return $this;
    }

}
