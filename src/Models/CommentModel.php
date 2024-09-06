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

    /*public function getCommentByPost($postId)
    {
        $sql = "SELECT * FROM {$this->table}
            LEFT JOIN post ON comment.post_id = post.id
            WHERE post_id = ?";
        $query = $this->request($sql, [$postId]);

        return $query->fetchAll();
    }*/

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
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE post_id = :postId";
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
