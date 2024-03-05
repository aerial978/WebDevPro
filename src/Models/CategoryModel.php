<?php

namespace src\Models;

use src\Models\Model;

class CategoryModel extends Model
{
    protected $id;
    protected $name_category;
    protected $description_category;
    protected $created_at_category;
    protected $updated_at_category;

    public function __construct()
    {
        $this->table = "Category";
    }

    public function findAllCount()
    {
        $sql = "SELECT *, DATE_FORMAT(created_at_category, '%d/%m/%Y %H:%i:%s') AS date_create, COUNT(post.id) AS post_count, category.id AS categoryId FROM category
        LEFT JOIN post ON category.id = post.category_id
        GROUP BY category.id";

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
    public function getNameCategory()
    {
        return $this->name_category;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setNameCategory($name_category)
    {
        $this->name_category = $name_category;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getDescriptionCategory()
    {
        return $this->description_category;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setDescriptionCategory($description_category)
    {
        $this->description_category = $description_category;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getCreatedAtCategory()
    {
        return $this->created_at_category;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setCreatedAtCategory()
    {
        $this->created_at_category = date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Get the value of update
     */
    public function getUpdated_at_category()
    {
        return $this->updated_at_category;
    }

    /**
     * Set the value of update
     *
     * @return  self
     */
    public function setUpdated_at_category()
    {
        $this->updated_at_category = date('Y-m-d H:i:s');

        return $this;
    }

}
