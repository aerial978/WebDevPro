<?php

namespace src\Service;

use src\Models\StatusModel;

class StatusService
{
    private $statusModel;

    public function __construct()
    {
        $this->statusModel = new StatusModel();
    }

    public function getStatusOptions()
    {
        $status = $this->statusModel->getAllStatus();
        $statusOptions = [];

        foreach ($status as $statusItem) {
            $statusOptions[$statusItem->id] = $statusItem->name_status;
        }

        return $statusOptions;
    }
}
