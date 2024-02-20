<?php

namespace src\Models;

use src\Models\Model;

class PostModel extends Model
{
    protected $id;
    protected $title;
    protected $introduction;
    protected $postContent;
    protected $category;
    protected $postStatus;
    protected $postImage;
    protected $user_id;

    public function __construct()
    {
        $this->table = "Post";
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
        return $this->postContent;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */
    public function setPostContent($postContent)
    {
        $this->postContent = $postContent;

        return $this;
    }

    /**
     * Get the value of category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getPostStatus()
    {
        return $this->postStatus;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setPostStatus($postStatus)
    {
        $this->postStatus = $postStatus;

        return $this;
    }

    /**
    * Get the value of postImage
    */
    public function getPostImage()
    {
        return $this->postImage;
    }

    /**
     * Set the value of postImage
     *
     * @return  self
     */
    public function setPostImage($postImage)
    {
        $this->postImage = $postImage;

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
}
