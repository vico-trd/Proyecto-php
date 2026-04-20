<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home');
    }
}
