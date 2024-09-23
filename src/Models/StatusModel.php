<?php

namespace src\Models;

use src\Models\Model;

class StatusModel extends Model
{
    protected $id;
    protected $name_status;
    protected $description_status;

    public function __construct()
    {
        $this->table = "Status";
    }

    public function getAllStatus()
    {
        $sql = "SELECT id, name_status FROM {$this->table}";
        $query = $this->request($sql);

        return $query->fetchAll();
    }
}
