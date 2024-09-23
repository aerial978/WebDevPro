<?php

namespace src\Models;

use src\Models\Model;
use DateTime;
use HTMLPurifier;
use HTMLPurifier_Config;

class PostModel extends Model
{
    protected $id;
    protected $title;
    protected $post_content;
    protected $slug_post;
    protected $category_id;
    protected $status_id;
    protected $post_image;
    protected $user_id;
    protected $created_at_post;
    protected $updated_at_post;

    public function __construct()
    {
        $this->table = "Post";
    }

    // PostBackController => index()
    public function findAllPost()
    {
        $sql = "SELECT *, DATE_FORMAT(created_at_post, '%d/%m/%Y %H:%i:%s') AS date_create, post.id AS postId FROM {$this->table}
                JOIN user ON post.user_id = user.id
                JOIN category ON post.category_id = category.id ORDER BY updated_at_post DESC";

        $query = $this->request($sql);

        return $query->fetchAll();
    }

    // PostFrontController => postSingle()
    public function singlePost($slug)
    {
        $req = "SELECT *, DATE_FORMAT(created_at_post, '%d/%m/%Y %H:%i:%s') AS date_create, 
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) AS total,
        GROUP_CONCAT(tag.name_tag SEPARATOR ', ') AS tag_name,
        GROUP_CONCAT(tag.slug_tag SEPARATOR ', ') AS tag_slug, 
        post.id AS postId 
        FROM {$this->table}
        JOIN category ON post.category_id = category.id
        JOIN user ON post.user_id = user.id
        JOIN post_tag ON post.id = post_tag.post_id
        JOIN tag ON post_tag.tag_id = tag.id
        WHERE slug_post = :slugPost
        GROUP BY post.id";

        $query = $this->request($req, [':slugPost' => $slug]);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    // PostBackController => create()
    public function createPost()
    {
        $sql = "INSERT INTO {$this->table} (title, post_content, slug_post, category_id, status_id, post_image, user_id, created_at_post)
        VALUES (:title, :postContent, :slugPost, :category, :status_id, :postImage, :user_id, NOW())";

        $attributs = [
            ':title' => $this->title,
            ':postContent' => $this->post_content,
            ':slugPost' => $this->slug_post,
            ':category' => $this->category_id,
            ':status_id' => $this->status_id,
            ':postImage' => $this->post_image,
            ':user_id' => $this->user_id,
        ];

        $query = $this->request($sql, $attributs);

        return $query;
    }

    // ??????????????????
    // Assainir le contenu HTML de postContent (CkEditor) pour prévenir les attaques XSS avec HTMLPurifier
    public function sanitizeContent($content)
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($content);
    }

    // PostBackController => create()
    public function getLastInsertedPostId()
    {
        $sql = "SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1";
        $query = $this->request($sql);
        $result = $query->fetch();

        return ($result) ? $result->id : null;
    }

    // HomeController => index() & PostFrontController => postList() & singlePost()
    public function getPosts($offset = 0, $limit = 10)
    {
        $sql = "SELECT *, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update, (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) AS total, post.id AS postId 
                FROM {$this->table}  
                JOIN category ON post.category_id = category.id
                JOIN user ON post.user_id = user.id
                GROUP BY post.id ORDER BY date_update ASC
                LIMIT $offset, $limit";

        $query = $this->request($sql);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    // PostFrontController => PostList()
    public function countPosts()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $query = $this->request($sql);

        return $query->fetchColumn();
    }

    // PostFrontController => PostList()
    public function timeElapsedString($datetime, $full = false)
    {
        if (is_null($datetime)) {
            return 'unknown';
        }

        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if ($diff->d >= 7) {
            $weeks = floor($diff->d / 7);
            $string['w'] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    // PostFrontController => PostList()
    public function searchPosts($keyword, $offset = 0, $limit = 10)
    {
        $keyword = $_GET['search'];

        $sql = "SELECT *, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update, post.id AS postId
                FROM {$this->table}
                LEFT JOIN user ON post.user_id = user.id
                LEFT JOIN category ON post.category_id = category.id
                WHERE post.title LIKE '%$keyword%'
                   OR post.post_content LIKE '%$keyword%'
                GROUP BY post.id
                ORDER BY date_update ASC LIMIT $offset, $limit";

        $query = $this->request($sql);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    // PostFrontController => PostList()
    public function countSearchPosts($search)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE title LIKE :search OR post_content LIKE :search";
        $query = $this->request($sql, ['search' => '%' . $search . '%']);
        return $query->fetchColumn();
    }

    // PostFrontController => singlePost()
    public function getNextPostId($currentPostId)
    {
        $sql = "SELECT slug_post FROM {$this->table} WHERE id > ? ORDER BY id ASC LIMIT 1";
        $query = $this->request($sql, [$currentPostId]);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['slug_post'] : null;
    }

    // PostFrontController => singlePost()
    public function getPreviousPostId($currentPostId)
    {
        $sql = "SELECT slug_post FROM {$this->table} WHERE id < ? ORDER BY id DESC LIMIT 1";
        $query = $this->request($sql, [$currentPostId]);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['slug_post'] : null;
    }

    // PostFrontController => singlePost()
    public function incrementViewCount($id)
    {
        $sql = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = :id";
        $this->request($sql, [':id' => $id]);
    }

    // PostFrontController => postList() & singlePost()
    public function getMostViewedPosts($limit = 3)
    {
        $sql = "SELECT *, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update, post.id AS postId 
            FROM {$this->table}
            ORDER BY view_count DESC
            LIMIT $limit";

        $query = $this->request($sql);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPostsByCategory($categoryId, $offset, $limit)
    {
        $sql = "SELECT *, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update,
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) AS total, 
        post.id AS postId
        FROM {$this->table}
        JOIN user ON post.user_id = user.id
        JOIN category ON post.category_id = category.id
        WHERE category_id = :categoryId
        LIMIT $offset, $limit";
        $query = $this->request($sql, [':categoryId' => $categoryId]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPostsByCategory($categoryId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE category_id = :categoryId";
        $query = $this->request($sql, [':categoryId' => $categoryId]);

        return $query->fetchColumn();
    }

    public function getPostsByTag($tagId, $offset, $limit)
    {
        $sql = "SELECT p.*, u.username, u.profile_picture, c.name_category, c.slug_category, u.slug_username, 
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = p.id) AS total 
        FROM {$this->table} p
        JOIN post_tag pt ON p.id = pt.post_id
        JOIN user u ON p.user_id = u.id
        JOIN category c ON p.category_id = c.id
        JOIN tag t ON t.id = pt.tag_id
        WHERE pt.tag_id = :tagId
        LIMIT $offset, $limit";
        $query = $this->request($sql, [':tagId' => $tagId]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPostsByTag($tagId)
    {
        $sql = "SELECT COUNT(*) 
                FROM post p
                JOIN post_tag pt ON p.id = pt.post_id
                WHERE pt.tag_id = :tagId";
        $query = $this->request($sql, [':tagId' => $tagId]);

        return $query->fetchColumn();
    }

    public function getPostsByUser($userId, $offset, $limit)
    {
        $sql = "SELECT *, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update, 
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) AS total, 
        post.id AS postId
        FROM {$this->table}
        JOIN user ON post.user_id = user.id
        JOIN category ON post.category_id = category.id
        WHERE user_id = :userId
        LIMIT $offset, $limit";
        $query = $this->request($sql, [':userId' => $userId]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPostsByUser($userId)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :userId";
        $query = $this->request($sql, [':userId' => $userId]);

        return $query->fetchColumn();
    }

    public function getPostsByArchive($startDate, $endDate, $offset, $limit)
    {
        $sql = "SELECT p.*, DATE_FORMAT(updated_at_post, '%d/%m') AS date_update, 
        u.username, u.profile_picture, c.name_category, c.slug_category, u.slug_username, 
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = p.id) AS total,
        p.id AS postId
        FROM {$this->table} p
        JOIN user u ON p.user_id = u.id
        JOIN category c ON p.category_id = c.id
        WHERE created_at_post BETWEEN :startDate AND :endDate 
        ORDER BY created_at_post DESC
        LIMIT $offset, $limit";

        $query = $this->request($sql, [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ]);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getArchives()
    {
        $sql = "SELECT YEAR(created_at_post) as year, MONTH(created_at_post) as month, COUNT(*) as post_count
        FROM {$this->table}
        GROUP BY year, month
        ORDER BY year DESC, month DESC";

        $query = $this->request($sql);

        $archives = $query->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($archives as &$archive) {
            $dateObj = DateTime::createFromFormat('!m', $archive['month']); // Create a date object from the month number
            $archive['month_name'] = $dateObj->format('F'); // Get the month name (e.g., "January")
        }

        return $archives;
    }

    public function countPostsByArchive($startDate, $endDate)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE created_at_post BETWEEN :startDate AND :endDate";

        $query = $this->request($sql, [
            ':startDate' => $startDate,
            ':endDate' => $endDate
        ]);

        return $query->fetchColumn();
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
     * Get the value of slug
     */
    public function getSlugPost()
    {
        return $this->slug_post;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */
    public function setSlugPost($slug_post)
    {
        $this->slug_post = $slug_post;

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
    public function getStatus_id()
    {
        return $this->status_id;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus_id($status_id)
    {
        $this->status_id = $status_id;

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
