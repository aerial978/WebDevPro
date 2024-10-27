<?php

namespace src\Models;

use src\Models\Model;

class CommentHistoryModel extends Model
{
    protected $id;
    protected $user_id;
    protected $comment_id;
    protected $post_id;
    protected $status_id;
    protected $moderated_by_comment;
    protected $moderation_reason_id;
    protected $refusal_details;
    protected $created_at_moderation_comment;

    public function __construct()
    {
        $this->table = "comment_history";
    }

    public function addHistoryPost()
    {
        $sql = "INSERT INTO {$this->table} (comment_id, user_id, post_id, status_id, moderate_by_comment, moderation_reason_id, refusal_details, created_at_moderation_comment)
                VALUES (:commentId, :commentStatus, :moderatedByComment, :moderationReasonId, :refusalDetails, NOW())";

        $attributs = [
            ':commentId' => $this->comment_id,
            ':userId' => $this->user_id,
            ':postId' => $this->post_id,
            ':commentStatus' => $this->status_id,
            ':moderatedByComment' => $this->moderated_by_comment,
            ':moderationReasonId' => $this->moderation_reason_id ?? null,
            ':refusalDetails' =>  $this->refusal_details ??  null,
            ':created_at_moderation' => $this->created_at_moderation_comment
        ];

        $query = $this->request($sql, $attributs);

        return $query;
    }

    public function findModerationHistoryByCommentId($commentId)
    {
        $sql = "SELECT 
                    comment_history.id AS historyUniqueId,
                    comment_history.post_id AS historyPostId,
                    comment_history.user_id AS historyUserId,
                    comment_history.moderated_by_comment AS moderatedByUserId,
                    comment_history.status_id AS historyStatusId,
                    comment_history.moderation_reason_id AS historyReasonId,
                    comment_history.refusal_details AS historyRefusalDetails,
                    comment_history.created_at_moderation_comment AS moderationActionDate,
                    status.name_status AS moderationStatusName,
                    moderation_reason.refusal_reason AS moderationReasonName,
                    user.username AS moderatorUsername,
                    user.id AS moderatorUserId
                FROM {$this->table}
                JOIN status ON comment_history.status_id = status.id
                LEFT JOIN moderation_reason ON comment_history.moderation_reason_id = moderation_reason.id
                JOIN user ON comment_history.moderated_by_comment = user.id
                WHERE comment_history.comment_id = :commentId
                ORDER BY comment_history.created_at_moderation_comment DESC";

        $query = $this->request($sql, ['commentId' => $commentId]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
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
     * Get the value of user_id
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of comment_id
     */
    public function getcommentId()
    {
        return $this->comment_id;
    }

    /**
     * Set the value of comment_id
     *
     * @return  self
     */
    public function setCommentId($comment_id)
    {
        $this->comment_id = $comment_id;

        return $this;
    }

    /**
    * Get the value of post_id
    */
    public function getpostId()
    {
        return $this->post_id;
    }

    /**
     * Set the value of post_id
     *
     * @return  self
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;

        return $this;
    }

    /**
     * Get the value of status_id
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Set the value of status_id
     *
     * @return  self
     */
    public function setStatusId($status_id)
    {
        $this->status_id = $status_id;

        return $this;
    }

    /**
     * Get the value of moderated_by_comment
     */
    public function getModeratedByComment()
    {
        return $this->moderated_by_comment;
    }

    /**
     * Set the value of moderated_by_comment
     *
     * @return  self
     */
    public function setModeratedBycomment($moderated_by_comment)
    {
        $this->moderated_by_comment = $moderated_by_comment;

        return $this;
    }

    /**
     * Get the value of moderation_reason_id
     */
    public function getModerationReasonId()
    {
        return $this->moderation_reason_id;
    }

    /**
     * Set the value of moderation_reason_id
     *
     * @return  self
     */
    public function setModerationReasonId($moderation_reason_id)
    {
        $this->moderation_reason_id = $moderation_reason_id;

        return $this;
    }

    /**
     * Get the value of refusal_details
     */
    public function getRefusalDetails()
    {
        return $this->refusal_details;
    }

    /**
     * Set the value of refusal_details
     *
     * @return  self
     */
    public function setRefusalDetails($refusal_details)
    {
        $this->refusal_details = $refusal_details;

        return $this;
    }

    /**
     * Get the value of created_at_moderation_comment
     */
    public function getCreatedAtModerationComment()
    {
        return $this->created_at_moderation_comment;
    }

    /**
     * Set the value of created_at_moderation_comment
     *
     * @return  self
     */
    public function setCreatedAtModerationComment($created_at_moderation_comment)
    {
        $this->created_at_moderation_comment = $created_at_moderation_comment;

        return $this;
    }


}
