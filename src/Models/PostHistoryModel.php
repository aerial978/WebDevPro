<?php

namespace src\Models;

use src\Models\Model;

class PostHistoryModel extends Model
{
    protected $id;
    protected $post_id;
    protected $user_id;
    protected $status_id;
    protected $moderated_by_post;
    protected $moderation_reason_id;
    protected $revision_details;
    protected $created_at_moderation_post;

    public function __construct()
    {
        $this->table = "post_history";
    }

    public function addHistoryPost()
    {
        $sql = "INSERT INTO {$this->table} (post_id, user_id, status_id, moderate_by, moderation_reason_id, revision_details, created_at_moderation_post)
                VALUES (:statusId, :postStatus, :moderatedBy, :moderationReasonId, :revisionDetails, NOW())";

        $attributs = [
            ':postId' => $this->post_id,
            ':userId' => $this->user_id,
            ':postStatus' => $this->status_id,
            ':moderatedBy' => $this->moderated_by_post,
            ':moderationReasonId' => $this->moderation_reason_id ?? null,
            ':revisionDetails' =>  $this->revision_details ??  null,
            ':created_at_moderation' => $this->created_at_moderation_post
        ];

        $query = $this->request($sql, $attributs);

        return $query;
    }

    public function findModerationHistoryByPostId($postId)
    {
        $sql = "SELECT 
                    post_history.id AS historyUniqueId,
                    post_history.post_id AS historyPostId,
                    post_history.user_id AS historyUserId,
                    post_history.moderated_by_post AS moderatedByUserId,
                    post_history.status_id AS historyStatusId,
                    post_history.moderation_reason_id AS historyReasonId,
                    post_history.revision_details AS historyRevisionDetails,
                    post_history.created_at_moderation_post AS moderationActionDate,
                    status.name_status AS moderationStatusName,
                    moderation_reason.refusal_reason AS moderationReasonName,
                    user.username AS moderatorUsername,
                    user.id AS moderatorUserId
                FROM {$this->table}
                JOIN status ON post_history.status_id = status.id
                LEFT JOIN moderation_reason ON post_history.moderation_reason_id = moderation_reason.id
                JOIN user ON post_history.moderated_by_post = user.id
                WHERE post_history.post_id = :postId
                ORDER BY post_history.created_at_moderation_post DESC";

        $query = $this->request($sql, ['postId' => $postId]);

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
     * Get the value of moderated_by_post
     */
    public function getModeratedByPost()
    {
        return $this->moderated_by_post;
    }

    /**
     * Set the value of moderated_by_post
     *
     * @return  self
     */
    public function setModeratedByPost($moderated_by_post)
    {
        $this->moderated_by_post = $moderated_by_post;

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
     * Get the value of revision_details
     */
    public function getRevisionDetails()
    {
        return $this->revision_details;
    }

    /**
     * Set the value of revision_details
     *
     * @return  self
     */
    public function setRevisionDetails($revision_details)
    {
        $this->revision_details = $revision_details;

        return $this;
    }

    /**
     * Get the value of created_at_moderation_post
     */
    public function getCreatedAtModerationPost()
    {
        return $this->created_at_moderation_post;
    }

    /**
     * Set the value of created_at_moderation_post
     *
     * @return  self
     */
    public function setCreatedAtModerationPost($created_at_moderation_post)
    {
        $this->created_at_moderation_post = $created_at_moderation_post;

        return $this;
    }


}
