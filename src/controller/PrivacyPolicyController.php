<?php

namespace src\controller;

class PrivacyPolicyController extends BaseController
{
    public function index()
    {
        $this->twig->display('frontend/privacy_policy.html.twig');
    }
}
