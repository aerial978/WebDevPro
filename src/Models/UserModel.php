<?php

namespace src\Models;

use src\Models\Model;
use src\Session\SessionManager;

class UserModel extends Model
{
    protected $id;
    protected $email;
    protected $password;
    protected $roles;

    public function __construct()
    {
        $this->table = "user";
    }

    /**
     * Récupèrer un user à partir de son e-mail
     * @param string $email
     * @return void
     */
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
    public function setSession()
    {
        // Récupérer l'e-mail de l'utilisateur
        $userEmail = $this->email;

        // Diviser l'e-mail pour obtenir la partie avant le "@"
        $parts = explode('@', $userEmail);
        $username = $parts[0]; // Ceci contiendra la partie avant le "@" de l'e-mail

        // Enregistrer l'ID de l'utilisateur et le nom d'utilisateur dans la session
        $sessionManager = new SessionManager();
        $sessionManager->set('user', [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $username
        ]);
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
