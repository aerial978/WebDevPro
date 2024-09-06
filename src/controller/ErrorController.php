<?php

namespace src\controller;

class ErrorController extends BaseController
{
    public function notFound()
    {
        http_response_code(404);
        echo $this->twig->render('errors/404page.html.twig');
        exit();
    }

    public function serverError()
    {
        http_response_code(500);
        echo $this->twig->render('errors/500page.html.twig');
        exit();
    }
}
