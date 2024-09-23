<?php

namespace src\Models;

use src\Models\Model;

class CategoryModel extends Model
{
    protected $id;
    protected $name_category;
    protected $description_category;

    public function __construct()
    {
        $this->table = "Category";
    }

    // PostFrontController => PostList() & singlePost()
    public function findAllCategory()
    {
        $sql = "SELECT *, COUNT(post.id) AS post_count, category.id AS categoryId FROM {$this->table}
        LEFT JOIN post ON category.id = post.category_id
        GROUP BY category.id";

        $query = $this->request($sql);

        return $query->fetchAll();
    }

    // CategoryController => Create()
    public function createCategory()
    {
        $sql = "INSERT INTO {$this->table} (name_category, description_category)
        VALUES (:nameCategory, :descriptionCategory)";

        $attributs = [
            ':nameCategory' => $this->name_category,
            ':descriptionCategory' => $this->description_category,
        ];

        $query = $this->request($sql, $attributs);

        return $query;
    }

    public function getCategoryBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug_category = :slugCategory";
        $query = $this->request($sql, [':slugCategory' => $slug]);

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
}
