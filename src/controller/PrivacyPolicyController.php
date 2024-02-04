<?php

namespace src\controller;

class PrivacyPolicyController extends BaseController
{
    public function index()
    {
        echo $this->twig->render('frontend/privacy-policy.html.twig');
    }
}
