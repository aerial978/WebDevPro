<?php

namespace src\Models;

use src\Models\Model;
use src\Session\SessionManager;

class UserModel extends Model
{
    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $roles;
    protected $profile_picture;

    public function __construct()
    {
        $this->table = "user";
    }

    /**
     * Récupèrer un user à partir de son username
     * @param string $username
     * @return void
     */
    // SecurityController => handleRegistration()
    public function findOneByUsername(string $username)
    {
        $query = $this->request("SELECT * FROM {$this->table} WHERE username = ?", [$username]);

        return $query->fetch();
    }


    /**
     * Récupèrer un user à partir de son e-mail
     * @param string $email
     * @return void
     */
    // SecurityController => handleRegistration()
    public function findOneByEmail(string $email)
    {
        $query = $this->request("SELECT * FROM {$this->table} WHERE email = ?", [$email]);

        return $query->fetch();
    }

    /**
     * Crée la session de l'utilisateur
     *
     * @return void
     */
    // SecurityController => handleLogin()
    public function setSession()
    {
        // Enregistrer l'ID de l'utilisateur et le username dans la session
        $sessionManager = new SessionManager();
        $sessionManager->set('user', [
            'id' => $this->id,
            'username' => $this->username,
            'profile_picture' => $this->profile_picture
        ]);
    }

    public function getUserBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug_username = :slugUsername";
        $query = $this->request($sql, [':slugUsername' => $slug]);

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
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of profile_picture
     */
    public function getProfile_picture()
    {
        return $this->profile_picture;
    }

    /**
     * Set the value of profile_picture
     *
     * @return  self
     */
    public function setProfile_picture($profile_picture)
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }



    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */
    public function setRoles($roles)
    {
        $this->roles = json_decode($roles);

        return $this;
    }
}
