<?php

namespace App\Controllers;

class ErrorController extends BaseController
{
    public function notFound(): void
    {
        header('HTTP/1.1 404 Not Found');
        $this->render('errors/404');
    }
}
