<?php

namespace src\Models;

use src\Models\Model;

class TagModel extends Model
{
    protected $id;
    protected $tag_name;

    public function __construct()
    {
        $this->table = "Tag";
    }

    public function findAllTag()
    {
        $sql = "SELECT tag_name, COUNT(post_tag.post_id) AS post_count, tag.id AS tagId FROM {$this->table}
        LEFT JOIN post_tag ON tag.id = post_tag.tag_id
        GROUP BY tag_name ORDER BY tag_name ASC";

        $query = $this->request($sql);

        return $query->fetchAll();
    }

    public function getLastInsertedTagId()
    {
        $sql = "SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1";
        $query = $this->request($sql);
        $result = $query->fetch();

        return ($result) ? $result->id : null;
    }

    public function getTagsForPost($postId)
    {
        $sql = "SELECT * FROM {$this->table} 
            INNER JOIN post_tag ON tag.id = post_tag.tag_id
            WHERE post_tag.post_id = :post_id 
        ";

        $params = [':post_id' => $postId];

        $query = $this->request($sql, $params);
        $tags = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $tags;
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
     * Get the value of tag_name
     */
    public function getTagName()
    {
        return $this->tag_name;
    }

    /**
     * Set the value of tag_name
     *
     * @return  self
     */
    public function setTagName($tag_name)
    {
        $this->tag_name = $tag_name;

        return $this;
    }
}
