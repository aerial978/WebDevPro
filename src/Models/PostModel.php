<?php

namespace src\Models;

use src\Models\Model;

class PostModel extends Model
{
    protected $id;
    protected $title;
    protected $introduction;
    protected $post_content;
    protected $category_id;
    protected $post_status;
    protected $post_image;
    protected $user_id;
    protected $created_at_post;
    protected $updated_at_post;

    public function __construct()
    {
        $this->table = "Post";
    }

    public function findAll()
    {
        $sql = "SELECT *, DATE_FORMAT(created_at_post, '%d/%m/%Y %H:%i:%s') AS date_create, post.id AS postId FROM {$this->table}
                JOIN user ON post.user_id = user.id
                JOIN category ON post.category_id = category.id";

        $query = $this->request($sql);

        return $query->fetchAll();
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
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of introduction
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set the value of introduction
     *
     * @return  self
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
    * Get the value of content
    */
    public function getPostContent()
    {
        return $this->post_content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */
    public function setPostContent($post_content)
    {
        $this->post_content = $post_content;

        return $this;
    }

    /**
     * Get the value of category_id
     */
    public function getCategory_id(): int
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @return  self
     */
    public function setCategory_id(int $category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getPostStatus()
    {
        return $this->post_status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setPostStatus($post_status)
    {
        $this->post_status = $post_status;

        return $this;
    }

    /**
    * Get the value of postImage
    */
    public function getPostImage()
    {
        return $this->post_image;
    }

    /**
     * Set the value of postImage
     *
     * @return  self
     */
    public function setPostImage($post_image)
    {
        $this->post_image = $post_image;

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
     * Get the value of date
     */
    public function getCreated_at_post()
    {
        return $this->created_at_post;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setCreated_at_post()
    {
        $this->created_at_post = date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Get the value of update
     */
    public function getUpdated_at_post()
    {
        return $this->updated_at_post;
    }

    /**
     * Set the value of update
     *
     * @return  self
     */
    public function setUpdated_at_post()
    {
        $this->updated_at_post = date('Y-m-d H:i:s');

        return $this;
    }
}
