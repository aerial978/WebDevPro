<?php

namespace src\Models;

use src\Core\Db;

class Model extends Db
{
    protected $table;

    private $db;

    /**
     * Méthode qui exécutera les requêtes
     * @param string $sql Requête SQL à exécuter
     * @param array $attributes Attributs à ajouter à la requête
     * @return PDOStatement|false
     */
    public function request(string $sql, array $attributs = null)
    {
        $this->db = Db::getInstance();

        if($attributs !== null) {
            $query = $this->db->prepare($sql);
            $query->execute($attributs);
            return $query;
        } else {
            return $this->db->query($sql);
        }
    }

    /**
     * Sélection de tous les enregistrements d'une table
     * @return array Tableau des enregistrements trouvés
     */
    public function findAll()
    {
        $query = $this->request('SELECT * FROM '. $this->table);
        return $query->fetchAll();
    }

    /**
     * Sélection d'un enregistrement suivant son id
     * @param int $id id de l'enregistrement
     * @return array Tableau contenant l'enregistrement trouvé
     */
    public function find(int $id)
    {
        $req = "SELECT * FROM {$this->table} WHERE id = :id";
        $query = $this->request($req, [':id' => $id]);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Sélection de plusieurs enregistrements suivant un tableau de critères
     * @param array $criteres Tableau de critères
     * @return array Tableau des enregistrements trouvés
     */
    public function findBy(array $criteres)
    {
        $fields = [];
        $values = [];

        foreach($criteres as $field => $value) {
            $fields[] = "$field = ?";
            $values[] = $value;
        }

        $liste_champs = implode('AND', $fields);

        $query = $this->request("SELECT * FROM {$this->table} WHERE $liste_champs", $values);

        return $query->fetchAll();
    }

    /**
     * Méthode qui récupère l'ID du dernier enregistrement inséré.
     * @return string|null ID du dernier enregistrement inséré ou null en cas d'échec.
     */
    public function getLastInsertedId()
    {
        if ($this->db) {
            return $this->db->lastInsertId();
        }
        return null;
    }

    public function create()
    {
        $fields = [];
        $inter = [];
        $values = [];

        // $this correspond à l'objet Model lui-meme
        foreach($this as $field => $value) {
            if($value !== null && $field != 'db' && $field != 'table') {
                $fields[] = $field;
                $inter[] = "?";
                $values[] = $value;
            }
        }

        $liste_champs = implode(', ', $fields);
        $liste_inter = implode(', ', $inter);

        return $this->request('INSERT INTO '.$this->table.' ('.$liste_champs.') VALUES('.$liste_inter.')', $values);
    }

    /**
     * Mise à jour d'un enregistrement suivant un tableau de données

     * @return bool
     */
    public function update(int $id)
    {
        $fields = [];
        $values = [];

        foreach($this as $field => $value) {
            if($value !== null && $field != 'db' && $field != 'table') {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
        }
        $values[] = $id;

        $liste_champs = implode(', ', $fields);

        return $this->request('UPDATE '.$this->table.' SET '.$liste_champs.' WHERE id = ?', $values);
    }

    public function hydrate($donnees)
    {
        if (!is_array($donnees) && !is_object($donnees)) {

            return $this;
        }

        foreach($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);

            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Suppression d'un enregistrement
     * @param int $id id de l'enregistrement à supprimer
     * @return bool
     */
    public function delete(int $id)
    {
        return $this->request("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}
