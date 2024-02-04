<?php

namespace src\controller;

class DashboardController extends BaseController
{
    public function index()
    {
        $this->twig->display('admin/dashboard.html.twig');
    }
}
