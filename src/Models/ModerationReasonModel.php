<?php

namespace src\Models;

use src\Models\Model;

class ModerationReasonModel extends Model
{
    protected $id;
    protected $refusal_reason;

    public function __construct()
    {
        $this->table = 'Moderation_reason';
    }

    public function getAllReasons()
    {
        $sql = "SELECT * FROM {$this->table}";
        $query = $this->request($sql);

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
     * Get the value of refusal_reason
     */
    public function getRefusalReason()
    {
        return $this->refusal_reason;
    }

    /**
     * Set the value of refusal_reason
     *
     * @return  self
     */
    public function setRefusalReason($refusal_reason)
    {
        $this->refusal_reason = $refusal_reason;

        return $this;
    }
}
