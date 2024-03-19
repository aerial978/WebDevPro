<?php

namespace src\Models;

use src\Models\Model;

class PostTagModel extends Model
{
    protected $id;
    protected $post_id;
    protected $tag_id;

    public function __construct()
    {
        $this->table = "Post_tag";
    }

    public function addTagsToPost($postId, $tagIds)
    {
        $results = []; // Un tableau pour stocker les résultats de chaque insertion

        foreach ($tagIds as $tagId) {
            $sql = "INSERT INTO post_tag (post_id, tag_id) VALUES (:post_id, :tag_id)";
            $query = $this->request($sql, [':post_id' => $postId, ':tag_id' => $tagId]);

            // Ajouter le résultat de l'insertion au tableau des résultats
            $results[] = $query->rowCount();
        }

        return $results; // Retourner le tableau des résultats
    }

    public function removeTagsFromPost($postId, $tagIds): void
    {
        $results = []; // Un tableau pour stocker les résultats de chaque insertion

        foreach ($tagIds as $tagId) {
            $sql = "DELETE FROM post_tag WHERE post_id = :post_id AND tag_id = :tag_id";
            $query = $this->request($sql, [':post_id' => $postId, ':tag_id' => $tagId]);

            $results[] = $query->rowCount();
        }
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
     * Get the value of tag_id
     */
    public function getTag_id(): int
    {
        return $this->tag_id;
    }

    /**
     * Set the value of tag_id
     *
     * @return  self
     */
    public function setTag_id(int $tag_id)
    {
        $this->tag_id = $tag_id;

        return $this;
    }
}
