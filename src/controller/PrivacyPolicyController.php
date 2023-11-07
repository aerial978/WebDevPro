<?php

namespace src\controller;

class PrivacyPolicyController extends BaseController
{
    public function index()
    {
        echo $this->twig->render('front/privacy-policy.html.twig');
    }
}